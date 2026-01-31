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
        
        // Comprehensive medicine list with different brands and strengths
        $medicines = [
            // Pain Relief & Anti-inflammatory
            ['name' => 'Paracetamol', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Paracetamol', 'strength' => '650mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Paracetamol', 'strength' => '120mg/5ml', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Panadol', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Ibuprofen', 'strength' => '400mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ibuprofen', 'strength' => '600mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ibuprofen', 'strength' => '100mg/5ml', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Nurofen', 'strength' => '400mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Aspirin', 'strength' => '100mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Aspirin', 'strength' => '300mg', 'form' => 'Tablet', 'brand_id' => $bayer->id],
            ['name' => 'Diclofenac', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Diclofenac', 'strength' => '75mg', 'form' => 'Injection', 'brand_id' => $generic->id],
            ['name' => 'Naproxen', 'strength' => '250mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Naproxen', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Antibiotics
            ['name' => 'Amoxicillin', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Amoxicillin', 'strength' => '500mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Amoxicillin', 'strength' => '125mg/5ml', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Amoxil', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $pfizer->id],
            ['name' => 'Amoxicillin + Clavulanic Acid', 'strength' => '625mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Azithromycin', 'strength' => '250mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Azithromycin', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Azithromycin', 'strength' => '200mg/5ml', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Ciprofloxacin', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ciprofloxacin', 'strength' => '750mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Cephalexin', 'strength' => '250mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Cephalexin', 'strength' => '500mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Doxycycline', 'strength' => '100mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Erythromycin', 'strength' => '250mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Metronidazole', 'strength' => '400mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Antacids & Digestive
            ['name' => 'Omeprazole', 'strength' => '20mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Omeprazole', 'strength' => '40mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Pantoprazole', 'strength' => '20mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Pantoprazole', 'strength' => '40mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ranitidine', 'strength' => '150mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ranitidine', 'strength' => '300mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Domperidone', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Esomeprazole', 'strength' => '40mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Lansoprazole', 'strength' => '30mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            
            // Antihistamines & Allergy
            ['name' => 'Loratadine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Cetirizine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Cetirizine', 'strength' => '5mg/5ml', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Fexofenadine', 'strength' => '120mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Fexofenadine', 'strength' => '180mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Levocetirizine', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Chlorpheniramine', 'strength' => '4mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Desloratadine', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Cough & Cold
            ['name' => 'Dextromethorphan', 'strength' => '15mg/5ml', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Guaifenesin', 'strength' => '200mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Guaifenesin', 'strength' => '100mg/5ml', 'form' => 'Syrup', 'brand_id' => $generic->id],
            ['name' => 'Pseudoephedrine', 'strength' => '60mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Phenylephrine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Bromhexine', 'strength' => '8mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ambroxol', 'strength' => '30mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Vitamins & Supplements
            ['name' => 'Vitamin D3', 'strength' => '1000 IU', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Vitamin D3', 'strength' => '60000 IU', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Vitamin C', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Vitamin C', 'strength' => '1000mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Vitamin B Complex', 'strength' => '1 Tablet', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Multivitamin', 'strength' => '1 Tablet', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Calcium', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Calcium + Vitamin D', 'strength' => '500mg+250IU', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Iron', 'strength' => '100mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Folic Acid', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Zinc', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Omega-3', 'strength' => '1000mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            
            // Cardiovascular
            ['name' => 'Atenolol', 'strength' => '25mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Atenolol', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Amlodipine', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Amlodipine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Losartan', 'strength' => '25mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Losartan', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Enalapril', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Ramipril', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Metoprolol', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Carvedilol', 'strength' => '6.25mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Atorvastatin', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Atorvastatin', 'strength' => '20mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Rosuvastatin', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Clopidogrel', 'strength' => '75mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Diabetes
            ['name' => 'Metformin', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Metformin', 'strength' => '850mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Metformin', 'strength' => '1000mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Glibenclamide', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Gliclazide', 'strength' => '80mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Glimepiride', 'strength' => '2mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Sitagliptin', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Sitagliptin', 'strength' => '100mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Topical & Dermatology
            ['name' => 'Hydrocortisone Cream', 'strength' => '1%', 'form' => 'Cream', 'brand_id' => $generic->id],
            ['name' => 'Betamethasone Cream', 'strength' => '0.1%', 'form' => 'Cream', 'brand_id' => $generic->id],
            ['name' => 'Mupirocin Ointment', 'strength' => '2%', 'form' => 'Ointment', 'brand_id' => $generic->id],
            ['name' => 'Clotrimazole Cream', 'strength' => '1%', 'form' => 'Cream', 'brand_id' => $generic->id],
            ['name' => 'Ketoconazole Cream', 'strength' => '2%', 'form' => 'Cream', 'brand_id' => $generic->id],
            ['name' => 'Fusidic Acid Cream', 'strength' => '2%', 'form' => 'Cream', 'brand_id' => $generic->id],
            ['name' => 'Calamine Lotion', 'strength' => '100ml', 'form' => 'Lotion', 'brand_id' => $generic->id],
            ['name' => 'Benzoyl Peroxide Gel', 'strength' => '5%', 'form' => 'Gel', 'brand_id' => $generic->id],
            ['name' => 'Tretinoin Cream', 'strength' => '0.05%', 'form' => 'Cream', 'brand_id' => $generic->id],
            
            // Hair Treatment
            ['name' => 'Minoxidil Solution', 'strength' => '5%', 'form' => 'Solution', 'brand_id' => $generic->id],
            ['name' => 'Minoxidil Solution', 'strength' => '2%', 'form' => 'Solution', 'brand_id' => $generic->id],
            ['name' => 'Finasteride', 'strength' => '1mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Biotin', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Hair Growth Serum', 'strength' => '60ml', 'form' => 'Serum', 'brand_id' => $generic->id],
            
            // Weight Management
            ['name' => 'Orlistat', 'strength' => '120mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Orlistat', 'strength' => '60mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            
            // Mental Health & Sleep
            ['name' => 'Fluoxetine', 'strength' => '20mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Sertraline', 'strength' => '50mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Escitalopram', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Alprazolam', 'strength' => '0.5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Diazepam', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Melatonin', 'strength' => '3mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Eye & Ear
            ['name' => 'Tobramycin Eye Drops', 'strength' => '0.3%', 'form' => 'Drops', 'brand_id' => $generic->id],
            ['name' => 'Ciprofloxacin Eye Drops', 'strength' => '0.3%', 'form' => 'Drops', 'brand_id' => $generic->id],
            ['name' => 'Artificial Tears', 'strength' => '10ml', 'form' => 'Drops', 'brand_id' => $generic->id],
            ['name' => 'Ear Drops', 'strength' => '10ml', 'form' => 'Drops', 'brand_id' => $generic->id],
            
            // Women's Health
            ['name' => 'Mefenamic Acid', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Tranexamic Acid', 'strength' => '500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Norethisterone', 'strength' => '5mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Pain Management (Advanced)
            ['name' => 'Tramadol', 'strength' => '50mg', 'form' => 'Capsule', 'brand_id' => $generic->id],
            ['name' => 'Codeine + Paracetamol', 'strength' => '30mg+500mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            
            // Muscle Relaxants
            ['name' => 'Cyclobenzaprine', 'strength' => '10mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
            ['name' => 'Tizanidine', 'strength' => '2mg', 'form' => 'Tablet', 'brand_id' => $generic->id],
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
