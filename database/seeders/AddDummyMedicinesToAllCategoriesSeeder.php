<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBrand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds dummy medicines and assigns them to ALL categories for testing.
 * This ensures every category has medicines available for patient selection.
 * 
 * Run: php artisan db:seed --class=AddDummyMedicinesToAllCategoriesSeeder
 */
class AddDummyMedicinesToAllCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating medicine brands...');
        
        // Create or get medicine brands
        $generic = MedicineBrand::firstOrCreate(['name' => 'Generic'], ['name' => 'Generic']);
        $bayer = MedicineBrand::firstOrCreate(['name' => 'Bayer'], ['name' => 'Bayer']);
        $pfizer = MedicineBrand::firstOrCreate(['name' => 'Pfizer'], ['name' => 'Pfizer']);
        $glaxo = MedicineBrand::firstOrCreate(['name' => 'GlaxoSmithKline'], ['name' => 'GlaxoSmithKline']);
        $novartis = MedicineBrand::firstOrCreate(['name' => 'Novartis'], ['name' => 'Novartis']);
        $cipla = MedicineBrand::firstOrCreate(['name' => 'Cipla'], ['name' => 'Cipla']);
        
        $this->command->info('Creating dummy medicines...');
        
        // Common medicines with different brands and strengths
        $medicines = [
            // Pain Relief
            ['name' => 'Paracetamol', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Paracetamol', 'strength' => '650mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Panadol', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Ibuprofen', 'strength' => '400mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ibuprofen', 'strength' => '600mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Nurofen', 'strength' => '400mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Aspirin', 'strength' => '100mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Diclofenac', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Antibiotics
            ['name' => 'Amoxicillin', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Amoxicillin', 'strength' => '500mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Amoxil', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $pfizer->id],
            ['name' => 'Azithromycin', 'strength' => '250mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Azithromycin', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ciprofloxacin', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Cephalexin', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            
            // Antacids & Digestive
            ['name' => 'Omeprazole', 'strength' => '20mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Omeprazole', 'strength' => '40mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Pantoprazole', 'strength' => '40mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ranitidine', 'strength' => '150mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Domperidone', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Antihistamines
            ['name' => 'Loratadine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Cetirizine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Fexofenadine', 'strength' => '120mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Levocetirizine', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Cough & Cold
            ['name' => 'Dextromethorphan', 'strength' => '15mg', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Guaifenesin', 'strength' => '200mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Pseudoephedrine', 'strength' => '60mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Vitamins & Supplements
            ['name' => 'Vitamin D3', 'strength' => '1000 IU', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Vitamin C', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Multivitamin', 'strength' => '1 Tablet', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Calcium', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Cardiovascular
            ['name' => 'Atenolol', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Amlodipine', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Losartan', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Diabetes
            ['name' => 'Metformin', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Metformin', 'strength' => '850mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Glibenclamide', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Topical
            ['name' => 'Hydrocortisone Cream', 'strength' => '1%', 'form' => 'Cream', 'brand_id' => $generic->id],
            ['name' => 'Mupirocin Ointment', 'strength' => '2%', 'form' => 'Ointment', 'brand_id' => $generic->id],
            ['name' => 'Clotrimazole Cream', 'strength' => '1%', 'form' => 'Cream', 'brand_id' => $generic->id],
        ];
        
        $medicineIds = [];
        foreach ($medicines as $med) {
            $medicine = Medicine::firstOrCreate(
                [
                    'name' => $med['name'],
                    'strength' => $med['strength'],
                    'form' => $med['form']
                ],
                [
                    'brand_id' => $med['brand_id'],
                    'status' => 1,
                    'description' => 'Dummy medicine for testing questionnaire medicine selection.'
                ]
            );
            $medicineIds[] = $medicine->id;
        }
        
        $this->command->info('Found ' . count($medicineIds) . ' medicines.');
        
        // Get all categories
        $categories = Category::where('status', 1)->get();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No active categories found. Please create categories first.');
            return;
        }
        
        $this->command->info('Assigning medicines to ' . $categories->count() . ' categories...');
        
        // Assign ALL medicines to ALL categories
        foreach ($categories as $category) {
            // Sync all medicines to this category (this will replace any existing links)
            $category->medicines()->sync($medicineIds);
            $this->command->line("  ✓ Assigned " . count($medicineIds) . " medicines to category: {$category->name} (ID: {$category->id})");
        }
        
        $this->command->info('');
        $this->command->info('✓ Successfully assigned ' . count($medicineIds) . ' medicines to ' . $categories->count() . ' categories!');
        $this->command->info('You can now test medicine selection in patient login for all categories.');
    }
}
