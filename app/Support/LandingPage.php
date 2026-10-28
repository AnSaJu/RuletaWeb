<?php

namespace App\Support;

use App\Models\Ajuste;
use Illuminate\Support\Facades\Storage;

/**
 * Textos y colores de la landing page, editables desde el panel.
 *
 * Los valores se guardan en la tabla `ajustes` con el prefijo "landing.".
 * Lo que no se haya guardado usa el valor predeterminado de este archivo.
 */
class LandingPage
{
    public const PREFIJO = 'landing.';

    /** Disco donde se guarda el logo (public/uploads). */
    public const DISCO = 'uploads';

    /**
     * @return array<string, string>
     */
    public static function predeterminados(): array
    {
        return [
            'meta_titulo' => config('app.name').' · Regístrate y participa',
            'nombre_principal' => config('app.name'),
            'logo' => '',
            'insignia' => '🎡 Gira la ruleta y gana',
            'titulo' => 'Regístrate y *participa* en la ruleta',
            'descripcion' => 'Completa el formulario con tus datos y quedarás inscrito para participar. Solo toma un minuto.',
            'beneficios' => "Registro rápido y sencillo\nUna inscripción por cédula\nTus datos se guardan de forma segura",
            'formulario_titulo' => 'Tus datos',
            'formulario_subtitulo' => 'Todos los campos son obligatorios.',
            'boton_texto' => 'Quiero participar',
            'color_fondo' => '#020617',
            'color_texto' => '#f1f5f9',
            'color_acento_1' => '#fbbf24',
            'color_acento_2' => '#d946ef',
        ];
    }

    /**
     * Current values: what was saved, or the default for anything not saved yet.
     *
     * @return array<string, string>
     */
    public static function valores(): array
    {
        $guardados = Ajuste::query()
            ->where('clave', 'like', self::PREFIJO.'%')
            ->pluck('valor', 'clave');

        $valores = [];

        foreach (self::predeterminados() as $campo => $predeterminado) {
            $valores[$campo] = $guardados->get(self::PREFIJO.$campo, $predeterminado);
        }

        return $valores;
    }

    /**
     * @param  array<string, string|null>  $valores
     */
    public static function guardar(array $valores): void
    {
        foreach (array_keys(self::predeterminados()) as $campo) {
            Ajuste::updateOrCreate(
                ['clave' => self::PREFIJO.$campo],
                ['valor' => (string) ($valores[$campo] ?? '')],
            );
        }
    }

    public static function restaurar(): void
    {
        self::borrarLogo(self::valores()['logo']);

        Ajuste::query()->where('clave', 'like', self::PREFIJO.'%')->delete();
    }

    /**
     * Public URL of the uploaded logo (relative to the current host).
     */
    public static function urlLogo(string $ruta): string
    {
        return asset('uploads/'.$ruta);
    }

    public static function borrarLogo(string $ruta): void
    {
        if ($ruta !== '') {
            Storage::disk(self::DISCO)->delete($ruta);
        }
    }

    /**
     * Split a title written with *highlighted* words into segments.
     *
     * @return list<array{texto: string, resaltado: bool}>
     */
    public static function segmentosDelTitulo(string $titulo): array
    {
        $partes = preg_split('/\*([^*]+)\*/', $titulo, -1, PREG_SPLIT_DELIM_CAPTURE);

        $segmentos = [];

        foreach ($partes as $i => $texto) {
            if ($texto !== '') {
                $segmentos[] = ['texto' => $texto, 'resaltado' => $i % 2 === 1];
            }
        }

        return $segmentos;
    }

    /**
     * @return list<string>
     */
    public static function listaDeBeneficios(string $beneficios): array
    {
        $lineas = array_map('trim', preg_split('/\R/', $beneficios));

        return array_values(array_filter($lineas, fn (string $linea) => $linea !== ''));
    }

    /**
     * Dark or white text, whichever reads better on the accent gradient.
     */
    public static function colorTextoBoton(string $acento1, string $acento2): string
    {
        $luminosidad = (self::luminosidad($acento1) + self::luminosidad($acento2)) / 2;

        return $luminosidad > 0.55 ? '#0f172a' : '#ffffff';
    }

    private static function luminosidad(string $hex): float
    {
        [$r, $g, $b] = sscanf(ltrim($hex, '#'), '%02x%02x%02x');

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    }
}
