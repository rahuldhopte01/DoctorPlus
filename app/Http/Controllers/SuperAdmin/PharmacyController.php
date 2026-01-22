<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Country;
use App\Models\Pharmacy;
use App\Models\PharmacySettle;
use App\Models\PharmacyWorkingHour;
use App\Models\PurchaseMedicine;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class PharmacyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('pharmacy_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharamacies = Pharmacy::orderBy('id', 'DESC')->get();
        $center_coords = Setting::select('lat', 'lang')->first();

        return view('superAdmin.pharmacy.pharmacy', compact('pharamacies', 'center_coords'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('pharmacy_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::get();
        $currency = Setting::first()->currency_symbol;
        $setting = Setting::first();

        return view('superAdmin.pharmacy.create_pharmacy', compact('countries', 'currency', 'setting'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'bail|required',
            'phone' => 'bail|required|digits_between:6,12',
            'email' => 'bail|required|email|unique:users',
            'address' => 'bail|required',
        ]);
        $data = $request->all();
        $setting = Setting::first();
        
        // Generate temporary password
        $password = mt_rand(100000, 999999);
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'verify' => 1,
            'phone' => $data['phone'],
            'phone_code' => $data['phone_code'],
        ]);
        $user->assignRole('pharmacy');
        
        // Build clean pharmacy data array with default values
        $pharmacyData = [
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'postcode' => $data['postcode'] ?? null,
            'lat' => $data['lat'] ?? $setting->lat ?? '',
            'lang' => $data['lang'] ?? $setting->lang ?? '',
            'start_time' => strtolower('08:00 am'), // Default start time
            'end_time' => strtolower('08:00 pm'), // Default end time
            'description' => null,
            'commission_amount' => $setting->pharmacy_commission ?? 0, // Use default from settings
            'status' => 'approved', // Pre-approved
            'is_priority' => $request->has('is_priority') ? 1 : 0,
            'is_shipping' => 0, // Default to no shipping
            'image' => 'defaultUser.png',
            'delivery_charges' => null,
        ];
        
        $pharmacy = Pharmacy::create($pharmacyData);
        
        // Create default working hours
        $start_time = $pharmacy->start_time;
        $end_time = $pharmacy->end_time;
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        for ($i = 0; $i < count($days); $i++) {
            $master = [];
            $temp2['start_time'] = $start_time;
            $temp2['end_time'] = $end_time;
            array_push($master, $temp2);
            $work_time['pharmacy_id'] = $pharmacy->id;
            $work_time['period_list'] = json_encode($master);
            $work_time['day_index'] = $days[$i];
            $work_time['status'] = 1;
            PharmacyWorkingHour::create($work_time);
        }
        
        // Send approval email with temporary password
        $message = "Dear " . $data['name'] . ",\n\n";
        $message .= "Your pharmacy account has been approved and created successfully!\n\n";
        $message .= "Login Credentials:\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Temporary Password: " . $password . "\n\n";
        $message .= "Please login and change your password for security reasons.\n\n";
        $message .= "You can login at: " . url('pharmacy_login') . "\n\n";
        $message .= "Thank you!";
        
        try {
            $config = [
                'driver' => $setting->mail_mailer,
                'host' => $setting->mail_host,
                'port' => $setting->mail_port,
                'from' => ['address' => $setting->mail_from_address, 'name' => $setting->mail_from_name],
                'encryption' => $setting->mail_encryption,
                'username' => $setting->mail_username,
                'password' => $setting->mail_password,
            ];
            Config::set('mail', $config);
            Mail::to($user->email)->send(new SendMail($message, 'Pharmacy Account Approved'));
        } catch (\Exception $e) {
            info($e);
        }

        return redirect('pharmacy')->withStatus(__('Pharmacy created successfully and approval email sent..!!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pharmacy = Pharmacy::find($id);
        $medicines = PurchaseMedicine::with('user')->where('pharmacy_id', $pharmacy->id)->get();
        $currency = Setting::first()->currency_symbol;

        return view('superAdmin.pharmacy.show_pharmacy', compact('pharmacy', 'medicines', 'currency'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('pharmacy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::get();
        $pharmacy = Pharmacy::find($id);
        $currency = Setting::first()->currency_symbol;
        $pharmacy['start_time'] = Carbon::parse($pharmacy['start_time'])->format('H:i');
        $pharmacy['end_time'] = Carbon::parse($pharmacy['end_time'])->format('H:i');

        return view('superAdmin.pharmacy.edit_pharmacy', compact('countries', 'pharmacy', 'currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'bail|required',
            'start_time' => 'bail|required',
            'end_time' => 'bail|required|after:start_time',
            'address' => 'bail|required',
            'commission_amount' => 'bail|required',
            'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
        ],
            [
                'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
            ]);
        $pharmacy = Pharmacy::find($id);
        $data = $request->all();
        $data['is_shipping'] = $request->has('is_shipping') ? 1 : 0;
        $delivery = [];
        for ($i = 0; $i < count($data['min_value']); $i++) {
            $temp['min_value'] = $data['min_value'][$i];
            $temp['max_value'] = $data['max_value'][$i];
            $temp['charges'] = $data['charges'][$i];
            array_push($delivery, $temp);
        }
        $data['delivery_charges'] = json_encode($delivery);
        if ($request->hasFile('image')) {
            (new CustomController)->deleteFile($pharmacy->image);
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        $data['start_time'] = strtolower(Carbon::parse($data['start_time'])->format('h:i a'));
        $data['end_time'] = strtolower(Carbon::parse($data['end_time'])->format('h:i a'));
        $pharmacy->update($data);

        return redirect('pharmacy')->withStatus(__('Pharmacy updated successfully..!!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('pharmacy_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::find($id);
        (new CustomController)->deleteFile($pharmacy->image);
        $user = User::find($pharmacy->user_id);
        $user->removeRole('pharmacy');
        $user->delete();
        $pharmacy->delete();

        return response(['success' => true]);
    }

    public function change_status(Request $reqeust)
    {
        // Extended to support approval workflow: pending, approved, rejected
        // This method is kept for backward compatibility but status management is done via approve/reject
        $pharmacy = Pharmacy::find($reqeust->id);
        $data['status'] = $pharmacy->status == 'approved' ? 'pending' : 'approved';
        $pharmacy->update($data);

        return response(['success' => true]);
    }

    public function approve_pharmacy(Request $request)
    {
        abort_if(Gate::denies('pharmacy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::find($request->id);
        if ($pharmacy) {
            // If already approved, just return success
            if ($pharmacy->status !== 'approved') {
                $pharmacy->status = 'approved';
                $pharmacy->save();

                // Send approval mail with temporary password so pharmacy can login
                $setting = Setting::first();
                $user = User::find($pharmacy->user_id);

                if ($user && $setting) {
                    // Generate a new temporary password and update the user
                    //$password = mt_rand(100000, 999999);
                    //$user->password = Hash::make($password);
                    //$user->save();

                    $message = "Dear " . $pharmacy->name . ",\n\n";
                    $message .= "Your pharmacy account has been approved successfully!\n\n";
                    $message .= "Login Credentials:\n";
                    $message .= "Email: " . $pharmacy->email . "\n";
                    //$message .= "Temporary Password: " . $password . "\n\n";
                    $message .= "You can login at: " . url('pharmacy_login') . "\n\n";
                    $message .= "Thank you!";

                    try {
                        $config = [
                            'driver' => $setting->mail_mailer,
                            'host' => $setting->mail_host,
                            'port' => $setting->mail_port,
                            'from' => ['address' => $setting->mail_from_address, 'name' => $setting->mail_from_name],
                            'encryption' => $setting->mail_encryption,
                            'username' => $setting->mail_username,
                            'password' => $setting->mail_password,
                        ];
                        Config::set('mail', $config);
                        Mail::to($user->email)->send(new SendMail($message, 'Pharmacy Account Approved'));
                    } catch (\Exception $e) {
                        info($e);
                    }
                }
            }

            return response(['success' => true, 'message' => __('Pharmacy approved successfully')]);
        }
        return response(['success' => false, 'message' => __('Pharmacy not found')], 404);
    }

    public function reject_pharmacy(Request $request)
    {
        abort_if(Gate::denies('pharmacy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::find($request->id);
        if ($pharmacy) {
            $pharmacy->status = 'rejected';
            $pharmacy->save();
            return response(['success' => true, 'message' => __('Pharmacy rejected successfully')]);
        }
        return response(['success' => false, 'message' => __('Pharmacy not found')], 404);
    }

    public function toggle_priority(Request $request)
    {
        abort_if(Gate::denies('pharmacy_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pharmacy = Pharmacy::find($request->id);
        if ($pharmacy) {
            $pharmacy->is_priority = !$pharmacy->is_priority;
            $pharmacy->save();
            return response(['success' => true, 'is_priority' => $pharmacy->is_priority]);
        }
        return response(['success' => false, 'message' => __('Pharmacy not found')], 404);
    }

    public function pharmacy_commission($pharmacy_id)
    {
        $pharmacy = Pharmacy::find($pharmacy_id);
        $now = Carbon::today();
        $medicines = [];
        $currency = Setting::first()->currency_symbol;
        for ($i = 0; $i < 7; $i++) {
            $appointment = PurchaseMedicine::where('pharmacy_id', $pharmacy->id)->whereDate('created_at', $now)->get();
            $appointment['amount'] = $appointment->sum('amount');
            $appointment['admin_commission'] = $appointment->sum('admin_commission');
            $appointment['pharmacy_commission'] = $appointment->sum('pharmacy_commission');
            $now = $now->subDay();
            $appointment['date'] = $now->toDateString();
            array_push($medicines, $appointment);
        }

        $past = Carbon::now(env('timezone'))->subDays(35);
        $now = Carbon::today();
        $c = $now->diffInDays($past);
        $loop = $c / 10;
        $data = [];
        while ($now->greaterThan($past)) {
            $t = $past->copy();
            $t->addDay();
            $temp['start'] = $t->toDateString();
            $past->addDays(10);
            if ($past->greaterThan($now)) {
                $temp['end'] = $now->toDateString();
            } else {
                $temp['end'] = $past->toDateString();
            }
            array_push($data, $temp);
        }

        $settels = [];
        $orderIds = [];
        foreach ($data as $key) {
            $settle = PharmacySettle::where('pharmacy_id', $pharmacy->id)->where('created_at', '>=', $key['start'].' 00.00.00')->where('created_at', '<=', $key['end'].' 23.59.59')->get();
            $value['d_total_task'] = $settle->count();
            $value['admin_earning'] = $settle->sum('admin_amount');
            $value['pharmacy_earning'] = $settle->sum('pharmacy_amount');
            $value['d_total_amount'] = $value['admin_earning'] + $value['pharmacy_earning'];
            $remainingOnline = PharmacySettle::where([['pharmacy_id', $pharmacy->id], ['payment', 0], ['pharmacy_status', 0]])->where('created_at', '>=', $key['start'].' 00.00.00')->where('created_at', '<=', $key['end'].' 23.59.59')->get();
            $remainingOffline = PharmacySettle::where([['pharmacy_id', $pharmacy->id], ['payment', 1], ['pharmacy_status', 0]])->where('created_at', '>=', $key['start'].' 00.00.00')->where('created_at', '<=', $key['end'].' 23.59.59')->get();

            $online = $remainingOnline->sum('pharmacy_amount'); // admin e devana
            $offline = $remainingOffline->sum('admin_amount'); // admin e levana

            $value['duration'] = $key['start'].' - '.$key['end'];
            $value['d_balance'] = $offline - $online; // + hoy to levana - devana
            array_push($settels, $value);
        }

        return view('superAdmin.pharmacy.finance', compact('pharmacy', 'medicines', 'currency', 'settels'));
    }

    public function show_pharmacy_settalement(Request $request)
    {
        $duration = explode(' - ', $request->duration);
        $currency = Setting::first()->currency_symbol;
        $settle = PharmacySettle::where('created_at', '>=', $duration[0].' 00.00.00')->where('created_at', '<=', $duration[1].' 23.59.59')->get();
        foreach ($settle as $s) {
            $s->date = $s->created_at->toDateString();
        }

        return response(['success' => true, 'data' => $settle, 'currency' => $currency]);
    }

    public function pharmacy_schedule($pharmacy_id)
    {
        $pharmacy = Pharmacy::find($pharmacy_id);
        $pharmacy->workingHours = PharmacyWorkingHour::where('pharmacy_id', $pharmacy->id)->get();
        $pharmacy->firstHours = PharmacyWorkingHour::where('pharmacy_id', $pharmacy->id)->first();

        return view('superAdmin.pharmacy.schedule',compact('pharmacy'));
    }
}
