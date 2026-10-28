<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administrador inicial
    |--------------------------------------------------------------------------
    |
    | Datos con los que AdminUserSeeder crea o actualiza al administrador del
    | panel. Se definen en el archivo .env para no guardar contrasenas en el
    | codigo.
    |
    */

    'name' => env('ADMIN_NAME', 'Administrador'),

    'email' => env('ADMIN_EMAIL'),

    'password' => env('ADMIN_PASSWORD'),

];
