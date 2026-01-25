<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireSubmission extends Model
{
    use HasFactory;

    protected $table = 'questionnaire_submissions';

    protected $fillable = [
        'user_id',
        'category_id',
        'questionnaire_id',
        'delivery_type',
        'delivery_address_id',
        'delivery_postcode',
        'delivery_city',
        'delivery_state',
        'delivery_address',
        'selected_pharmacy_id',
        'selected_medicines',
        'status',
    ];

    protected $casts = [
        'selected_medicines' => 'array',
    ];

    /**
     * Get the user that submitted the questionnaire.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category this submission belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the questionnaire this submission belongs to.
     */
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    /**
     * Get the delivery address (if delivery is selected).
     */
    public function deliveryAddress()
    {
        return $this->belongsTo(UserAddress::class, 'delivery_address_id');
    }

    /**
     * Get the selected pharmacy (if pickup is selected).
     */
    public function selectedPharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'selected_pharmacy_id');
    }

    /**
     * Get questionnaire answers for this submission.
     */
    public function answers()
    {
        return QuestionnaireAnswer::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('questionnaire_id', $this->questionnaire_id)
            ->whereNull('appointment_id')
            ->get();
    }

    /**
     * Check if delivery type is set.
     */
    public function hasDeliveryType(): bool
    {
        return !empty($this->delivery_type);
    }

    /**
     * Check if delivery address is complete.
     */
    public function hasCompleteDeliveryAddress(): bool
    {
        return $this->delivery_type === 'delivery' 
            && !empty($this->delivery_address)
            && !empty($this->delivery_postcode)
            && !empty($this->delivery_city)
            && !empty($this->delivery_state);
    }

    /**
     * Check if pharmacy is selected.
     */
    public function hasSelectedPharmacy(): bool
    {
        return $this->delivery_type === 'pickup' && !empty($this->selected_pharmacy_id);
    }

    /**
     * Check if medicines are selected.
     */
    public function hasSelectedMedicines(): bool
    {
        return !empty($this->selected_medicines) && is_array($this->selected_medicines) && count($this->selected_medicines) > 0;
    }
}
