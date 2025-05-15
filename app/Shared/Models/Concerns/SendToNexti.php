<?php

namespace App\Shared\Models\Concerns;

use App\Shared\Enums\TypeEnum;
use App\Shared\Enums\SituationEnum;
use Illuminate\Database\Eloquent\Builder;

trait SendToNexti
{
    /**
     * Initialize the send to nexti trait for an instance.
     *
     * @return void
     */
    public function initializeSendToNexti(): void
    {
        if (!isset($this->casts['TIPO'])) {
            $this->casts['TIPO'] = TypeEnum::class;
        }

        if (!isset($this->casts['SITUACAO'])) {
            $this->casts['SITUACAO'] = SituationEnum::class;
        }
    }

    /**
     * Scope a query to only Pendents
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeIsPendent(Builder $query): void
    {
        $query->whereIn('SITUACAO', [
            SituationEnum::Pendent->value,
            SituationEnum::Error->value
        ]);
    }

    /**
     * Scope a query to only Pendents to Create
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeIsPendentToCreate(Builder $query): void
    {
        $query->where('TIPO', TypeEnum::Create->value)
                ->isPendent();
    }

    /**
     * Scope a query to only Pendents to Update
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeIsPendentToUpdate(Builder $query): void
    {
        $query->where('TIPO', TypeEnum::Update->value)
                ->isPendent();
    }

    /**
     * Scope a query to only Pendents to Delete
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeIsPendentToDelete(Builder $query): void
    {
        $query->where('TIPO', TypeEnum::Delete->value)
                ->isPendent();
    }
}
