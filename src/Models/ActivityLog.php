<?php

namespace ShreyaSarker\Activitylog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    protected $fillable = [
        'event',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the table name for the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('activitylog.table', parent::getTable());
    }

    /**
     * Get the subject model that this activity was performed on.
     *
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer model that caused this activity.
     *
     * @return MorphTo
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include logs for a specific event.
     *
     * @param Builder $query
     * @param string $event
     * @return Builder
     */
    public function scopeForEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    /**
     * Scope a query to only include logs caused by a specific model.
     *
     * @param Builder $query
     * @param Model $causer
     * @return Builder
     */
    public function scopeCausedBy(Builder $query, Model $causer): Builder
    {
        return $query->where('causer_type', get_class($causer))
                     ->where('causer_id', $causer->id);
    }

    /**
     * Scope a query to only include logs performed on a specific model.
     *
     * @param Builder $query
     * @param Model $subject
     * @return Builder
     */
    public function scopePerformedOn(Builder $query, Model $subject): Builder
    {
        return $query->where('subject_type', get_class($subject))
                     ->where('subject_id', $subject->id);
    }

    /**
     * Scope a query to only include logs for a specific subject type.
     *
     * @param Builder $query
     * @param string $subjectType
     * @return Builder
     */
    public function scopeForSubjectType(Builder $query, string $subjectType): Builder
    {
        return $query->where('subject_type', $subjectType);
    }

    /**
     * Scope a query to only include logs for a specific causer type.
     *
     * @param Builder $query
     * @param string $causerType
     * @return Builder
     */
    public function scopeForCauserType(Builder $query, string $causerType): Builder
    {
        return $query->where('causer_type', $causerType);
    }
}
