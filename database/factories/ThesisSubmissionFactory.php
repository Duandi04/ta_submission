<?php

namespace Database\Factories;

use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThesisSubmission>
 */
class ThesisSubmissionFactory extends Factory
{
    /**
     * Realistic Indonesian thesis titles by research field
     */
    protected static array $thesisTitles = [
        'Artificial Intelligence' => [
            'Implementasi Algoritma Deep Learning untuk Klasifikasi Penyakit Tanaman Padi Berbasis Citra Digital',
            'Sistem Pengenalan Wajah Menggunakan Convolutional Neural Network untuk Absensi Karyawan',
            'Pengembangan Chatbot Berbasis Natural Language Processing untuk Layanan Informasi Akademik',
            'Deteksi Objek Real-Time Menggunakan YOLO untuk Sistem Keamanan Kampus',
            'Prediksi Harga Saham Menggunakan Long Short-Term Memory Neural Network',
        ],
        'Web Development' => [
            'Perancangan dan Implementasi Sistem Informasi Manajemen Perpustakaan Berbasis Web',
            'Pengembangan Portal E-Learning dengan Fitur Gamifikasi untuk Meningkatkan Motivasi Belajar',
            'Sistem Informasi Geografis Pemetaan Potensi Wisata Daerah Berbasis WebGIS',
            'Implementasi Progressive Web App untuk Sistem Pemesanan Makanan Online',
            'Pengembangan Marketplace UMKM dengan Integrasi Payment Gateway',
        ],
        'Mobile Development' => [
            'Aplikasi Mobile Pemantauan Kesehatan Ibu Hamil Berbasis Android',
            'Pengembangan Aplikasi Pengelolaan Keuangan Pribadi dengan Fitur OCR untuk Scan Struk',
            'Sistem Informasi Pariwisata Berbasis Augmented Reality pada Platform Android',
            'Aplikasi Mobile Learning Bahasa Daerah dengan Metode Gamifikasi',
            'Pengembangan Aplikasi Deteksi Dini Bencana Alam Berbasis IoT dan Mobile',
        ],
        'Internet of Things' => [
            'Rancang Bangun Sistem Smart Home Berbasis IoT dengan Kontrol Suara',
            'Implementasi Sistem Monitoring Kualitas Air Sungai Berbasis Internet of Things',
            'Pengembangan Sistem Irigasi Otomatis untuk Pertanian Berbasis IoT dan Machine Learning',
            'Smart Parking System Menggunakan Sensor Ultrasonik dan Aplikasi Mobile',
            'Sistem Pemantauan Suhu dan Kelembaban Gudang Penyimpanan Berbasis IoT',
        ],
        'Data Science' => [
            'Analisis Sentimen Media Sosial terhadap Kebijakan Pemerintah Menggunakan Text Mining',
            'Clustering Pelanggan E-Commerce Menggunakan Algoritma K-Means untuk Strategi Pemasaran',
            'Prediksi Tingkat Kelulusan Mahasiswa Menggunakan Algoritma Random Forest',
            'Analisis Big Data untuk Optimalisasi Rute Transportasi Publik',
            'Sistem Rekomendasi Film Berbasis Collaborative Filtering dan Content-Based Filtering',
        ],
    ];

    protected static array $abstracts = [
        'Penelitian ini bertujuan untuk mengembangkan dan mengimplementasikan sistem yang dapat meningkatkan efisiensi dan efektivitas dalam penyelesaian masalah yang ada. Metode yang digunakan dalam penelitian ini adalah metode pengembangan sistem dengan pendekatan waterfall yang terdiri dari tahap analisis kebutuhan, perancangan sistem, implementasi, dan pengujian. Hasil penelitian menunjukkan bahwa sistem yang dikembangkan mampu memberikan solusi yang optimal dengan tingkat akurasi yang tinggi. Pengujian dilakukan menggunakan metode black-box testing dan user acceptance testing dengan hasil yang memuaskan.',
        'Perkembangan teknologi informasi yang pesat mendorong kebutuhan akan sistem yang lebih cerdas dan efisien. Penelitian ini mengusulkan sebuah pendekatan baru dalam menyelesaikan permasalahan yang dihadapi dengan memanfaatkan teknologi terkini. Metodologi yang digunakan meliputi studi literatur, pengumpulan data, analisis, perancangan, implementasi, dan evaluasi sistem. Hasil evaluasi menunjukkan peningkatan performa yang signifikan dibandingkan dengan metode konvensional yang ada sebelumnya.',
        'Sistem informasi memiliki peran penting dalam mendukung proses bisnis dan pengambilan keputusan. Penelitian ini fokus pada pengembangan sistem yang dapat mengotomatisasi proses-proses yang sebelumnya dilakukan secara manual. Pengembangan sistem dilakukan menggunakan framework modern dengan arsitektur yang scalable dan maintainable. Pengujian sistem dilakukan secara komprehensif meliputi unit testing, integration testing, dan system testing.',
    ];

    protected static array $researchFields = [
        'Artificial Intelligence',
        'Web Development',
        'Mobile Development',
        'Internet of Things',
        'Data Science',
        'Cyber Security',
        'Cloud Computing',
        'Computer Networks',
        'Software Engineering',
        'Human Computer Interaction',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $researchField = fake()->randomElement(self::$researchFields);
        $titles = self::$thesisTitles[$researchField] ?? self::$thesisTitles['Web Development'];
        
        return [
            'student_id' => User::factory(),
            'supervisor_id' => User::factory(),
            'title' => fake()->randomElement($titles),
            'abstract' => fake()->randomElement(self::$abstracts),
            'research_field' => $researchField,
            'status' => 'draft',
            'submission_date' => null,
            'defense_date' => null,
            'notes' => null,
            'final_score' => null,
        ];
    }

    /**
     * Status States
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'submission_date' => null,
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'submission_date' => now()->subDays(rand(1, 7)),
        ]);
    }

    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
            'submission_date' => now()->subDays(rand(7, 14)),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'submission_date' => now()->subDays(rand(21, 30)),
        ]);
    }

    public function scheduledForDefense(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled_for_defense',
            'submission_date' => now()->subDays(rand(30, 45)),
            'defense_date' => now()->addDays(rand(7, 14)),
        ]);
    }

    public function defenseInProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'defense_in_progress',
            'submission_date' => now()->subMonths(2),
            'defense_date' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'submission_date' => now()->subMonths(rand(2, 6)),
            'defense_date' => now()->subDays(rand(7, 30)),
            'final_score' => fake()->randomFloat(2, 70, 100),
            'notes' => 'Sidang selesai dengan baik.',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'submission_date' => now()->subMonths(rand(1, 3)),
            'notes' => 'Pengajuan dibatalkan atas permintaan mahasiswa.',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'submission_date' => now()->subDays(rand(14, 30)),
            'notes' => 'Proposal tidak memenuhi kriteria kelayakan. Silakan ajukan topik baru.',
        ]);
    }

    /**
     * Configure the factory to use specific student and supervisor
     */
    public function forStudent(User $student): static
    {
        return $this->state(fn (array $attributes) => [
            'student_id' => $student->id,
        ]);
    }

    public function forSupervisor(User $supervisor): static
    {
        return $this->state(fn (array $attributes) => [
            'supervisor_id' => $supervisor->id,
        ]);
    }
}
