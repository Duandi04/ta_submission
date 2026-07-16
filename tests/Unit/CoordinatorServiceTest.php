<?php

namespace Tests\Unit;

use App\Models\ThesisSubmission;
use App\Services\Coordinator\CoordinatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoordinatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CoordinatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CoordinatorService();
    }

    public function test_get_all_submissions(): void
    {
        ThesisSubmission::factory()->count(12)->create();

        $result = $this->service->getAllSubmissions(5);

        $this->assertEquals(12, $result->total());
        $this->assertCount(5, $result->items());
    }

    public function test_get_submission(): void
    {
        $submission = ThesisSubmission::factory()->create();

        $result = $this->service->getSubmission($submission);

        $this->assertEquals($submission->id, $result->id);
        $this->assertTrue($result->relationLoaded('student'));
        $this->assertTrue($result->relationLoaded('supervisor'));
        $this->assertTrue($result->relationLoaded('files'));
    }
}
