<?php

namespace App\Models\Concerns;

use App\Models\Campus;
use App\Support\CampusContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

trait BelongsToCampus
{
    public static function bootBelongsToCampus(): void
    {
        static::addGlobalScope('campus', function (Builder $builder): void {
            $campusId = app(CampusContext::class)->id()
                ?? Campus::query()->where('slug', config('campus.default_slug'))->value('id');

            if ($campusId !== null) {
                $builder->where($builder->getModel()->qualifyColumn('campus_id'), $campusId);
            }
        });

        static::creating(function ($model): void {
            if ($model->campus_id !== null) {
                return;
            }

            $campusId = app(CampusContext::class)->id()
                ?? Campus::query()->where('slug', config('campus.default_slug'))->value('id');

            if ($campusId === null) {
                throw new LogicException('A campus must exist before tenant records can be created.');
            }

            $model->campus_id = $campusId;
        });
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }
}
