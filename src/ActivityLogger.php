<?php

namespace ShreyaSarker\Activitylog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use ShreyaSarker\Activitylog\Models\ActivityLog;

class ActivityLogger
{
    protected ?string $event = null;
    protected ?Model $subject = null;
    protected ?Model $causer = null;
    protected array $properties = [];

    /**
     * Set the event name for this activity log.
     *
     * @param string|null $event
     * @return self
     */
    public function event(?string $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Set the subject model that this activity was performed on.
     *
     * @param Model|null $subject
     * @return self
     */
    public function performedOn(?Model $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set the causer model that caused this activity.
     *
     * @param Model|null $causer
     * @return self
     */
    public function causedBy(?Model $causer): self
    {
        $this->causer = $causer;
        return $this;
    }

    /**
     * Set multiple properties at once.
     *
     * @param array $properties
     * @return self
     */
    public function withProperties(array $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * Add a single property using dot notation.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addProperty(string $key, mixed $value): self
    {
        Arr::set($this->properties, $key, $value);
        return $this;
    }

    /**
     * Log the activity with the given description.
     *
     * @param string $description
     * @return ActivityLog|null Returns null if logging fails silently
     * @throws \Exception If silent mode is disabled
     */
    public function log(string $description): ?ActivityLog
    {
        try {
            // Validate properties can be JSON encoded
            if (!empty($this->properties)) {
                json_encode($this->properties);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException(
                        'Properties cannot be JSON encoded: ' . json_last_error_msg()
                    );
                }
            }

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
        } catch (\Exception $e) {
            // Log the error
            Log::error('Activity log failed: ' . $e->getMessage(), [
                'exception' => $e,
                'description' => $description,
                'event' => $this->event,
            ]);

            // Handle based on config
            if (config('activitylog.silent_failures', true)) {
                return null;
            }

            throw $e;
        } finally {
            // Always reset state
            $this->reset();
        }
    }

    /**
     * Reset the logger state.
     *
     * @return void
     */
    private function reset(): void
    {
        $this->event = null;
        $this->subject = null;
        $this->causer = null;
        $this->properties = [];
    }
}
