<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['clave', 'valor'])]
class Ajuste extends Model
{
    public const SEGUNDOS_POR_VUELTA = 'segundos_por_vuelta';

    public const SEGUNDOS_POR_VUELTA_INICIAL = 2.0;

    public $incrementing = false;

    protected $primaryKey = 'clave';

    protected $keyType = 'string';

    /**
     * Seconds the wheel takes to complete one turn.
     */
    public static function segundosPorVuelta(): float
    {
        $valor = static::query()->whereKey(self::SEGUNDOS_POR_VUELTA)->value('valor');

        return $valor === null ? self::SEGUNDOS_POR_VUELTA_INICIAL : (float) $valor;
    }

    public static function guardarSegundosPorVuelta(float $segundos): void
    {
        static::updateOrCreate(
            ['clave' => self::SEGUNDOS_POR_VUELTA],
            ['valor' => (string) $segundos],
        );
    }
}
