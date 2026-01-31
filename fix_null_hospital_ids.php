<?php
/**
 * Script to fix NULL hospital_id in questionnaire_answers
 * 
 * This will update questionnaire_answers with NULL hospital_id
 * by finding the appropriate hospital based on category assignments
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING NULL HOSPITAL_ID IN QUESTIONNAIRE_ANSWERS ===\n\n";

$answersWithNullHospital = \App\Models\QuestionnaireAnswer::whereNull('hospital_id')
    ->whereNull('appointment_id')
    ->with('category')
    ->get()
    ->groupBy(function($a) {
        return $a->user_id . '_' . $a->category_id . '_' . $a->questionnaire_id;
    });

echo "Found " . $answersWithNullHospital->count() . " submissions with NULL hospital_id\n\n";

$fixed = 0;
$notFixed = 0;

foreach($answersWithNullHospital as $key => $group) {
    $first = $group->first();
    $categoryId = $first->category_id;
    
    // Find a doctor with this category to determine hospital
    $doctor = \App\Models\Doctor::whereHas('categories', function($query) use ($categoryId) {
        $query->where('category_id', $categoryId);
    })
    ->whereNotNull('hospital_id')
    ->first();
    
    if ($doctor) {
        // Update all answers in this submission
        \App\Models\QuestionnaireAnswer::where('user_id', $first->user_id)
            ->where('category_id', $first->category_id)
            ->where('questionnaire_id', $first->questionnaire_id)
            ->whereNull('appointment_id')
            ->whereNull('hospital_id')
            ->update(['hospital_id' => $doctor->hospital_id]);
        
        echo "Fixed submission {$key}: Set hospital_id to {$doctor->hospital_id}\n";
        $fixed++;
    } else {
        echo "Could not fix submission {$key}: No doctor found with category #{$categoryId}\n";
        $notFixed++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Fixed: {$fixed}\n";
echo "Not Fixed: {$notFixed}\n";
