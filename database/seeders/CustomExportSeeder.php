<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Faculty;
use App\Models\ProgramStudi;
use App\Models\ThesisSubmission;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CustomExportSeeder extends Seeder
{
    public function run(): void
    {
        // Call necessary base seeders
        $this->call([
            RolePermissionSeeder::class,
        ]);



        // Create Admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@ta.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Fakultas and Prodi
        $faculty = Faculty::create([
            'code' => 'FKOM',
            'name' => 'Fakultas Komputer'
        ]);

        $prodi = ProgramStudi::create([
            'code' => 'TPL',
            'name' => 'Teknik Perangkat Lunak',
            'faculty_id' => $faculty->id
        ]);

        // Create Kaprodi TPL
        $kaprodi = User::create([
            'name' => 'Kaharuddin, S.Kom., M.Kom',
            'email' => 'kaharuddin@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => 'D001',
            'is_active' => true,
            'program_studi_id' => $prodi->id,
        ]);
        $kaprodi->assignRole('kaprodi');
        $kaprodi->assignRole('dosen');

        // Create Dosen TPL
        $dosen1 = User::create([
            'name' => 'Ilwan Syafrinal, S.Kom., M.Kom',
            'email' => 'ilwan@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => 'D002',
            'is_active' => true,
            'program_studi_id' => $prodi->id,
        ]);
        $dosen1->assignRole('dosen');

        $dosen2 = User::create([
            'name' => 'Eka Lia Febrianti, S.Kom., M.Kom.',
            'email' => 'ekalia@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => 'D003',
            'is_active' => true,
            'program_studi_id' => $prodi->id,
        ]);
        $dosen2->assignRole('dosen');

        $dosen3 = User::create([
            'name' => 'Masparudin, S.Kom., M.Kom.',
            'email' => 'masparudin@ta.test',
            'password' => Hash::make('password'),
            'nim_nip' => 'D004',
            'is_active' => true,
            'program_studi_id' => $prodi->id,
        ]);
        $dosen3->assignRole('dosen');

        $dosens = [
            'Ilwan Syafrinal, S.Kom., M.Kom' => $dosen1->id,
            'Kaharuddin, S.Kom., M.Kom' => $kaprodi->id,
            'Eka Lia Febrianti, S.Kom., M.Kom.' => $dosen2->id,
            'Masparudin, S.Kom., M.Kom.' => $dosen3->id,
            'Eka Lia Febrianti' => $dosen2->id,
        ];

        // Specific proposals and comments
        $proposals = [
            [
                'nim' => '2022133011',
                'name' => 'Alex Ferguson',
                'title' => 'Perancangan Sistem Monitoring Maintenance Sarana dan Prasarana di Universitas Universal Berbasis Web',
                'comment' => 'Di Latar belakang isinya tidak boleh diambil dari Jurnal semua, perlu dijabarkan permasalahan dari hasil wawancara atau observasi.- Data pendukung: perlu ditambahkan data kuantitatif tentang jumlah sarana prasarana yang dikelola UVERS (misalnya jumlah kelas, proyektor, AC), agar urgensi penelitian lebih terasa.- Rumusan masalah dan tujuan: masih cenderung umum, bisa dibuat lebih spesifik, misalnya “Bagaimana sistem dapat menghasilkan laporan otomatis kondisi fasilitas?” atau “Bagaimana sistem memberikan notifikasi jadwal maintenance?”.- Ruang lingkup sistem: sudah ada batasan fitur, tetapi sebaiknya dijelaskan lebih rinci fitur utama (dashboard monitoring, riwayat maintenance, notifikasi).-Dampak yang diuraikan sudah baik. Untuk memperkuat, proposal bisa menambahkan estimasi kuantitatif, misalnya "Diharapkan dapat mengurangi waktu pelaporan dari beberapa jam menjadi beberapa menit" atau "Meningkatkan akurasi data inventaris aset hingga 99%".',
                'dospem1' => 'Ilwan Syafrinal, S.Kom., M.Kom',
                'dospem2' => null
            ],
            [
                'nim' => '2022133001',
                'name' => 'Calvin Whenjaya',
                'title' => 'Perancangan Aplikasi Pendeteksi Makanan Cemilan Vegetarian Yang Tidak Sehat Berbasis Image Recognition Untuk Membantu Pola Hidup Sehat',
                'comment' => '-',
                'dospem1' => 'Masparudin, S.Kom., M.Kom.',
                'dospem2' => 'Eka Lia Febrianti'
            ],
            [
                'nim' => '2022133012',
                'name' => 'Duandi',
                'title' => 'Rancang Bangun Sistem Pengajuan Proposal Tugas Akhir Berbasis Web Menggunakan Metode Agile SCRUM',
                'comment' => 'Identifikasi Permasalahan 1. Tidak efisien pada pembuatan dokumen spreadsheet untuk penilaian setiap judul yang diajukan Mahasiswa 2. Sulit dalam melakukan peniliaian 3. Keamanan data karena data dapat diakses oleh user lain. 4. Data tidak terpusat mempersulit dalam memberikan informasi kepada mahasiswa.- Pastikan bahwa penerapan metodologi Agile Scrum dapat menjawab kebutuhan pengembangan yang fleksibel dan iteratif, serta memberikan cukup ruang untuk perubahan sesuai umpan balik dari pemangku kepentingan.- Apakah dampak dari inefisiensi ini cukup signifikan? Dapatkah contoh konkret diberikan, seperti waktu yang terbuang atau masalah dalam pencatatan dan pelacakan data yang pernah terjadi?',
                'dospem1' => 'Ilwan Syafrinal, S.Kom., M.Kom',
                'dospem2' => null
            ],
            [
                'nim' => '2022133002',
                'name' => 'Dustin Walter Lim',
                'title' => 'Perancangan Game Eksplorasi Interaktif Ruang Galeri Seni Universitas Universal Sebagai Media Promosi',
                'comment' => '-',
                'dospem1' => 'Kaharuddin, S.Kom., M.Kom',
                'dospem2' => null
            ],
            [
                'nim' => '2022133007',
                'name' => 'Edison',
                'title' => 'Rancang Bangun Website Penilaian Konsumsi Gula pada Minuman dengan Sistem Grade dan Rekomendasi',
                'comment' => 'Harus lebih jelas algoritma yang digunakan untuk dapat merekomendasikan konsumsi gula harian pengguna.- Validasi Sistem: perlu dijelaskan bagaimana sistem grading akan diuji (misalnya, mengacu pada standar WHO atau Kemenkes tentang asupan gula).- Ruang Lingkup: tambahkan batasan agar tidak melebar, misalnya hanya fokus pada minuman kemasan populer di Indonesia. Jelaskan dasar atau acuan untuk algoritma grading-nya (misal: "diadaptasi dari standar Kemenkes RI atau standar WHO").',
                'dospem1' => 'Eka Lia Febrianti, S.Kom., M.Kom.',
                'dospem2' => null
            ],
            [
                'nim' => '2022133014',
                'name' => 'Fariwati',
                'title' => 'Perancangan Game Edukatif  Sebagai Media Pembelajaran Interaktif Dalam Pengenalan Tabel Unsur Periodik Terhadap Siswa SMA',
                'comment' => '1. Detailkan Metode Evaluasi: Pada tahap Beta Testing, jelaskan secara spesifik bagaimana efektivitas edukatif akan diukur. Sangat disarankan untuk menggunakan metode pre-test dan post-test kepada siswa untuk mendapatkan data kuantitatif tentang peningkatan pemahaman mereka setelah bermain game.  2. Jelaskan Konsep Game: Untuk memberikan gambaran lebih jelas, bisa ditambahkan satu atau dua kalimat pada bagian Inisiasi  mengenai genre atau konsep inti game (misalnya: puzzle, kuis, petualangan, dll).',
                'dospem1' => 'Kaharuddin, S.Kom., M.Kom',
                'dospem2' => null
            ],
            [
                'nim' => '2022133003',
                'name' => 'Hadi Susanto',
                'title' => 'Rancang Bangun Aplikasi Tour Guide Pintar Berbasis Lokasi dengan Rekomendasi Tempat Otomatis di Batam.',
                'comment' => 'Rekomendasi harus menggunakan algoritma yang tepat agar sesuai dengan profil pengguna- Ruang Lingkup: masih umum, sebaiknya lebih detail fitur yang akan dikembangkan (misalnya: integrasi GPS, sistem rekomendasi berbasis preferensi, informasi budaya lokal).- Evaluasi Aplikasi: perlu dijelaskan bagaimana efektivitas aplikasi akan diuji (misalnya melalui usability testing, SUS score, atau uji kepuasan pengguna).- Integrasi Budaya Lokal: karena pemandu wisata juga berfungsi sebagai mediator budaya, sebaiknya dijelaskan apakah aplikasi akan mencakup informasi etika, budaya, dan bahasa lokal.- Inovasi Teknis: agar berbeda dari aplikasi peta biasa (Google Maps), perlu ditonjolkan fitur pembeda, misalnya rekomendasi otomatis berbasis machine learning, AR untuk navigasi, atau chatbot interaktif. Sebutkan Teknologi: Cantumkan technology stack yang jelas. Contoh: "Aplikasi akan dikembangkan secara native untuk Android menggunakan Kotlin, memanfaatkan Google Maps API untuk fitur lokasi, dan Firebase sebagai backend.". Serta Jelaskan Konsep Rekomendasi: Beri gambaran awal tentang cara kerja sistem rekomendasi (misalnya, berbasis popularitas, berbasis kategori preferensi pengguna, dll).',
                'dospem1' => 'Eka Lia Febrianti, S.Kom., M.Kom.',
                'dospem2' => null
            ],
            [
                'nim' => '2022133006',
                'name' => 'Herman',
                'title' => 'Perancangan Sistem Informasi Manajemen Servis Berbasis Web Untuk Meningkatkan Efisiensi Pelacakan Pekerjaan Pada CV ITC',
                'comment' => 'Penulisan rumusan masalah harus mencakup semua yang ada pada identifikasi masalah Pertimbangkan Metodologi Agile: Disarankan untuk mempertimbangkan metode Agile. Mengingat pengguna sistem (admin dan teknisi) dapat memberikan feedback yang berharga selama pengembangan, pendekatan iteratif Agile akan lebih efektif dalam memastikan sistem benar-benar sesuai dengan kebutuhan alur kerja mereka dibandingkan Waterfall.',
                'dospem1' => 'Eka Lia Febrianti, S.Kom., M.Kom.',
                'dospem2' => null
            ],
            [
                'nim' => '2022133005',
                'name' => 'Khenjy Johnelson',
                'title' => 'Perancangan Penjadwalan Sistem Informasi Akademik di Universitas Universal',
                'comment' => '-',
                'dospem1' => 'Masparudin, S.Kom., M.Kom.',
                'dospem2' => 'Eka Lia Febrianti'
            ],
            [
                'nim' => '2022133010',
                'name' => 'Lily',
                'title' => 'Aplikasi Mobile “E-Disiplin” Sebagai Media Pencatatan Dan Pemantauan Poin Pelanggaran Tata Tertib Mahasiswa Di Universitas Universal',
                'comment' => 'Harus melakukan wawancara atau observasi terhadap stakeholder kemahasiswaan untuk mendapatkan data yang lengkap - Tambahkan Metodenya.- Sebutkan Teknologi: Proposal harus mencantumkan technology stack yang akan dipakai. Contoh: "Aplikasi akan dibangun dengan Kotlin untuk Android, dan menggunakan Firebase sebagai backend untuk notifikasi real-time.',
                'dospem1' => 'Masparudin, S.Kom., M.Kom.',
                'dospem2' => 'Eka Lia Febrianti'
            ],
            [
                'nim' => '2022133013',
                'name' => 'Richard',
                'title' => 'Perancangan Sistem Inventory Stok Barang Berbasis Database Website untuk Optimalisasi Manajemen Persediaan Stok',
                'comment' => 'Harus jelas objek penelitian dilakukan dimana- Permasalahan penelitian tidak jelas dan didapat dari mana Secara keseluruhan Isi Proposal harus di pertajam lagi. Mahasiswa harus menetapkan sebuah studi kasus yang nyata, yang masih menggunakan pencatatan manual dan bersedia menjadi objek penelitian. Selanjutnya, menjelaskan secara spesifik bagaimana sistem ini akan memberikan dampak positif bagi perusahaan atau organisasi tersebut. Bagaimana sistem akan dirancang, dibangun, dan diuji. Urgensi harus dikaitkan langsung dengan studi kasus. Setelah studi kasus ditemukan, jelaskan masalah mendesak apa yang sedang mereka hadapi akibat sistem manual (misalnya, sering kehilangan barang, kesulitan audit, dll). Proposal ini harus direvisi secara fundamental dan menyeluruh. Mahasiswa harus melengkapi semua bagian yang hilang, terutama bab metodologi pengembangan perangkat lunak, dan mengaitkan seluruh isi proposal dengan sebuah studi kasus yang konkret.',
                'dospem1' => 'Ilwan Syafrinal, S.Kom., M.Kom',
                'dospem2' => null
            ],
            [
                'nim' => '2022133008',
                'name' => 'Steven Tang',
                'title' => 'Rancang Bangun Website StudyStrip Sebagai Media Pembelajaran Interaktif Berbasis Komik Digital',
                'comment' => 'Disarankan untuk fokus pada satu jenjang pendidikan (misalnya, siswa SMP) dan satu mata pelajaran (misalnya, Sejarah atau Biologi). Hal ini akan membuat proyek lebih terarah, dapat dicapai, dan hasil pengujiannya lebih bermakna.',
                'dospem1' => 'Eka Lia Febrianti, S.Kom., M.Kom.',
                'dospem2' => null
            ],
            [
                'nim' => '2022133017',
                'name' => 'Venessya Calista',
                'title' => 'Perancangan Game Visual Novel Interaktif di Platform Roblox sebagai Media Edukasi Pencegahan Bullying dan Kekerasan Verbal',
                'comment' => '-',
                'dospem1' => 'Kaharuddin, S.Kom., M.Kom',
                'dospem2' => null
            ],
            [
                'nim' => '2022133018',
                'name' => 'Vito Timothi',
                'title' => 'Pengembangan Sistem Informasi Berbasis Website Untuk Manajemen Dan Publikasi Event Di Uvers',
                'comment' => '-',
                'dospem1' => 'Ilwan Syafrinal, S.Kom., M.Kom',
                'dospem2' => null
            ],
            [
                'nim' => '2022133004',
                'name' => 'Vivie Triyanti',
                'title' => 'Rancang Bangun Game Edukasi Simulasi Deforestasi : Dampak Penebangan Hutan terhadap Banjir',
                'comment' => 'Spesifikasikan Target Pengujian: Jelaskan target pengguna untuk uji coba (misalnya, siswa SMP atau SMA) agar lebih fokus. Sebutkan Teknologi: Proposal ini belum menyebutkan technology stack. Tambahkan detail teknologi yang akan digunakan (misalnya PHP Laravel, MySQL) untuk melengkapi perencanaan. Hilangkan kontradiksi platform. Pilih satu platform target (Android atau Windows) dan pastikan seluruh dokumen konsisten.',
                'dospem1' => 'Kaharuddin, S.Kom., M.Kom',
                'dospem2' => null
            ]
        ];

        foreach ($proposals as $prop) {
            $student = User::firstOrCreate(
                ['nim_nip' => $prop['nim']],
                [
                    'name' => $prop['name'],
                    'email' => strtolower(str_replace(' ', '', $prop['name'])) . '@ta.test',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'program_studi_id' => $prodi->id,
                ]
            );
            $student->assignRole('mahasiswa');

            $dospem1Id = $dosens[$prop['dospem1']] ?? null;
            $dospem2Id = $prop['dospem2'] ? ($dosens[$prop['dospem2']] ?? null) : null;

            $thesis = ThesisSubmission::create([
                'student_id' => $student->id,
                'supervisor_id' => $dospem1Id,
                'supervisor_2_id' => $dospem2Id,
                'title' => $prop['title'],
                'abstract' => 'Abstrak untuk ' . $prop['title'],
                'research_field' => 'Teknik Perangkat Lunak',
                'status' => 'approved', // accepted proposal
                'submission_date' => now()->subDays(rand(1, 30)),
            ]);

            if ($prop['comment'] !== '-') {
                Comment::create([
                    'thesis_submission_id' => $thesis->id,
                    'user_id' => $kaprodi->id, // Assuming Kaprodi adds the comment
                    'content' => $prop['comment']
                ]);
            }
        }
    }
}
