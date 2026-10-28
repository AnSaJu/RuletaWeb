#!/usr/bin/env bash
#
# Actualiza la aplicación en el servidor con lo último de GitHub.
# Uso (en el servidor, dentro de la carpeta del proyecto):  bash deploy.sh
#
# Si en el servidor el comando "php" no es la versión correcta, indica la ruta:
#   PHP_BIN=/opt/alt/php84/usr/bin/php bash deploy.sh

set -euo pipefail

cd "$(dirname "$0")"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

paso() { printf '\n\033[1;34m==> %s\033[0m\n' "$1"; }

fallo() {
    printf '\n\033[1;31mEl despliegue falló.\033[0m El sitio quedó en modo mantenimiento para no mostrar errores.\n'
    printf 'Revisa el mensaje de arriba, corrige y vuelve a ejecutar: bash deploy.sh\n'
    printf 'Si necesitas reabrir el sitio sin terminar:  %s artisan up\n' "$PHP_BIN"
}
trap fallo ERR

# Todo va dentro de main() para que bash lea el script completo antes de ejecutarlo:
# así, si "git pull" trae una versión nueva de este archivo, no se mezclan versiones.
main() {
    paso "Versión de PHP"
    "$PHP_BIN" -r 'echo PHP_VERSION, PHP_EOL; exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);' \
        || { echo "Se necesita PHP 8.2 o superior. Usa PHP_BIN=/ruta/a/php bash deploy.sh"; exit 1; }

    paso "Modo mantenimiento"
    "$PHP_BIN" artisan down --retry=15 || true

    paso "Descargando cambios de GitHub"
    git pull --ff-only

    paso "Instalando dependencias de PHP"
    # --no-scripts: Hostinger desactiva proc_open, que Composer necesita para ejecutar los
    # scripts de Laravel; el descubrimiento de paquetes se ejecuta directamente con artisan.
    "$PHP_BIN" "$(command -v "$COMPOSER_BIN")" install --no-dev --optimize-autoloader --no-interaction --no-scripts
    "$PHP_BIN" artisan package:discover

    paso "Actualizando la base de datos"
    "$PHP_BIN" artisan migrate --force

    paso "Enlace de imágenes (public/storage)"
    if [ ! -e public/storage ]; then
        # Con ln y no con "artisan storage:link": Hostinger desactiva symlink() y exec() en PHP.
        ln -s "$(pwd)/storage/app/public" public/storage
        echo "Creado."
    else
        echo "Ya existe."
    fi

    paso "Optimizando"
    "$PHP_BIN" artisan optimize:clear
    "$PHP_BIN" artisan optimize

    paso "Reabriendo el sitio"
    "$PHP_BIN" artisan up

    printf '\n\033[1;32mListo. La aplicación está actualizada.\033[0m\n'
}

main "$@"
exit
