<?php
use App\Models\User;
use App\Models\Rubric;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Get a Kaprodi
$kaprodi = User::role('kaprodi')->first();
Auth::login($kaprodi);

echo "Logged in as Kaprodi: " . $kaprodi->name . " (Prodi ID: " . $kaprodi->program_studi_id . ")\n";

// Get rubric 1
$rubric = Rubric::find(1);
echo "Rubric 1 Prodi ID: " . ($rubric->program_studi_id ?? 'NULL') . "\n";

try {
    $service = new \App\Services\Kaprodi\KaprodiService();
    echo "Attempting to update rubric 1...\n";
    $service->updateRubric(1, ['name' => 'Test Update']);
    echo "Success!\n";
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    echo "Caught ModelNotFoundException (will result in 404)\n";
} catch (\Exception $e) {
    echo "Caught Exception: " . $e->getMessage() . "\n";
}
