<?php

namespace App\Models;

use Database\Factories\ParticipanteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'cedula', 'direccion', 'celular'])]
class Participante extends Model
{
    /** @use HasFactory<ParticipanteFactory> */
    use HasFactory;
}
