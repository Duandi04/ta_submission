# Dokumentasi Fitur Similarity Check (Pengecekan Kemiripan)

## Deskripsi Umum
Fitur **Similarity Check** adalah sistem deteksi kemiripan judul Tugas Akhir/Skripsi yang bertujuan untuk membantu Dosen Pembimbing dan Penguji dalam mengidentifikasi potensi duplikasi, plagiarisme, atau tumpang tindih topik antar mahasiswa. 

Sistem ini membandingkan judul dari pengajuan mahasiswa yang sedang diperiksa dengan seluruh data pengajuan yang sudah ada di dalam database (yang tidak berstatus 'draft').

---

## 1. Cara Kerja Algoritma

Sistem ini menggunakan fungsi internal PHP `similar_text()` yang didasarkan pada algoritma **Programming Classics: Implementing Useful Algorithms in C** oleh Oliver (1993).

### Karakteristik Algoritma:
- **Case-Insensitive**: Sistem mengubah semua input menjadi huruf kecil (`strtolower`) sebelum dibandingkan.
- **Urutan Karakter**: Algoritma ini mencari substring terpanjang yang sama secara berurutan (*longest common substring*) secara rekursif (bukan subsequence).
- **Skor Persentase**:
  $$ \text{Kemiripan} = \frac{\text{Jumlah Karakter Sama} \times 2}{\text{Total Panjang String 1} + \text{Total Panjang String 2}} \times 100 $$
- **Kelebihan**: Sangat efektif untuk membandingkan judul pendek hingga menengah tanpa memerlukan library eksternal yang berat.

### Ilustrasi Contoh Perhitungan

**Judul A (Input):** *"Sistem Informasi Penjualan Berbasis Web"* (39 karakter)  
**Judul B (DB):** *"Sistem Informasi Penjualan Barang Berbasis Web"* (46 karakter)

1. **Normalisasi**: Keduanya diubah menjadi huruf kecil (*lowercase*).
2. **Pencarian Karakter Sama**:
   - Kata *"sistem informasi penjualan "* ada di keduanya (26 karakter).
   - Kata *" berbasis web"* ada di keduanya (13 karakter).
   - Total karakter yang sama = 26 + 13 = **39**.
3. **Penerapan Rumus**:
   - Total panjang string A + B = 39 + 46 = **85**.
   - Perhitungan: $(39 \times 2) \div 85 \times 100 = \mathbf{91.76\%}$
4. **Hasil**: Karena 91.76% > 50% (threshold), maka Judul B akan muncul sebagai "Judul Serupa" dengan indikator warna **Merah** di portal dosen.

---

## 2. Alur Pengecekan (Step-by-Step)

Proses pengecekan dilakukan melalui tahapan berikut:

1. **Input**: Mahasiswa mengetik judul pada form pengajuan (minimal 5 karakter).
2. **Debounce**: Sistem menunggu selama 500ms setelah ketikan terakhir untuk menghindari terlalu banyak request ke server.
3. **AJAX Request**: Frontend mengirimkan judul ke endpoint `/similarity/check`.
4. **Query Database**: Backend mengambil data pengajuan yang sudah ada (kecuali draft).
5. **Komparasi Loop**:
   - Sistem melakukan perulangan (*looping*) pada setiap data di database.
   - Menghitung skor kemiripan antara input dan data di database.
6. **Filtering**: Hasil dengan skor di bawah ambang batas (*threshold* 50%) dibuang.
7. **Sorting**: Hasil diurutkan dari yang paling mirip (persentase tertinggi).
8. **Feedback**: Hasil dikirim kembali ke browser dan ditampilkan secara real-time.

---

## 3. Integrasi Code & AJAX

### A. Frontend (Javascript)
Menggunakan **Fetch API** dengan teknik *debounce* untuk efisiensi performa.

```javascript
// Debounce logic
let timeout = null;
titleInput.addEventListener('input', () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        // Kirim AJAX ke server
        fetch('/similarity/check', {
            method: 'POST',
            body: JSON.stringify({ title: inputTitle }),
            headers: { 'X-CSRF-TOKEN': token }
        }).then(res => res.json())
          .then(data => updateUI(data));
    }, 500);
});
```


### B. Controller (`SimilarityController.php`)
Menerima request dan memanggil logic di Helper.

```php
public function check(Request $request) {
    $similarSubmissions = SimilarityHelper::findSimilarSubmissions(
        $request->title, 
        50, // threshold
        $request->exclude_id
    );
    return response()->json(['data' => $similarSubmissions]);
}
```


### C. Backend Logic (`SimilarityHelper.php`)
Inti dari perbandingan dilakukan secara server-side.

```php
foreach ($allSubmissions as $submission) {
    // lowercase comparison
    similar_text(strtolower($title), strtolower($submission->title), $percent);
    
    if ($percent >= $threshold) {
        // Tambahkan ke koleksi hasil
    }
}
```

---

## 4. Implementasi Teknis Dasar

Logika utama berada pada `App\Helpers\SimilarityHelper::findSimilarSubmissions`.

### 4.1. Parameter Helper
  - `title` (string): Judul yang akan diperiksa.
  - `threshold` (int, default 50): Batas minimum persentase kemiripan untuk ditampilkan.
  - `excludeId` (int, opsional): ID pengajuan yang dikecualikan dari pencarian.
- **Output**: Koleksi (*Collection*) objek hasil kemiripan yang diurutkan dari persentase tertinggi.


### 4.2. API Endpoint
Tersedia endpoint API yang dapat digunakan oleh sisi frontend untuk pengecekan real-time:
- **Route Name**: `similarity.check`
- **Method**: `POST`
- **URL**: `/similarity/check`
- **Payload**:
  ```json
  {
      "title": "Judul Skripsi Contoh",
      "exclude_id": 123
  }
  ```
- **Response**: Mengembalikan jumlah total temuan dan 10 data teratas yang paling mirip.

---

## 5. Penggunaan pada Portal Dosen

Dosen dapat melihat informasi kemiripan ini pada saat melakukan **Penilaian (Assessment)**.

### 5.1. Tampilan pada Halaman Penilaian
Pada bagian kanan atau bawah formulir penilaian, terdapat panel **"Judul Serupa"** yang menampilkan:
- **Judul Terkait**: Judul-judul lain yang dianggap mirip.
- **Nama Mahasiswa**: Pemilik judul terkait tersebut.
- **Waktu Pengajuan**: Kapan judul terkait tersebut diajukan.
- **Indikator Persentase**:
  - <span style="color:red">**Merah (>= 70%)**</span>: Indikasi kemiripan sangat tinggi, perlu perhatian khusus.
  - <span style="color:orange">**Kuning (>= 40%)**</span>: Indikasi kemiripan sedang, perlu ditinjau jika topiknya identik.
  - <span style="color:green">**Hijau (< 40%)**</span>: Kemiripan rendah, kemungkinan hanya menggunakan kata-kata umum yang sama.

---

## 6. Manfaat bagi Pengguna
1. **Integritas Akademik**: Menjaga agar tidak ada topik yang benar-benar sama persis digunakan kembali tanpa modifikasi signifikan.
2. **Efisiensi Dosen**: Memudahkan dosen penguji dalam mencari referensi tugas akhir terdahulu yang sejenis untuk perbandingan kualitas.
3. **Data Real-time**: Pengecekan dilakukan langsung ke database terbaru, menjamin data yang ditampilkan selalu mutakhir.

---
> [!NOTE]
> Fitur ini hanya melakukan pengecekan pada **Judul**. Pengecekan isi dokumen (file PDF) dilakukan secara manual oleh dosen atau menggunakan perangkat lunak eksternal seperti Turnitin jika diperlukan.
