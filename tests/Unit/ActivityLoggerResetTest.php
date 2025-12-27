<?php

namespace ShreyaSarker\Activitylog\Tests\Unit;

use ShreyaSarker\Activitylog\Models\ActivityLog;
use ShreyaSarker\Activitylog\Tests\TestCase;

class ActivityLoggerResetTest extends TestCase
{
    public function test_logger_resets_state_after_logging(): void
    {
        $logger = activity();

        $logger->event('first')->withProperties(['n' => 1])->log('First');
        $logger->event('second')->log('Second');

        $this->assertDatabaseCount('activity_logs', 2);

        $first = ActivityLog::query()->orderBy('id')->first();
        $second = ActivityLog::query()->orderByDesc('id')->first();

        $this->assertSame('first', $first->event);
        $this->assertSame(['n' => 1], $first->properties);

        $this->assertSame('second', $second->event);
        $this->assertSame([], $second->properties ?? []);
    }
}
