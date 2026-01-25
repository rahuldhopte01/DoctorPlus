<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBrand;
use Illuminate\Database\Seeder;

/**
 * Seeds medicines, brands, and category–medicine links for testing the questionnaire flow.
 * Medicines are assigned to categories via category_medicine. Run:
 *   php artisan db:seed --class=QuestionnairePatientFlowTestDataSeeder
 */
class QuestionnairePatientFlowTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $generic = MedicineBrand::firstOrCreate(['name' => 'Generic'], ['name' => 'Generic']);
        $bayer = MedicineBrand::firstOrCreate(['name' => 'Bayer'], ['name' => 'Bayer']);
        $pfizer = MedicineBrand::firstOrCreate(['name' => 'Pfizer'], ['name' => 'Pfizer']);

        $meds = [
            ['name' => 'Paracetamol', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ibuprofen', 'strength' => '400mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Amoxicillin', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Panadol', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Nurofen', 'strength' => '400mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Amoxil', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $pfizer->id],
            ['name' => 'Omeprazole', 'strength' => '20mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Loratadine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Cetirizine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
        ];

        $medicineIds = [];
        foreach ($meds as $m) {
            $model = Medicine::firstOrCreate(
                ['name' => $m['name'], 'strength' => $m['strength'], 'form' => $m['form']],
                ['brand_id' => $m['brand_id'], 'status' => 1, 'description' => 'Test medicine for questionnaire flow.']
            );
            $medicineIds[] = $model->id;
        }

        $categories = Category::orderBy('id')->get();
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Create categories first, then run this seeder again.');
            return;
        }

        foreach ($categories as $index => $category) {
            $category->medicines()->detach();
            // Assign a rotating subset of medicines per category so each has different options
            $offset = $index % count($medicineIds);
            $ids = [];
            for ($i = 0; $i < min(5, count($medicineIds)); $i++) {
                $ids[] = $medicineIds[($offset + $i) % count($medicineIds)];
            }
            $category->medicines()->sync(array_unique($ids));
        }

        $this->command->info('Questionnaire patient flow test data seeded: medicines, brands, category–medicine links.');
    }
}
