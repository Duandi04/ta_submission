<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Faculty;
use App\Models\ProgramStudi;
use App\Models\Rubric;
use App\Models\ThesisStatus;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyProposalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['2022133011', 'Alex Ferguson', 'Perancangan Sistem Crowdsourcing Demand Produk untuk Analisis Kebutuhan Pasar di Indonesia Berbasis Web'],
            ['2022133011', 'Alex Ferguson', 'Perancangan Sistem Monitoring Maintenance Sarana dan Prasarana di Universitas Universal Berbasis Web'],
            ['2022133001', 'Calvin Whenjaya', 'PERANCANGAN APLIKASI PENDETEKSI MAKANAN CEMILAN VEGETARIAN YANG TIDAK SEHAT BERBASIS IMAGE RECOGNITION UNTUK MEMBANTU POLA HIDUP SEHAT'],
            ['2022133001', 'Calvin Whenjaya', 'PERANCANGAN SISTEM REKOMENDASI DESTINASI WISATA BERBASIS KEPRIPADIAN & BUDGET PENGGUNA'],
            ['2022133012', 'Duandi', 'RANCANG BANGUN SISTEM E-SURAT DENGAN METODOLOGI AGILE SCRUM DI FAKULTAS KOMPUTER UNIVERSITAS UNIVERSAL (UVERS)'],
            ['2022133012', 'Duandi', 'PERANCANGAN APLIKASI MONITORING, MANAJEMEN, DAN KOLABORASI KEGIATAN HIMPUNAN MAHASISWA UNIVERSITAS UNIVERSAL BERBASIS WEB'],
            ['2022133012', 'Duandi', 'SISTEM REKOMENDASI KARIER BERBASIS DATA AKADEMIK DAN MINAT DENGAN METODE SAW DI UNIVERSITAS UNIVERSAL'],
            ['2022133002', 'Dustin Walter Lim', 'Gamifikasi Kebersihan Lingkungan : Perancangan Game Simulasi untuk Menumbuhkan Kesadaran Peduli Lingkungan'],
            ['2022133007', 'Edison', 'Rancang Bangun Website Penilaian Konsumsi Gula pada Minuman dengan Sistem Grade dan Rekomendasi'],
            ['2022133007', 'Edison', 'Rancang Bangun Website Informasi Perawatan Kulit Berjerawat dengan Fitur Rekomendasi Produk dan Edukasi'],
            ['2022133014', 'Fariwati', 'PERANCANGAN GAME EDUKASI DENGAN PENDEKATAN PROBLEM-BASED LEARNING  UNTUK MATERI GERAK PARABOLA DI SMA'],
            ['2022133014', 'Fariwati', 'PERANCANGAN GAME EDUKATIF  SEBAGAI MEDIA PEMBELAJARAN INTERAKTIF DALAM PENGENALAN TABEL UNSUR PERIODIK TERHADAP SISWA SMA'],
            ['2022133003', 'Hadi Susanto', 'Rancang Bangun Aplikasi Tour Guide Pintar Berbasis Lokasi dengan Rekomendasi Tempat Otomatis di Batam.'],
            ['2022133003', 'Hadi Susanto', 'Rancang Bangun Game Simulasi Petualangan untuk Mencapai Kebebasan Keuangan Pribadi di Kalangan Generasi-Z'],
            ['2022133006', 'Herman', 'PERANCANGAN SISTEM INFORMASI MANAJEMEN SERVIS BERBASIS WEB UNTUK MENINGKATKAN EFISIENSI PELACAKAN PEKERJAAN PADA CV ITC'],
            ['2022133006', 'Herman', 'PERANCANGAN SISTEM MONITORING STOK SPAREPART KOMPUTER BERBASIS WEBSITE PADA CV INNOVATION TECHNOLOGY CENTER'],
            ['2022133005', 'Khenjy Johnelson', 'PERANCANGANAPLIKASI PEMINJAMAN RUANGAN FASILITAS KAMPUS BERBASIS MOBILE DI UNIVERSITAS UNIVERSAL'],
            ['2022133005', 'Khenjy Johnelson', 'PERANCANGAN APLIKASI PENGELOLAAN PRODUK INDUSTRI KREATIF PADA PLUT KUMKM BATAM'],
            ['2022133010', 'Lily', 'APLIKASI MOBILE “E-DISIPLIN” SEBAGAI MEDIA PENCATATAN DAN PEMANTAUAN POIN PELANGGARAN TATA TERTIB MAHASISWA DI UNIVERSITAS UNIVERSAL'],
            ['2022133010', 'Lily', 'RANCANG BANGUN APLIKASI WEB JURNAL HARIAN DENGAN ANALISIS SENTIMEN UNTUK MENDUKUNG KESEHATAN MENTAL MAHASISWA'],
            ['2022133013', 'Richard', 'Perancangan Sistem Inventory Stok Barang Berbasis Database Website untuk Optimalisasi Manajemen Persediaan Stok'],
            ['2022133008', 'Steven Tang', 'Rancang Bangun Website StudyStrip sebagai Media Pembelajaran  Interaktif Berbasis Komik Digital'],
            ['2022133008', 'Steven Tang', 'Perancangan dan Implementasi Sistem Informasi Marketplace Penjualan Aksesori Ponsel Berbasis Web Responsif Axxora'],
            ['2022133017', 'Venessya Calista', 'Pengembangan Aplikasi Gamifikasi Pembelajaran Bahasa Mandarin untuk Meningkatkan Literasi Membaca dan Menulis  bagi Penutur Non-Native'],
            ['2022133017', 'Venessya Calista', 'Perancangan Game Visual Novel Interaktif di Platform Roblox sebagai Media Edukasi Pencegahan Bullying dan Kekerasan Verbal'],
            ['2022133018', 'Vito Timothi', 'PENGEMBANGAN SISTEM INFORMASI BERBASIS WEBSITE UNTUK MANAJEMEN DAN PUBLIKASI EVENT DI UVERS'],
            ['2022133018', 'Vito Timothi', 'PENGEMBANGAN SISTEM PENCATATAN DAN MONITORING DATA PEMINJAMAN SARANA DAN PRASARANA UNIVERSITAS BERBASIS WEB'],
            ['2022133004', 'Vivie Triyanti', 'Rancang Bangun Game Edukasi Simulasi Deforestasi : Dampak Penebangan Hutan terhadap Banjir'],
            ['2022133004', 'Vivie Triyanti', 'Rancang Bangun Gameplay Platformer Berbasis 2D dengan Simulasi Visual Dampak Pengelolaan Sampah'],
        ];

        // Ensure Fakultas Komputer exists
        $faculty = Faculty::firstOrCreate(
            ['name' => 'Komputer'],
            ['code' => 'FKOM']
        );

        // Ensure Program Studi Teknik Perangkat Lunak exists
        $prodi = ProgramStudi::firstOrCreate(
            ['name' => 'Teknik Perangkat Lunak'],
            ['code' => 'TPL', 'faculty_id' => $faculty->id]
        );

        $lecturersData = [
            ['name' => 'Kaharuddin, S.Kom., M.Kom', 'role' => 'kaprodi', 'email' => 'kaharuddin@ta.test', 'nip' => 'D001'],
            ['name' => 'Eka Lia Febrianti, S.Kom, M.Kom', 'role' => 'dosen', 'email' => 'eka.lia@ta.test', 'nip' => 'D002'],
            ['name' => 'Ilwan Syafrinal, S.Kom, M.Kom', 'role' => 'dosen', 'email' => 'ilwan.syafrinal@ta.test', 'nip' => 'D003'],
            ['name' => 'Masparuddin, S.Kom, M.Kom', 'role' => 'dosen', 'email' => 'masparuddin@ta.test', 'nip' => 'D004'],
        ];

        $lecturers = collect();
        foreach ($lecturersData as $ld) {
            $user = User::firstOrCreate(
                ['email' => $ld['email']],
                [
                    'name' => $ld['name'],
                    'password' => Hash::make('password'),
                    'nim_nip' => $ld['nip'],
                    'is_active' => true,
                    'program_studi_id' => $prodi->id,
                ]
            );
            if (!$user->hasRole($ld['role'])) {
                $user->assignRole($ld['role']);
            }
            if (!$user->hasRole('dosen')) {
                $user->assignRole('dosen');
            }
            $lecturers->push($user);
        }

        $rubric = Rubric::where('is_active', true)->first();
        if (!$rubric) {
            $this->command->warn('No active rubric found. Skipping assessments.');
        }

        $statuses = ['draft', 'submitted', 'under_review', 'completed', 'cancelled'];

        foreach ($data as $row) {
            $nim = trim($row[0]);
            $name = trim($row[1]);
            $title = trim($row[2]);

            $email = strtolower(str_replace(' ', '.', explode(' ', $name)[0] . $nim)) . '@ta.test';
            $student = User::firstOrCreate(
                ['nim_nip' => $nim],
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'program_studi_id' => $prodi->id,
                ]
            );

            if (!$student->hasRole('mahasiswa')) {
                $student->assignRole('mahasiswa');
            }

            $supervisor = $lecturers->random();
            $status = $statuses[array_rand($statuses)];

            $thesis = ThesisSubmission::create([
                'student_id' => $student->id,
                'supervisor_id' => $supervisor->id,
                'title' => $title,
                'abstract' => 'Abstrak untuk ' . $title,
                'research_field' => 'Teknik Perangkat Lunak',
                'status' => $status,
                'submission_date' => $this->getSubmissionDate($status),
                'defense_date' => ($status === 'completed') ? now()->subDays(rand(1, 30)) : null,
                'final_score' => ($status === 'completed') ? rand(75, 95) : null,
            ]);

            $this->createStatusHistory($thesis, $supervisor);

            // If under review or completed, it has assessments
            if (($status === 'under_review' || $status === 'completed') && $rubric) {
                $this->createAssessments($thesis, $supervisor, $lecturers, $rubric);
            }
        }

        $this->command->info('Dummy proposals for TPL seeded successfully.');
    }

    protected function getSubmissionDate(string $status): ?\DateTime
    {
        return match ($status) {
            'draft' => null,
            'submitted' => now()->subDays(rand(1, 7)),
            'under_review' => now()->subDays(rand(7, 21)),
            'completed' => now()->subMonths(rand(2, 6)),
            'cancelled' => now()->subMonths(rand(1, 3)),
            default => now(),
        };
    }

    protected function createStatusHistory(ThesisSubmission $thesis, User $changedBy): void
    {
        $statusFlow = [
            'draft' => ['draft'],
            'submitted' => ['draft', 'submitted'],
            'under_review' => ['draft', 'submitted', 'under_review'],
            'completed' => ['draft', 'submitted', 'under_review', 'completed'],
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
                'comment' => 'Status changed to ' . $status,
            ]);
            $previousStatus = $status;
        }
    }

    protected function createAssessments(ThesisSubmission $thesis, User $supervisor, $lecturers, Rubric $rubric): void
    {
        // Find other lecturers for examiner assessments (all lecturers in TPL minus the supervisor)
        $examiners = $lecturers->where('id', '!=', $supervisor->id)->take(2)->values();

        // Evaluators will be supervisor, and 2 examiners
        $evaluatorsList = [
            ['user' => $supervisor, 'type' => 'supervisor'],
        ];
        
        if (isset($examiners[0])) {
            $evaluatorsList[] = ['user' => $examiners[0], 'type' => 'examiner_1'];
        }
        if (isset($examiners[1])) {
            $evaluatorsList[] = ['user' => $examiners[1], 'type' => 'examiner_2'];
        }

        foreach ($evaluatorsList as $evalData) {
            $evaluator = $evalData['user'];
            $type = $evalData['type'];

            $assessment = Assessment::factory()
                ->forThesis($thesis)
                ->forEvaluator($evaluator)
                ->state([
                    'rubric_id' => $rubric->id,
                    'is_submitted' => true,
                    'submitted_at' => now(),
                ])
                ->create();

            $this->seedScores($assessment, $rubric);
        }
    }

    protected function seedScores(Assessment $assessment, Rubric $rubric): void
    {
        if (!isset($rubric->criteria)) {
            return;
        }

        $totalScore = 0;
        foreach ($rubric->criteria as $criterion) {
            $scoreValue = rand(70, 95);
            $weight = $criterion['weight'] ?? ($criterion['weight_percentage'] ?? 0);

            AssessmentScore::create([
                'assessment_id' => $assessment->id,
                'criterion_name' => $criterion['name'],
                'criterion_description' => $criterion['description'] ?? null,
                'weight' => $weight,
                'score' => $scoreValue,
            ]);

            $totalScore += ($scoreValue * $weight) / 100;
        }

        $assessment->update(['total_score' => $totalScore]);
    }
}
