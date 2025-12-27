<?php

namespace ShreyaSarker\Activitylog\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use ShreyaSarker\Activitylog\Models\ActivityLog;
use ShreyaSarker\Activitylog\Tests\Support\TestUser;
use ShreyaSarker\Activitylog\Tests\TestCase;

class ActivityLoggingTest extends TestCase
{
    public function test_activity_logs_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('activity_logs'));
    }

    public function test_it_can_store_an_activity_log(): void
    {
        $user = TestUser::factory()->create();

        activity()
            ->event('test.event')
            ->causedBy($user)
            ->withProperties(['foo' => 'bar'])
            ->log('Test activity');

        $this->assertDatabaseCount('activity_logs', 1);

        $log = ActivityLog::query()->first();

        $this->assertNotNull($log);
        $this->assertSame('test.event', $log->event);
        $this->assertSame('Test activity', $log->description);
        $this->assertSame(['foo' => 'bar'], $log->properties);

        $this->assertSame(TestUser::class, $log->causer_type);
        $this->assertSame($user->id, $log->causer_id);
    }

    public function test_it_can_associate_a_subject_model(): void
    {
        $user = TestUser::factory()->create();
        $subject = TestUser::factory()->create();

        activity()
            ->event('subject.test')
            ->performedOn($subject)
            ->causedBy($user)
            ->log('Subject attached');

        $log = ActivityLog::query()->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertSame(TestUser::class, $log->subject_type);
        $this->assertSame($subject->id, $log->subject_id);
    }
}
