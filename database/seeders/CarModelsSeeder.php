<?php

namespace Database\Seeders;

use App\Models\Brands;
use App\Models\CarModels;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelsByBrand = [
            'Toyota' => ['Corolla', 'Camry', 'RAV4', 'Yaris', 'Prius', 'Land Cruiser', 'Hilux', 'C-HR'],
            'BMW' => ['Series 3', 'Series 5', 'X3', 'X5', 'Series 1', 'M3', 'M4', 'iX'],
            'Mercedes' => ['Class A', 'Class C', 'Class E', 'GLC', 'GLE', 'Class S', 'CLA'],
            'Renault' => ['Clio', 'Megane', 'Captur', 'Arkana', 'Austral', 'Zoe', 'Kangoo'],
            'Volvo' => ['XC40', 'XC60', 'XC90', 'S60', 'S90', 'V60', 'C40'],
            'Ford' => ['Fiesta', 'Focus', 'Puma', 'Kuga', 'Mustang', 'Ranger', 'Explorer'],
            'Hyundai' => ['Tucson', 'Kona', 'i20', 'i30', 'Santa Fe', 'Ioniq 5', 'Bayon'],
            'Volkswagen' => ['Golf', 'Polo', 'Tiguan', 'T-Roc', 'Passat', 'ID.3', 'ID.4'],
            'Tesla' => ['Model 3', 'Model Y', 'Model S', 'Model X', 'Cybertruck'],
            'Audi' => ['A3', 'A4', 'A6', 'Q3', 'Q5', 'Q7', 'Q8', 'e-tron'],
        ];

        foreach ($modelsByBrand as $brandName => $models) {
            $brand = Brands::where('nombre', $brandName)->first();

            if ($brand) {
                foreach ($models as $modelName) {
                    CarModels::firstOrCreate([
                        'id_marca' => $brand->id,
                        'nombre' => $modelName
                    ]);
                }
            }
        }
    }
}
