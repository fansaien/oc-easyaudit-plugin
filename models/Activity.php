<?php namespace LukeTowers\EasyAudit\Models;

use Lang;
use Model;

/**
 * Activity Model
 */
class Activity extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model
     */
    public $table = 'luketowers_easyaudit_activities';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'event' => 'required|between:1,255',
    ];

    /**
     * @var array The attributes to protect against mass-assignment
     */
    public $guarded = ['created_at'];

    /**
     * @var array Attribute names to encode and decode using JSON.
     */
    protected $jsonable = ['properties'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];
    const CREATED_AT = 'created_at';

    /**
     * Relations
     */
    public $morphTo = [
        'subject' => [],
        'source'  => [],
    ];

    /**
     * Scope a query to only include activities with the provided event name descriptor
     *
     * @param Builder $query
     * @param mixed $logNames Array or String of event names to search for activities in
     * @return Builder
     */
    public function scopeWithEvent($query, ...$eventNames)
    {
        if (is_array($eventNames[0])) {
            $eventNames = $eventNames[0];
        }

        return $query->whereIn('event', $eventNames);
    }

    /**
     * Scope a query to only include activities for a given subject.
     *
     * @param Builder $query
     * @param Model $subject
     * @return Builder
     */
    public function scopeForSubject($query, $subject)
    {
        return $query
            ->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey());
    }

    /**
     * Scope a query to only include activities by a given source.
     *
     * @param Builder $query
     * @param Model $source
     * @return Builder
     */
    public function scopeFromSource($query, $source)
    {
        return $query
            ->where('source_type', get_class($source))
            ->where('source_id', $source->getKey());
    }

    /**
     * Disable setting the updated_at column automatically as this model doesn't support that column
     *
     * @param mixed $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        return $this;
    }

    /**
     * Get the source name
     *
     * @return string Name of the source for this activity item
     */
    public function getSourceNameAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if ($this->source) {
            return $this->source->full_name;
        } else {
            return Lang::get('luketowers.easyaudit::lang.models.activity.unknown_source');
        }
    }
}
