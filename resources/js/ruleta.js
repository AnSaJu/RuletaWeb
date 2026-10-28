import { dibujarRueda, segmentoBajoLaFlecha } from './ruleta-rueda';

const PAUSA_ANTES_DE_ENVIAR = 1500; // ms para que el participante vea el resultado

const contenedor = document.getElementById('ruleta');

if (contenedor) {
    const opciones = JSON.parse(contenedor.dataset.opciones);
    const velocidad = 360 / Number(contenedor.dataset.segundosPorVuelta); // grados por segundo

    const rueda = document.getElementById('rueda');
    const boton = document.getElementById('detener');
    const resultado = document.getElementById('resultado');
    const formulario = document.getElementById('form-ruleta');
    const campoOpcion = document.getElementById('campo-opcion');

    const segmentos = dibujarRueda(rueda, opciones);

    let angulo = Math.random() * 360;
    let anterior = null;
    let girando = true;

    const dibujar = () => rueda.setAttribute('transform', `rotate(${angulo})`);

    const girar = (ahora) => {
        if (! girando) {
            return;
        }

        const delta = anterior === null ? 0 : Math.min((ahora - anterior) / 1000, 0.05);
        anterior = ahora;
        angulo = (angulo + velocidad * delta) % 360;
        dibujar();

        requestAnimationFrame(girar);
    };

    boton.addEventListener('click', () => {
        if (! girando) {
            return;
        }

        girando = false;
        boton.disabled = true;

        const elegido = segmentoBajoLaFlecha(segmentos, angulo);

        dibujar();
        elegido.trazo.setAttribute('stroke', '#ffffff');
        elegido.trazo.setAttribute('stroke-width', 3);
        rueda.append(elegido.grupo); // al frente, para que el borde blanco no quede tapado

        resultado.textContent = `Elegiste: ${elegido.opcion.nombre}`;
        resultado.classList.remove('hidden');

        campoOpcion.value = elegido.opcion.id;
        setTimeout(() => formulario.submit(), PAUSA_ANTES_DE_ENVIAR);
    });

    dibujar();
    requestAnimationFrame(girar);
}
