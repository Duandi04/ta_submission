<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Comment;
use App\Models\ProgramStudi;
use App\Models\Rubric;
use App\Models\SubmissionFile;
use App\Models\ThesisStatus;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThesisSubmissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Realistic Indonesian thesis data
     */
    protected array $thesisData = [
        // DRAFT submissions (2)
        [
            'title' => 'Implementasi Algoritma Deep Learning untuk Klasifikasi Penyakit Tanaman Padi Berbasis Citra Digital',
            'abstract' => 'Penelitian ini bertujuan untuk mengembangkan sistem klasifikasi penyakit tanaman padi menggunakan teknik deep learning. Metode yang digunakan adalah Convolutional Neural Network (CNN) dengan arsitektur transfer learning. Dataset yang digunakan terdiri dari 5000 citra daun padi dengan 4 kategori penyakit. Hasil pengujian awal menunjukkan akurasi sebesar 92%.',
            'research_field' => 'Artificial Intelligence',
            'status' => 'draft',
        ],
        [
            'title' => 'Perancangan Sistem Monitoring Kualitas Air Sungai Berbasis Internet of Things',
            'abstract' => 'Penelitian ini mengusulkan rancangan sistem monitoring kualitas air sungai secara real-time menggunakan teknologi IoT. Sistem akan mengukur parameter pH, suhu, kekeruhan, dan kadar oksigen terlarut. Data akan ditampilkan pada dashboard berbasis web dan aplikasi mobile.',
            'research_field' => 'Internet of Things',
            'status' => 'draft',
        ],
        
        // SUBMITTED submissions (2)
        [
            'title' => 'Pengembangan Aplikasi Mobile E-Learning dengan Fitur Gamifikasi untuk Siswa Sekolah Menengah',
            'abstract' => 'Aplikasi e-learning ini dikembangkan untuk meningkatkan motivasi belajar siswa melalui elemen gamifikasi seperti poin, badge, dan leaderboard. Aplikasi dibangun menggunakan framework Flutter dengan backend Laravel. Fitur utama meliputi video pembelajaran, kuis interaktif, dan forum diskusi.',
            'research_field' => 'Mobile Development',
            'status' => 'submitted',
        ],
        [
            'title' => 'Analisis Sentimen Ulasan Produk E-Commerce Menggunakan Metode BERT',
            'abstract' => 'Penelitian ini melakukan analisis sentimen terhadap ulasan produk e-commerce menggunakan model BERT yang telah di-fine-tune dengan dataset bahasa Indonesia. Hasil analisis digunakan untuk memberikan insight kepada penjual mengenai kepuasan pelanggan.',
            'research_field' => 'Data Science',
            'status' => 'submitted',
        ],

        // UNDER_REVIEW submissions (2)
        [
            'title' => 'Sistem Rekomendasi Wisata Daerah Berbasis Collaborative Filtering dan Content-Based Filtering',
            'abstract' => 'Sistem rekomendasi hybrid ini menggabungkan metode collaborative filtering dan content-based filtering untuk memberikan rekomendasi destinasi wisata yang personalized kepada pengguna. Data wisata diperoleh dari berbagai sumber termasuk review pengguna dan informasi geografis.',
            'research_field' => 'Artificial Intelligence',
            'status' => 'under_review',
        ],
        [
            'title' => 'Implementasi Blockchain untuk Sistem Verifikasi Ijazah Digital',
            'abstract' => 'Penelitian ini mengimplementasikan teknologi blockchain untuk membangun sistem verifikasi ijazah digital yang aman dan transparan. Smart contract digunakan untuk menyimpan dan memverifikasi keaslian ijazah. Sistem dibangun menggunakan platform Ethereum.',
            'research_field' => 'Cyber Security',
            'status' => 'under_review',
        ],

        // REVISION_REQUIRED submissions (2)
        [
            'title' => 'Deteksi Wajah Real-Time Menggunakan YOLO untuk Sistem Absensi Karyawan',
            'abstract' => 'Sistem deteksi wajah ini dikembangkan untuk mengotomatisasi proses absensi karyawan. Menggunakan algoritma YOLO (You Only Look Once) versi 5 dengan optimasi untuk perangkat edge. Sistem terintegrasi dengan database karyawan dan dapat mengenali wajah dalam berbagai kondisi pencahayaan.',
            'research_field' => 'Artificial Intelligence',
            'status' => 'revision_required',
            'notes' => 'Perbaiki bagian metodologi: jelaskan lebih detail preprocessing citra dan tambahkan perbandingan dengan metode face detection lain.',
        ],
        [
            'title' => 'Pengembangan Progressive Web App untuk Sistem Pemesanan Makanan Online UMKM',
            'abstract' => 'PWA ini dikembangkan untuk membantu UMKM kuliner dalam mengelola pesanan secara online. Fitur utama meliputi katalog menu, keranjang belanja, pembayaran online, dan tracking pesanan. Aplikasi dapat diakses offline dan diinstal layaknya aplikasi native.',
            'research_field' => 'Web Development',
            'status' => 'revision_required',
            'notes' => 'Tambahkan uji performa aplikasi dan bandingkan dengan native app. Sertakan hasil pengujian load testing.',
        ],

        // APPROVED submissions (2)
        [
            'title' => 'Rancang Bangun Smart Home Berbasis IoT dengan Kontrol Suara dan Aplikasi Mobile',
            'abstract' => 'Penelitian ini menghasilkan prototype smart home yang dapat dikontrol melalui perintah suara dan aplikasi mobile. Sistem menggunakan ESP32 sebagai mikrokontroler utama dengan integrasi Google Assistant. Fitur meliputi kontrol lampu, AC, dan monitoring konsumsi listrik.',
            'research_field' => 'Internet of Things',
            'status' => 'approved',
        ],
        [
            'title' => 'Prediksi Harga Saham Menggunakan Long Short-Term Memory (LSTM) Neural Network',
            'abstract' => 'Model LSTM dikembangkan untuk memprediksi pergerakan harga saham di Bursa Efek Indonesia. Data historis 5 tahun digunakan untuk training dengan teknik sliding window. Hasil menunjukkan RMSE yang lebih rendah dibandingkan metode ARIMA tradisional.',
            'research_field' => 'Data Science',
            'status' => 'approved',
        ],

        // SCHEDULED_FOR_DEFENSE submissions (2)
        [
            'title' => 'Sistem Informasi Geografis Pemetaan Fasilitas Kesehatan Berbasis WebGIS',
            'abstract' => 'WebGIS ini menyajikan informasi lokasi dan kapasitas fasilitas kesehatan di wilayah perkotaan. Fitur meliputi pencarian fasilitas terdekat, navigasi, dan informasi layanan. Dibangun menggunakan Leaflet.js dengan data dari OpenStreetMap.',
            'research_field' => 'Web Development',
            'status' => 'scheduled_for_defense',
        ],
        [
            'title' => 'Aplikasi Mobile Augmented Reality untuk Pembelajaran Anatomi Tubuh Manusia',
            'abstract' => 'Aplikasi AR ini dikembangkan sebagai media pembelajaran interaktif anatomi tubuh manusia. Pengguna dapat memindai gambar di buku untuk menampilkan model 3D organ tubuh. Dibangun menggunakan Unity dengan ARCore untuk platform Android.',
            'research_field' => 'Mobile Development',
            'status' => 'scheduled_for_defense',
        ],

        // DEFENSE_IN_PROGRESS submissions (1)
        [
            'title' => 'Chatbot Berbasis Natural Language Processing untuk Layanan Informasi Akademik Universitas',
            'abstract' => 'Chatbot ini dikembangkan untuk menjawab pertanyaan seputar layanan akademik kampus secara otomatis. Menggunakan model Transformer dengan fine-tuning pada dataset FAQ akademik. Terintegrasi dengan sistem informasi akademik untuk memberikan informasi yang akurat.',
            'research_field' => 'Artificial Intelligence',
            'status' => 'defense_in_progress',
        ],

        // COMPLETED submissions (3)
        [
            'title' => 'Sistem Irigasi Otomatis untuk Pertanian Berbasis IoT dan Machine Learning',
            'abstract' => 'Sistem irigasi cerdas ini menggunakan sensor kelembaban tanah dan cuaca untuk mengatur penyiraman secara otomatis. Model machine learning digunakan untuk memprediksi kebutuhan air berdasarkan jenis tanaman dan kondisi lingkungan. Hasil implementasi menunjukkan penghematan air hingga 40%.',
            'research_field' => 'Internet of Things',
            'status' => 'completed',
            'final_score' => 88.50,
        ],
        [
            'title' => 'Clustering Pelanggan E-Commerce Menggunakan K-Means untuk Strategi Pemasaran',
            'abstract' => 'Penelitian ini menerapkan algoritma K-Means untuk segmentasi pelanggan e-commerce berdasarkan perilaku pembelian. Hasil clustering digunakan untuk menyusun strategi pemasaran yang targeted. Analisis RFM (Recency, Frequency, Monetary) digunakan sebagai fitur utama.',
            'research_field' => 'Data Science',
            'status' => 'completed',
            'final_score' => 91.25,
        ],
        [
            'title' => 'Portal E-Learning dengan Fitur Video Conference dan Collaborative Document Editing',
            'abstract' => 'Platform e-learning ini menyediakan fitur video conference terintegrasi dan kolaborasi dokumen real-time. Dibangun menggunakan Next.js dengan WebRTC untuk komunikasi real-time. Fitur meliputi ruang kelas virtual, whiteboard, dan sistem penilaian otomatis.',
            'research_field' => 'Web Development',
            'status' => 'completed',
            'final_score' => 85.75,
        ],

        // CANCELLED submission (1)
        [
            'title' => 'Aplikasi Pendeteksi Berita Hoax Menggunakan Text Mining dan Social Network Analysis',
            'abstract' => 'Aplikasi ini dikembangkan untuk mendeteksi berita hoax yang tersebar di media sosial. Menggunakan kombinasi text mining untuk analisis konten dan social network analysis untuk melihat pola penyebaran. Dataset dikumpulkan dari berbagai platform media sosial Indonesia.',
            'research_field' => 'Data Science',
            'status' => 'cancelled',
            'notes' => 'Pengajuan dibatalkan karena mahasiswa mengundurkan diri dari program studi.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programStudis = ProgramStudi::all();
        
        if ($programStudis->isEmpty()) {
            $this->command->warn('No Program Studi found. Please run ProgramStudiSeeder first.');
            return;
        }

        $rubric = Rubric::first();

        foreach ($this->thesisData as $data) {
            $prodi = $programStudis->random();
            
            // Get a student from this prodi
            $student = User::whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'))
                ->where('program_studi_id', $prodi->id)
                ->inRandomOrder()
                ->first();

            // Get a supervisor (dosen) from this prodi
            $supervisor = User::whereHas('roles', fn($q) => $q->whereIn('name', ['dosen', 'kaprodi']))
                ->where('program_studi_id', $prodi->id)
                ->inRandomOrder()
                ->first();

            if (!$student || !$supervisor) {
                $this->command->warn("Skipping thesis '{$data['title']}' - no student or supervisor found for prodi {$prodi->name}");
                continue;
            }

            // Create the thesis submission
            $thesis = ThesisSubmission::create([
                'student_id' => $student->id,
                'supervisor_id' => $supervisor->id,
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'research_field' => $data['research_field'],
                'status' => $data['status'],
                'submission_date' => $this->getSubmissionDate($data['status']),
                'defense_date' => $this->getDefenseDate($data['status']),
                'notes' => $data['notes'] ?? null,
                'final_score' => $data['final_score'] ?? null,
            ]);

            // Create proposal file for non-draft submissions
            if ($data['status'] !== 'draft') {
                SubmissionFile::factory()
                    ->proposal()
                    ->forThesis($thesis)
                    ->uploadedBy($student)
                    ->create();
            }

            // Create status history
            $this->createStatusHistory($thesis, $supervisor);

            // Create comments for reviewed submissions
            if (in_array($data['status'], ['under_review', 'revision_required', 'approved', 'scheduled_for_defense', 'defense_in_progress', 'completed'])) {
                $this->createComments($thesis, $student, $supervisor);
            }

            // Create assessments for completed submissions
            if ($data['status'] === 'completed') {
                $this->createAssessments($thesis, $supervisor, $rubric);
            }

            $this->command->info("Created thesis: {$data['title']} ({$data['status']})");
        }

        $this->command->info('ThesisSubmissionSeeder completed successfully!');
    }

    /**
     * Get submission date based on status
     */
    protected function getSubmissionDate(string $status): ?\DateTime
    {
        return match ($status) {
            'draft' => null,
            'submitted' => now()->subDays(rand(1, 7)),
            'under_review' => now()->subDays(rand(7, 14)),
            'revision_required' => now()->subDays(rand(14, 21)),
            'approved' => now()->subDays(rand(21, 30)),
            'scheduled_for_defense' => now()->subDays(rand(30, 45)),
            'defense_in_progress' => now()->subMonths(2),
            'completed' => now()->subMonths(rand(2, 6)),
            'cancelled' => now()->subMonths(rand(1, 3)),
            default => now(),
        };
    }

    /**
     * Get defense date based on status
     */
    protected function getDefenseDate(string $status): ?\DateTime
    {
        return match ($status) {
            'scheduled_for_defense' => now()->addDays(rand(7, 14)),
            'defense_in_progress' => now(),
            'completed' => now()->subDays(rand(7, 30)),
            default => null,
        };
    }

    /**
     * Create status history for thesis
     */
    protected function createStatusHistory(ThesisSubmission $thesis, User $changedBy): void
    {
        $statusFlow = [
            'draft' => ['draft'],
            'submitted' => ['draft', 'submitted'],
            'under_review' => ['draft', 'submitted', 'under_review'],
            'revision_required' => ['draft', 'submitted', 'under_review', 'revision_required'],
            'approved' => ['draft', 'submitted', 'under_review', 'approved'],
            'scheduled_for_defense' => ['draft', 'submitted', 'under_review', 'approved', 'scheduled_for_defense'],
            'defense_in_progress' => ['draft', 'submitted', 'under_review', 'approved', 'scheduled_for_defense', 'defense_in_progress'],
            'completed' => ['draft', 'submitted', 'under_review', 'approved', 'scheduled_for_defense', 'defense_in_progress', 'completed'],
            'cancelled' => ['draft', 'submitted', 'cancelled'],
        ];

        $flow = $statusFlow[$thesis->status] ?? ['draft'];
        $previousStatus = null;

        foreach ($flow as $status) {
            ThesisStatus::create([
                'thesis_submission_id' => $thesis->id,
                'changed_by' => $changedBy->id,
                'old_status' => $previousStatus,
                'new_status' => $status,
                'comment' => $this->getStatusComment($status),
            ]);
            $previousStatus = $status;
        }
    }

    /**
     * Get comment for status change
     */
    protected function getStatusComment(string $status): string
    {
        return match ($status) {
            'draft' => 'Proposal baru dibuat.',
            'submitted' => 'Proposal telah diajukan untuk ditinjau.',
            'under_review' => 'Sedang dalam proses peninjauan oleh pembimbing.',
            'revision_required' => 'Diperlukan revisi sesuai catatan pembimbing.',
            'approved' => 'Proposal disetujui, siap untuk dijadwalkan sidang.',
            'scheduled_for_defense' => 'Sidang telah dijadwalkan.',
            'defense_in_progress' => 'Sidang sedang berlangsung.',
            'completed' => 'Sidang selesai dengan sukses. Selamat!',
            'cancelled' => 'Pengajuan dibatalkan.',
            default => 'Status diubah.',
        };
    }

    /**
     * Create comments for thesis
     */
    protected function createComments(ThesisSubmission $thesis, User $student, User $supervisor): void
    {
        // Supervisor comment
        $supervisorComment = Comment::factory()
            ->forThesis($thesis)
            ->fromUser($supervisor)
            ->fromSupervisor()
            ->create();

        // Student reply
        Comment::factory()
            ->forThesis($thesis)
            ->fromUser($student)
            ->studentReply()
            ->replyTo($supervisorComment)
            ->create();
    }

    /**
     * Create assessments for completed thesis
     */
    protected function createAssessments(ThesisSubmission $thesis, User $supervisor, ?Rubric $rubric): void
    {
        // Supervisor assessment
        Assessment::factory()
            ->forThesis($thesis)
            ->forEvaluator($supervisor)
            ->supervisor()
            ->submitted()
            ->create([
                'rubric_id' => $rubric?->id,
                'rubric_snapshot' => $rubric?->criteria,
            ]);

        // Find other lecturers for examiner assessments
        $examiners = User::whereHas('roles', fn($q) => $q->whereIn('name', ['dosen', 'kaprodi']))
            ->where('id', '!=', $supervisor->id)
            ->inRandomOrder()
            ->take(2)
            ->get();

        foreach ($examiners as $index => $examiner) {
            Assessment::factory()
                ->forThesis($thesis)
                ->forEvaluator($examiner)
                ->state(['evaluator_type' => $index === 0 ? 'examiner_1' : 'examiner_2'])
                ->submitted()
                ->create([
                    'rubric_id' => $rubric?->id,
                    'rubric_snapshot' => $rubric?->criteria,
                ]);
        }
    }
}
