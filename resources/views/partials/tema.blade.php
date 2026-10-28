<style>
    :root {
        --lp-fondo: {{ $lp['color_fondo'] }};
        --lp-texto: {{ $lp['color_texto'] }};
        --lp-acento-1: {{ $lp['color_acento_1'] }};
        --lp-acento-2: {{ $lp['color_acento_2'] }};
        --lp-texto-boton: {{ \App\Support\LandingPage::colorTextoBoton($lp['color_acento_1'], $lp['color_acento_2']) }};
    }
</style>
