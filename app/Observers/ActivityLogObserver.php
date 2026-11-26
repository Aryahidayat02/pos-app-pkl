<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogObserver
{
    public function created(Model $model): void
    {
        if (! $this->isAdmin()) {
            return;
        }

        $this->record($model, 'create', [], $this->filterAttributes($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        if (! $this->isAdmin()) {
            return;
        }

        $changedAttributes = $this->filterAttributes($model->getChanges());

        if ($changedAttributes === []) {
            return;
        }

        $changedKeys = array_fill_keys(array_keys($changedAttributes), true);

        $this->record(
            $model,
            'update',
            array_intersect_key($this->filterAttributes($model->getRawOriginal()), $changedKeys),
            array_intersect_key($this->filterAttributes($model->getAttributes()), $changedKeys),
        );
    }

    public function deleted(Model $model): void
    {
        if (! $this->isAdmin()) {
            return;
        }

        $this->record($model, 'delete', $this->filterAttributes($model->getAttributes()), []);
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function filterAttributes(array $attributes): array
    {
        return array_filter(
            $attributes,
            fn (mixed $value, string|int $key): bool => ! in_array($key, ['created_at', 'updated_at'], true)
                && ! preg_match('/password|token|secret|api[_-]?key|remember|two_factor|recovery/i', (string) $key),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    private function record(Model $model, string $action, array $before, array $after): void
    {
        $identity = collect(['name', 'sku', 'invoice_number', 'purchase_number', 'key'])
            ->map(fn (string $attribute): mixed => $model->getAttribute($attribute))
            ->first(fn (mixed $value): bool => is_string($value) && $value !== '');

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => class_basename($model),
            'model_id' => $model->getKey(),
            'description' => Str::headline($action).' '.Str::headline(class_basename($model)).': '.($identity ?? '#'.$model->getKey()),
            'details' => [
                'before' => $before,
                'after' => $after,
            ],
        ]);
    }
}
