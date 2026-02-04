<?php

namespace Database\Factories;

use App\Models\ThesisStatus;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThesisStatus>
 */
class ThesisStatusFactory extends Factory
{
    protected static array $statusComments = [
        'draft' => [
            'Proposal baru dibuat.',
            'Masih dalam proses penulisan.',
        ],
        'submitted' => [
            'Proposal telah diajukan untuk ditinjau.',
            'Dokumen sudah dikirim untuk review.',
        ],
        'under_review' => [
            'Sedang dalam proses peninjauan oleh pembimbing.',
            'Proposal sedang direview.',
        ],
        'revision_required' => [
            'Diperlukan revisi sesuai catatan pembimbing.',
            'Silakan perbaiki sesuai saran yang diberikan.',
        ],
        'approved' => [
            'Proposal disetujui, siap untuk dijadwalkan sidang.',
            'Dokumen telah memenuhi syarat.',
        ],
        'scheduled_for_defense' => [
            'Sidang telah dijadwalkan.',
            'Jadwal sidang sudah ditentukan.',
        ],
        'defense_in_progress' => [
            'Sidang sedang berlangsung.',
            'Mahasiswa sedang melaksanakan sidang.',
        ],
        'completed' => [
            'Sidang selesai dengan sukses. Selamat!',
            'Proses TA telah selesai.',
        ],
        'cancelled' => [
            'Pengajuan dibatalkan.',
            'TA dibatalkan atas permintaan mahasiswa.',
        ],
        'rejected' => [
            'Proposal ditolak. Silakan ajukan topik baru.',
            'Tidak memenuhi kriteria, perlu pengajuan ulang.',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thesis_submission_id' => ThesisSubmission::factory(),
            'changed_by' => User::factory(),
            'old_status' => null,
            'new_status' => 'draft',
            'comment' => 'Status awal pengajuan.',
        ];
    }

    /**
     * Create a status transition
     */
    public function transition(string $oldStatus, string $newStatus): static
    {
        $comments = self::$statusComments[$newStatus] ?? ['Status diubah.'];
        
        return $this->state(fn (array $attributes) => [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => fake()->randomElement($comments),
        ]);
    }

    /**
     * For specific thesis
     */
    public function forThesis(ThesisSubmission $thesis): static
    {
        return $this->state(fn (array $attributes) => [
            'thesis_submission_id' => $thesis->id,
        ]);
    }

    /**
     * Changed by specific user
     */
    public function changedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'changed_by' => $user->id,
        ]);
    }
}
