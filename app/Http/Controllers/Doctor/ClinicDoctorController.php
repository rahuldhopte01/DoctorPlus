<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\CustomController;
use App\Mail\SendMail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Doctor;
use App\Models\DoctorSubscription;
use App\Models\Expertise;
use App\Models\Hospital;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Treatments;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClinicDoctorController extends Controller
{
    /**
     * Get the current logged-in admin doctor.
     */
    private function getAdminDoctor()
    {
        $user = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->first();
        
        if (!$doctor || !$doctor->isAdminDoctor()) {
            abort(403, 'You must be a clinic admin to access this page.');
        }
        
        return $doctor;
    }

    /**
     * Display a listing of sub-doctors for the clinic.
     */
    public function index()
    {
        $adminDoctor = $this->getAdminDoctor();
        
        // Get all sub-doctors belonging to the same clinic
        $doctors = Doctor::with('expertise')
            ->where('hospital_id', $adminDoctor->hospital_id)
            ->where('doctor_role', 'SUB_DOCTOR')
            ->orderBy('id', 'desc')
            ->get();
            
        foreach ($doctors as $doctor) {
            $doctor->user = User::find($doctor->user_id);
        }
        
        $clinic = Hospital::find($adminDoctor->hospital_id);

        return view('doctor.clinic_doctors.index', compact('doctors', 'clinic', 'adminDoctor'));
    }

    /**
     * Show the form for creating a new sub-doctor.
     */
    public function create()
    {
        $adminDoctor = $this->getAdminDoctor();
        $countries = Country::get();
        $treatments = Treatments::whereStatus(1)->get();
        $treat = [];
        $categories = [];
        $expertieses = [];
        $treat = Treatments::whereStatus(1)->first();
        if ($treat) {
            $categories = Category::whereStatus(1)->where('treatment_id', $treat->id)->get();
            $cat = Category::whereStatus(1)->where('treatment_id', $treat->id)->first();
            if ($cat) {
                $expertieses = Expertise::whereStatus(1)->where('category_id', $cat->id)->get();
            }
        }
        $clinic = Hospital::find($adminDoctor->hospital_id);

        return view('doctor.clinic_doctors.create', compact('countries', 'treatments', 'categories', 'expertieses', 'clinic', 'adminDoctor'));
    }

    /**
     * Store a newly created sub-doctor.
     */
    public function store(Request $request)
    {
        $adminDoctor = $this->getAdminDoctor();
        
        $request->validate([
            'name' => 'bail|nullable|unique:doctor',
            'email' => 'bail|nullable|email|unique:users',
            'treatment_id' => 'bail|nullable|array',
            'treatment_id.*' => 'bail|nullable|exists:treatments,id',
            'category_id' => 'bail|nullable|array',
            'category_id.*' => 'bail|nullable|exists:category,id',
            'dob' => 'bail|nullable',
            'gender' => 'bail|nullable',
            'phone' => 'bail|nullable|digits_between:6,12',
            'expertise_id' => 'bail|nullable',
            'timeslot' => 'bail|nullable',
            'start_time' => 'bail|nullable',
            'end_time' => 'bail|nullable|after:start_time',
            'desc' => 'nullable',
            'appointment_fees' => 'nullable|numeric',
            'experience' => 'bail|nullable|numeric',
            'custom_timeslot' => 'bail|nullable',
            'commission_amount' => 'bail|nullable',
            'password' => 'sometimes|nullable|min:6',
        ]);
        
        $data = $request->all();
        if (isset($data['password']) && ($data['password'] != '' || $data['password'] != null)) {
            $password = $data['password'];
        } else {
            $password = mt_rand(100000, 999999);
        }
        unset($data['password']);
        
        $setting = Setting::first();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'verify' => 1,
            'phone' => $data['phone'],
            'phone_code' => $data['phone_code'],
            'image' => 'defaultUser.png',
        ]);
        
        $message1 = 'Dear Doctor your password is : ' . $password;
        try {
            (new CustomController)->applyMailConfig($setting);
            Mail::to($user->email)->send(new SendMail($message1, 'Doctor Password'));
        } catch (\Exception $e) {
            info($e);
        }
        
        $user->assignRole('doctor');
        $data['user_id'] = $user->id;
        $data['start_time'] = strtolower(Carbon::parse($data['start_time'])->format('h:i a'));
        $data['end_time'] = strtolower(Carbon::parse($data['end_time'])->format('h:i a'));
        
        if ($request->hasFile('image')) {
            $data['image'] = (new CustomController)->imageUpload($request->image);
        } else {
            $data['image'] = 'defaultUser.png';
        }
        
        $education = [];
        for ($i = 0; $i < count($data['degree']); $i++) {
            $temp['degree'] = $data['degree'][$i];
            $temp['college'] = $data['college'][$i];
            $temp['year'] = $data['year'][$i];
            array_push($education, $temp);
        }
        $data['education'] = json_encode($education);
        
        $certificate = [];
        for ($i = 0; $i < count($data['certificate']); $i++) {
            $temp1['certificate'] = $data['certificate'][$i];
            $temp1['certificate_year'] = $data['certificate_year'][$i];
            array_push($certificate, $temp1);
        }
        $data['certificate'] = json_encode($certificate);
        
        $data['since'] = Carbon::now(env('timezone'))->format('Y-m-d , h:i A');
        $data['status'] = 1;
        $data['subscription_status'] = 1;
        $data['is_filled'] = 1;
        
        // Force hospital_id to admin doctor's hospital and role to SUB_DOCTOR
        $data['hospital_id'] = $adminDoctor->hospital_id;
        $data['doctor_role'] = 'SUB_DOCTOR';
        
        if ($data['commission_amount'] == '') {
            $data['commission_amount'] = null;
        }
        
        // Remove treatment_id and category_id from data array
        $treatmentIds = $request->treatment_id ?? [];
        $categoryIds = $request->category_id ?? [];
        unset($data['treatment_id'], $data['category_id']);
        
        $doctor = Doctor::create($data);
        
        // Sync treatments and categories
        if (!empty($treatmentIds)) {
            $doctor->treatments()->sync($treatmentIds);
        }
        if (!empty($categoryIds)) {
            $doctor->categories()->sync($categoryIds);
        }
        
        if ($doctor->based_on == 'subscription') {
            $subscription = Subscription::where('name', 'free')->first();
            if ($subscription) {
                $doctor_subscription['doctor_id'] = $doctor->id;
                $doctor_subscription['subscription_id'] = $subscription->id;
                $doctor_subscription['duration'] = 1;
                $doctor_subscription['start_date'] = Carbon::now(env('timezone'))->format('Y-m-d');
                $doctor_subscription['end_date'] = Carbon::now(env('timezone'))->addMonths(1)->format('Y-m-d');
                $doctor_subscription['status'] = 1;
                $doctor_subscription['payment_status'] = 1;
                DoctorSubscription::create($doctor_subscription);
            }
        }
        
        // Create working hours
        $start_time = strtolower($doctor->start_time);
        $end_time = strtolower($doctor->end_time);
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        for ($i = 0; $i < count($days); $i++) {
            $master = [];
            $temp2['start_time'] = $start_time;
            $temp2['end_time'] = $end_time;
            array_push($master, $temp2);
            $work_time['doctor_id'] = $doctor->id;
            $work_time['period_list'] = json_encode($master);
            $work_time['day_index'] = $days[$i];
            $work_time['status'] = 1;
            WorkingHour::create($work_time);
        }

        return redirect('doctor/clinic-doctors')->withStatus(__('Sub-doctor created successfully!'));
    }

    /**
     * Show the form for editing a sub-doctor.
     */
    public function edit($id)
    {
        $adminDoctor = $this->getAdminDoctor();
        $doctor = Doctor::with(['treatments', 'categories'])->find($id);
        
        // Security check: ensure the doctor belongs to the admin's clinic and is a sub-doctor
        if (!$doctor || $doctor->hospital_id != $adminDoctor->hospital_id || $doctor->doctor_role != 'SUB_DOCTOR') {
            abort(403, 'You can only edit sub-doctors from your clinic.');
        }
        
        $doctor->user = User::find($doctor->user_id);
        $countries = Country::get();
        $treatments = Treatments::whereStatus(1)->get();
        $categories = Category::whereStatus(1)->get();
        $expertieses = Expertise::whereStatus(1)->get();
        $clinic = Hospital::find($adminDoctor->hospital_id);
        
        $doctor['start_time'] = Carbon::parse($doctor['start_time'])->format('H:i');
        $doctor['end_time'] = Carbon::parse($doctor['end_time'])->format('H:i');
        
        // Get selected treatment and category IDs
        $doctor['selected_treatment_ids'] = $doctor->treatments->pluck('id')->toArray();
        $doctor['selected_category_ids'] = $doctor->categories->pluck('id')->toArray();

        return view('doctor.clinic_doctors.edit', compact('doctor', 'countries', 'treatments', 'categories', 'expertieses', 'clinic', 'adminDoctor'));
    }

    /**
     * Update the specified sub-doctor.
     */
    public function update(Request $request, $id)
    {
        $adminDoctor = $this->getAdminDoctor();
        $doctor = Doctor::find($id);
        
        // Security check
        if (!$doctor || $doctor->hospital_id != $adminDoctor->hospital_id || $doctor->doctor_role != 'SUB_DOCTOR') {
            abort(403, 'You can only edit sub-doctors from your clinic.');
        }
        
        $request->validate([
            'name' => 'bail|nullable|unique:doctor,name,' . $id . ',id',
            'treatment_id' => 'bail|nullable|array',
            'treatment_id.*' => 'bail|nullable|exists:treatments,id',
            'category_id' => 'bail|nullable|array',
            'category_id.*' => 'bail|nullable|exists:category,id',
            'dob' => 'bail|nullable',
            'gender' => 'bail|nullable',
            'phone' => 'bail|nullable|digits_between:6,12',
            'expertise_id' => 'bail|nullable',
            'timeslot' => 'bail|nullable',
            'start_time' => 'bail|nullable',
            'end_time' => 'bail|nullable|after:start_time',
            'desc' => 'nullable',
            'appointment_fees' => 'nullable|numeric',
            'experience' => 'bail|nullable|numeric',
            'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
            'custom_timeslot' => 'bail|nullable',
            'commission_amount' => 'bail|nullable',
            'password' => 'sometimes|nullable|min:6',
        ], [
            'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
        ]);
        
        $data = $request->all();
        $data['start_time'] = Carbon::parse($data['start_time'])->format('h:i A');
        $data['end_time'] = Carbon::parse($data['end_time'])->format('h:i A');
        
        if ($request->hasFile('image')) {
            (new CustomController)->deleteFile($doctor->image);
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        
        $education = [];
        for ($i = 0; $i < count($data['degree']); $i++) {
            $temp['degree'] = $data['degree'][$i];
            $temp['college'] = $data['college'][$i];
            $temp['year'] = $data['year'][$i];
            array_push($education, $temp);
        }
        $data['education'] = json_encode($education);
        
        $certificate = [];
        for ($i = 0; $i < count($data['certificate']); $i++) {
            $temp1['certificate'] = $data['certificate'][$i];
            $temp1['certificate_year'] = $data['certificate_year'][$i];
            array_push($certificate, $temp1);
        }
        $data['certificate'] = json_encode($certificate);
        
        $data['is_filled'] = 1;
        $data['custom_timeslot'] = $request->custom_time == '' ? null : $request->custom_time;
        
        // Keep the hospital_id and doctor_role unchanged
        $data['hospital_id'] = $adminDoctor->hospital_id;
        $data['doctor_role'] = 'SUB_DOCTOR';
        
        if ($data['commission_amount'] == '') {
            $data['commission_amount'] = null;
        }
        
        // Handle subscription
        if ($request->based_on == 'subscription') {
            if (!DoctorSubscription::where('doctor_id', $id)->exists()) {
                $subscription = Subscription::where('name', 'free')->first();
                if ($subscription) {
                    $doctor_subscription['doctor_id'] = $doctor->id;
                    $doctor_subscription['subscription_id'] = $subscription->id;
                    $doctor_subscription['duration'] = 1;
                    $doctor_subscription['start_date'] = Carbon::now(env('timezone'))->format('Y-m-d');
                    $doctor_subscription['end_date'] = Carbon::now(env('timezone'))->addMonths(1)->format('Y-m-d');
                    $doctor_subscription['status'] = 1;
                    $doctor_subscription['payment_status'] = 1;
                    DoctorSubscription::create($doctor_subscription);
                }
            }
        }

        // Remove treatment_id and category_id from data array
        $treatmentIds = $request->treatment_id ?? [];
        $categoryIds = $request->category_id ?? [];
        unset($data['treatment_id'], $data['category_id']);

        if (isset($data['password']) && ($data['password'] != '' || $data['password'] != null)) {
            $password = Hash::make($data['password']);
            User::find($doctor->user_id)->update(['password' => $password]);
        }
        unset($data['password']);
        
        $doctor->update($data);
        
        // Sync treatments and categories
        if (!empty($treatmentIds)) {
            $doctor->treatments()->sync($treatmentIds);
        }
        if (!empty($categoryIds)) {
            $doctor->categories()->sync($categoryIds);
        }

        return redirect('doctor/clinic-doctors')->withStatus(__('Sub-doctor updated successfully!'));
    }

    /**
     * Remove the specified sub-doctor.
     */
    public function destroy($id)
    {
        $adminDoctor = $this->getAdminDoctor();
        $doctor = Doctor::find($id);
        
        // Security check
        if (!$doctor || $doctor->hospital_id != $adminDoctor->hospital_id || $doctor->doctor_role != 'SUB_DOCTOR') {
            return response(['success' => false, 'data' => 'You can only delete sub-doctors from your clinic.']);
        }
        
        $user = User::find($doctor->user_id);
        if ($user) {
            $user->removeRole('doctor');
            $user->delete();
        }
        
        (new CustomController)->deleteFile($doctor->image);
        $doctor->delete();

        return response(['success' => true]);
    }

    /**
     * Change the status of a sub-doctor.
     */
    public function changeStatus(Request $request)
    {
        $adminDoctor = $this->getAdminDoctor();
        $doctor = Doctor::find($request->id);
        
        // Security check
        if (!$doctor || $doctor->hospital_id != $adminDoctor->hospital_id || $doctor->doctor_role != 'SUB_DOCTOR') {
            return response(['success' => false]);
        }
        
        $data['status'] = $doctor->status == 1 ? 0 : 1;
        $doctor->update($data);

        return response(['success' => true]);
    }
}
