<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'cedula', 'direccion', 'celular'])]
class Participante extends Model
{
    //
}
