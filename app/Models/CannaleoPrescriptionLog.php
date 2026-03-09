<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CannaleoPrescriptionLog extends Model
{
    use HasFactory;

    protected $table = 'cannaleo_prescription_log';

    protected $fillable = [
        'prescription_id',
        'questionnaire_submission_id',
        'called_at',
        'request_payload',
        'response_status',
        'response_body',
        'external_order_id',
        'products_snapshot',
        'total_medicine_cost',
        'prescription_fee',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'products_snapshot' => 'array',
        'called_at' => 'datetime',
        'total_medicine_cost' => 'decimal:2',
        'prescription_fee' => 'decimal:2',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function questionnaireSubmission()
    {
        return $this->belongsTo(QuestionnaireSubmission::class, 'questionnaire_submission_id');
    }
}
