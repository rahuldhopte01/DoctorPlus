<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Questionnaire;
use App\Models\QuestionnaireSection;
use App\Models\QuestionnaireQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateHairfallQuestionnaire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questionnaire:create-hairfall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a dummy questionnaire for the hairfall category';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find the hairfall category (case-insensitive search)
        $category = Category::whereRaw('LOWER(name) = ?', [Str::lower('hairfall')])
            ->orWhereRaw('LOWER(name) = ?', [Str::lower('hair fall')])
            ->orWhereRaw('LOWER(name) = ?', [Str::lower('hairfall treatment')])
            ->first();

        if (!$category) {
            $this->error('Category "hairfall" not found. Please create it first.');
            $this->info('Available categories:');
            Category::all()->each(function($cat) {
                $this->line("  - {$cat->name} (ID: {$cat->id})");
            });
            return 1;
        }

        $this->info("Found category: {$category->name} (ID: {$category->id})");

        // Check if questionnaire already exists
        $existingQuestionnaire = Questionnaire::where('category_id', $category->id)->first();
        if ($existingQuestionnaire) {
            if (!$this->confirm("Questionnaire already exists for this category. Delete and recreate?", true)) {
                $this->info('Cancelled.');
                return 0;
            }
            $this->warn("Deleting existing questionnaire...");
            $existingQuestionnaire->delete(); // This will cascade delete sections and questions
        }

        $this->info("Creating questionnaire...");

        // Create questionnaire
        $questionnaire = Questionnaire::create([
            'category_id' => $category->id,
            'name' => 'Hair Fall Assessment Questionnaire',
            'description' => 'Please complete this questionnaire to help us understand your hair fall condition and provide the best treatment recommendations.',
            'status' => 1,
            'version' => 1,
        ]);

        $this->info("✓ Created questionnaire: {$questionnaire->name}");

        // Section 1: Personal Information
        $section1 = QuestionnaireSection::create([
            'questionnaire_id' => $questionnaire->id,
            'name' => 'Personal Information',
            'description' => 'Basic information about you',
            'order' => 0,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section1->id,
            'question_text' => 'What is your age?',
            'field_type' => 'number',
            'options' => null,
            'required' => true,
            'validation_rules' => ['min' => 1, 'max' => 120],
            'order' => 0,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section1->id,
            'question_text' => 'What is your gender?',
            'field_type' => 'radio',
            'options' => ['Male', 'Female', 'Other'],
            'required' => true,
            'order' => 1,
        ]);

        // Section 2: Hair Fall History
        $section2 = QuestionnaireSection::create([
            'questionnaire_id' => $questionnaire->id,
            'name' => 'Hair Fall History',
            'description' => 'Tell us about your hair fall condition',
            'order' => 1,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section2->id,
            'question_text' => 'How long have you been experiencing hair fall?',
            'field_type' => 'radio',
            'options' => ['Less than 1 month', '1-3 months', '3-6 months', '6-12 months', 'More than 1 year'],
            'required' => true,
            'order' => 0,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section2->id,
            'question_text' => 'How severe is your hair fall?',
            'field_type' => 'radio',
            'options' => ['Mild (few strands)', 'Moderate (noticeable thinning)', 'Severe (significant hair loss)', 'Very Severe (bald patches)'],
            'required' => true,
            'order' => 1,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section2->id,
            'question_text' => 'Where do you notice hair fall the most?',
            'field_type' => 'checkbox',
            'options' => ['Crown/Top of head', 'Temples', 'Front hairline', 'Back of head', 'All over', 'Other'],
            'required' => true,
            'order' => 2,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section2->id,
            'question_text' => 'How many strands of hair do you lose per day?',
            'field_type' => 'radio',
            'options' => ['Less than 50', '50-100', '100-150', 'More than 150', 'Not sure'],
            'required' => true,
            'order' => 3,
        ]);

        // Section 3: Medical History
        $section3 = QuestionnaireSection::create([
            'questionnaire_id' => $questionnaire->id,
            'name' => 'Medical History',
            'description' => 'Relevant medical information',
            'order' => 2,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section3->id,
            'question_text' => 'Do you have any existing medical conditions?',
            'field_type' => 'radio',
            'options' => ['Yes', 'No'],
            'required' => true,
            'flagging_rules' => [
                'flag_type' => 'soft',
                'conditions' => [
                    [
                        'operator' => 'equals',
                        'value' => 'Yes',
                        'flag_message' => 'Patient has existing medical conditions - review medications and interactions'
                    ]
                ]
            ],
            'order' => 0,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section3->id,
            'question_text' => 'If yes, please specify your medical conditions:',
            'field_type' => 'textarea',
            'options' => null,
            'required' => false,
            'order' => 1,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section3->id,
            'question_text' => 'Are you currently taking any medications?',
            'field_type' => 'radio',
            'options' => ['Yes', 'No'],
            'required' => true,
            'order' => 2,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section3->id,
            'question_text' => 'Please list your current medications:',
            'field_type' => 'textarea',
            'options' => null,
            'required' => false,
            'doctor_notes' => 'Check for medications that may cause hair loss',
            'order' => 3,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section3->id,
            'question_text' => 'Do you have a family history of hair loss?',
            'field_type' => 'radio',
            'options' => ['Yes', 'No', 'Not sure'],
            'required' => true,
            'order' => 4,
        ]);

        // Section 4: Lifestyle & Habits
        $section4 = QuestionnaireSection::create([
            'questionnaire_id' => $questionnaire->id,
            'name' => 'Lifestyle & Habits',
            'description' => 'Information about your lifestyle that may affect hair health',
            'order' => 3,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section4->id,
            'question_text' => 'What is your stress level?',
            'field_type' => 'radio',
            'options' => ['Low', 'Moderate', 'High', 'Very High'],
            'required' => true,
            'flagging_rules' => [
                'flag_type' => 'soft',
                'conditions' => [
                    [
                        'operator' => 'in',
                        'value' => ['High', 'Very High'],
                        'flag_message' => 'High stress levels may contribute to hair fall'
                    ]
                ]
            ],
            'order' => 0,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section4->id,
            'question_text' => 'How would you describe your diet?',
            'field_type' => 'radio',
            'options' => ['Balanced', 'Vegetarian', 'Vegan', 'High protein', 'Restricted/Low calorie'],
            'required' => true,
            'order' => 1,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section4->id,
            'question_text' => 'Do you smoke?',
            'field_type' => 'radio',
            'options' => ['Yes, regularly', 'Occasionally', 'No'],
            'required' => true,
            'order' => 2,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section4->id,
            'question_text' => 'How often do you use heat styling tools (hair dryer, straightener, curler)?',
            'field_type' => 'radio',
            'options' => ['Daily', '2-3 times a week', 'Once a week', 'Rarely', 'Never'],
            'required' => true,
            'order' => 3,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section4->id,
            'question_text' => 'How often do you color or chemically treat your hair?',
            'field_type' => 'radio',
            'options' => ['Monthly or more', 'Every 2-3 months', 'Every 6 months', 'Rarely', 'Never'],
            'required' => true,
            'order' => 4,
        ]);

        // Section 5: Hair Care Routine
        $section5 = QuestionnaireSection::create([
            'questionnaire_id' => $questionnaire->id,
            'name' => 'Hair Care Routine',
            'description' => 'Tell us about your current hair care practices',
            'order' => 4,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section5->id,
            'question_text' => 'How often do you wash your hair?',
            'field_type' => 'radio',
            'options' => ['Daily', 'Every other day', '2-3 times a week', 'Once a week', 'Less than once a week'],
            'required' => true,
            'order' => 0,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section5->id,
            'question_text' => 'What type of shampoo do you currently use?',
            'field_type' => 'text',
            'options' => null,
            'required' => false,
            'order' => 1,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section5->id,
            'question_text' => 'Have you tried any hair fall treatments before?',
            'field_type' => 'radio',
            'options' => ['Yes', 'No'],
            'required' => true,
            'order' => 2,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section5->id,
            'question_text' => 'If yes, what treatments have you tried?',
            'field_type' => 'textarea',
            'options' => null,
            'required' => false,
            'doctor_notes' => 'Review previous treatments to avoid repetition',
            'order' => 3,
        ]);

        // Section 6: Additional Information
        $section6 = QuestionnaireSection::create([
            'questionnaire_id' => $questionnaire->id,
            'name' => 'Additional Information',
            'description' => 'Any other relevant information',
            'order' => 5,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section6->id,
            'question_text' => 'Have you noticed any scalp issues? (Check all that apply)',
            'field_type' => 'checkbox',
            'options' => ['Dandruff', 'Itchy scalp', 'Oily scalp', 'Dry scalp', 'Scalp psoriasis', 'None'],
            'required' => false,
            'order' => 0,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section6->id,
            'question_text' => 'Have you experienced any recent major life changes? (e.g., pregnancy, surgery, illness)',
            'field_type' => 'radio',
            'options' => ['Yes', 'No'],
            'required' => true,
            'order' => 1,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section6->id,
            'question_text' => 'If yes, please describe:',
            'field_type' => 'textarea',
            'options' => null,
            'required' => false,
            'order' => 2,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section6->id,
            'question_text' => 'Please upload any relevant photos of your hair/scalp (optional)',
            'field_type' => 'file',
            'options' => null,
            'required' => false,
            'validation_rules' => [
                'file_types' => ['jpg', 'jpeg', 'png'],
                'file_max_size' => 5242880 // 5MB
            ],
            'order' => 3,
        ]);

        QuestionnaireQuestion::create([
            'section_id' => $section6->id,
            'question_text' => 'Any additional comments or concerns?',
            'field_type' => 'textarea',
            'options' => null,
            'required' => false,
            'order' => 4,
        ]);

        $totalQuestions = $questionnaire->questions()->count();
        $this->info("✅ Successfully created questionnaire!");
        $this->info("   - Questionnaire ID: {$questionnaire->id}");
        $this->info("   - Category: {$category->name}");
        $this->info("   - Sections: 6");
        $this->info("   - Questions: {$totalQuestions}");
        $this->info("   - Status: Active");
        $this->info("");
        $this->info("You can now view it in Super Admin → Questionnaires");
        $this->info("Or test it at: /questionnaire/category/{$category->id}");

        return 0;
    }
}
