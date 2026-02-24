<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    // Scopes

    public function scopeForModel($query, string $type, ?int $id = null)
    {
        $query->where('model_type', $type);
        if ($id) {
            $query->where('model_id', $id);
        }
        return $query;
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    // Static Methods

    /**
     * Record an audit log entry for a model action.
     *
     * @param string $action The action performed (created, updated, deleted, exported, imported)
     * @param mixed $model The Eloquent model instance
     * @param array|null $oldValues Previous attribute values
     * @param array|null $newValues New attribute values
     * @return self
     */
    public static function record(string $action, $model, ?array $oldValues = null, ?array $newValues = null): self
    {
        $label = null;
        if (method_exists($model, 'getAuditLabel')) {
            $label = $model->getAuditLabel();
        } elseif (isset($model->name)) {
            $label = $model->name;
        } elseif (isset($model->title)) {
            $label = $model->title;
        } elseif (isset($model->subject)) {
            $label = $model->subject;
        }

        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => class_basename($model),
            'model_id' => $model->id ?? null,
            'model_label' => $label ? Str::limit($label, 255) : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Accessors

    public function getShortModelTypeAttribute(): string
    {
        return class_basename($this->model_type);
    }
}
