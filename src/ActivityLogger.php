<?php

namespace ShreyaSarker\Activitylog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use ShreyaSarker\Activitylog\Models\ActivityLog;

class ActivityLogger
{
    protected ?string $event = null;
    protected ?Model $subject = null;
    protected ?Model $causer = null;
    protected array $properties = [];

    public function event(?string $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function performedOn(?Model $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function causedBy(?Model $causer): self
    {
        $this->causer = $causer;
        return $this;
    }

    public function withProperties(array $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    public function addProperty(string $key, mixed $value): self
    {
        Arr::set($this->properties, $key, $value);
        return $this;
    }

    public function log(string $description): ActivityLog
    {
        try {
            $log = new ActivityLog();

            $log->event = $this->event;
            $log->description = $description;
            $log->properties = $this->properties;

            if ($this->subject) {
                $log->subject()->associate($this->subject);
            }

            if ($this->causer) {
                $log->causer()->associate($this->causer);
            }

            // Only capture request info when not running in console (tests/CLI)
            if (
                config('activitylog.capture_request', true)
                && ! app()->runningInConsole()
                && app()->bound('request')
            ) {
                $request = app('request');
                $log->ip = $request->ip();
                $log->user_agent = substr((string) $request->userAgent(), 0, 1000);
            }

            $log->save();

            return $log;
        } finally {
            // Reset so the singleton logger doesn't carry state
            $this->event = null;
            $this->subject = null;
            $this->causer = null;
            $this->properties = [];
        }
    }
}
