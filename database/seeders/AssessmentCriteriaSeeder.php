<?php

namespace Database\Seeders;

use App\Models\AssessmentCriterion;
use Illuminate\Database\Seeder;

class AssessmentCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'name' => 'Originalitas Penelitian',
                'description' => 'Tingkat kebaruan dan orisinalitas ide penelitian',
                'max_score' => 100,
                'weight_percentage' => 20,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Metodologi Penelitian',
                'description' => 'Ketepatan dan kesesuaian metodologi yang digunakan',
                'max_score' => 100,
                'weight_percentage' => 20,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Analisis dan Hasil',
                'description' => 'Kualitas analisis data dan kejelasan hasil penelitian',
                'max_score' => 100,
                'weight_percentage' => 25,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Penulisan dan Presentasi',
                'description' => 'Kualitas penulisan, tata bahasa, dan sistematika laporan',
                'max_score' => 100,
                'weight_percentage' => 20,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Kontribusi Keilmuan',
                'description' => 'Kontribusi penelitian terhadap pengembangan ilmu pengetahuan',
                'max_score' => 100,
                'weight_percentage' => 15,
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($criteria as $criterion) {
            AssessmentCriterion::create($criterion);
        }
    }
}
