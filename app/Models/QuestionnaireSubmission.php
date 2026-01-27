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

    /**
     * Check if a patient can submit a questionnaire for a category.
     * Returns true if no active submission exists (PENDING or UNDER_REVIEW).
     * 
     * @param int $userId
     * @param int $categoryId
     * @return array ['can_submit' => bool, 'existing_submission' => QuestionnaireAnswer|null, 'message' => string, 'status' => string|null]
     */
    public static function canPatientSubmit($userId, $categoryId): array
    {
        // Check for existing submissions with status PENDING or UNDER_REVIEW
        // Group by submitted_at to identify unique submissions
        $existingAnswers = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->whereNull('appointment_id') // Only check standalone submissions
            ->whereIn('status', ['pending', 'under_review', 'IN_REVIEW'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        if ($existingAnswers->isEmpty()) {
            return [
                'can_submit' => true,
                'existing_submission' => null,
                'message' => null,
                'status' => null,
            ];
        }

        // Group by submitted_at to get the latest submission
        $grouped = $existingAnswers->groupBy(function($answer) {
            $submittedAt = $answer->submitted_at ? $answer->submitted_at->format('Y-m-d H:i:s') : $answer->created_at->format('Y-m-d H:i:s');
            return $submittedAt;
        });

        // Get the most recent submission
        $latestGroup = $grouped->first();
        $latestSubmission = $latestGroup->first();

        $status = $latestSubmission->status;
        $statusText = ucfirst(str_replace('_', ' ', strtolower($status)));
        if ($status === 'IN_REVIEW' || $status === 'under_review') {
            $statusText = 'Under Review';
        } elseif ($status === 'pending') {
            $statusText = 'Pending';
        }

        return [
            'can_submit' => false,
            'existing_submission' => $latestSubmission,
            'status' => $status,
            'message' => "You already have a questionnaire under review for this category. Current status: {$statusText}.",
        ];
    }

    /**
     * Get the current submission status for a patient and category.
     * 
     * @param int $userId
     * @param int $categoryId
     * @return string|null Status (pending, under_review, IN_REVIEW, approved, rejected) or null if no submission
     */
    public static function getSubmissionStatus($userId, $categoryId): ?string
    {
        $latestAnswer = QuestionnaireAnswer::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->whereNull('appointment_id')
            ->orderBy('submitted_at', 'desc')
            ->first();

        return $latestAnswer ? $latestAnswer->status : null;
    }
}
