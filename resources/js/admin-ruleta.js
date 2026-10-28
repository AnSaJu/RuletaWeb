import { dibujarRueda } from './ruleta-rueda';

const formulario = document.getElementById('form-config-ruleta');

if (formulario) {
    const rueda = document.getElementById('rueda');
    const filas = [...formulario.querySelectorAll('[data-opcion]')];
    const campoSegundos = document.getElementById('segundos_por_vuelta');
    const botonProbar = document.getElementById('probar-velocidad');

    let angulo = 0;
    let girando = false;
    let anterior = null;

    const leerOpciones = () => filas.map((fila) => {
        const tamano = parseInt(fila.querySelector('[data-campo="tamano"]').value, 10);

        return {
            nombre: fila.querySelector('[data-campo="nombre"]').value,
            color: fila.querySelector('[data-campo="color"]').value,
            tamano: tamano > 0 ? tamano : 1,
        };
    });

    const segundosPorVuelta = () => {
        const valor = parseFloat(campoSegundos.value.replace(',', '.'));

        return valor > 0 ? valor : 2;
    };

    const dibujar = () => rueda.setAttribute('transform', `rotate(${angulo})`);

    const actualizar = () => {
        const opciones = leerOpciones();
        const total = opciones.reduce((suma, opcion) => suma + opcion.tamano, 0);

        dibujarRueda(rueda, opciones);
        dibujar();

        filas.forEach((fila, i) => {
            fila.querySelector('[data-porcentaje]').textContent = `${((opciones[i].tamano / total) * 100).toFixed(1).replace('.', ',')} %`;
        });
    };

    const girar = (ahora) => {
        if (! girando) {
            return;
        }

        const delta = anterior === null ? 0 : Math.min((ahora - anterior) / 1000, 0.05);
        anterior = ahora;
        angulo = (angulo + (360 / segundosPorVuelta()) * delta) % 360;
        dibujar();

        requestAnimationFrame(girar);
    };

    botonProbar.addEventListener('click', () => {
        girando = ! girando;
        anterior = null;
        botonProbar.textContent = girando ? 'Detener prueba' : 'Probar velocidad';

        if (girando) {
            requestAnimationFrame(girar);
        }
    });

    formulario.addEventListener('input', actualizar);
    actualizar();
}
