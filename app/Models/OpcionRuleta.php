<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'color', 'tamano', 'orden'])]
class OpcionRuleta extends Model
{
    protected $table = 'opciones_ruleta';

    /**
     * Options in the order they are drawn on the wheel.
     *
     * @param  Builder<OpcionRuleta>  $query
     */
    public function scopeOrdenadas(Builder $query): void
    {
        $query->orderBy('orden')->orderBy('id');
    }
}
