<?php

namespace App\Enums;

enum Rol: string
{
    case SuperAdmin = 'super_admin';
    case Administrador = 'administrador';

    public function etiqueta(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super administrador',
            self::Administrador => 'Administrador',
        };
    }

    public function descripcion(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Controla todos los paneles: participantes, ruleta y usuarios.',
            self::Administrador => 'Solo puede ver las personas registradas.',
        };
    }
}
