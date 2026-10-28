const SVG_NS = 'http://www.w3.org/2000/svg';
const RADIO = 100;
const VELOCIDAD = 180; // grados por segundo
const PAUSA_ANTES_DE_ENVIAR = 1500; // ms para que el participante vea el resultado

const contenedor = document.getElementById('ruleta');

if (contenedor) {
    const opciones = JSON.parse(contenedor.dataset.opciones);
    const segmento = 360 / opciones.length;

    const rueda = document.getElementById('rueda');
    const boton = document.getElementById('detener');
    const resultado = document.getElementById('resultado');
    const formulario = document.getElementById('form-ruleta');
    const campoOpcion = document.getElementById('campo-opcion');

    const punto = (grados, radio) => {
        const rad = (grados * Math.PI) / 180;

        return [radio * Math.sin(rad), -radio * Math.cos(rad)];
    };

    const crear = (nombre, atributos = {}) => {
        const el = document.createElementNS(SVG_NS, nombre);
        Object.entries(atributos).forEach(([clave, valor]) => el.setAttribute(clave, valor));

        return el;
    };

    const segmentos = opciones.map((opcion, i) => {
        const inicio = i * segmento;
        const fin = inicio + segmento;
        const [x0, y0] = punto(inicio, RADIO);
        const [x1, y1] = punto(fin, RADIO);
        const grande = segmento > 180 ? 1 : 0;

        const grupo = crear('g');
        const trazo = crear('path', {
            d: `M0,0 L${x0},${y0} A${RADIO},${RADIO} 0 ${grande} 1 ${x1},${y1} Z`,
            fill: opcion.color,
            stroke: '#0f172a',
            'stroke-width': 1.5,
        });

        const [tx, ty] = punto(inicio + segmento / 2, RADIO * 0.62);
        const texto = crear('text', {
            x: tx,
            y: ty,
            transform: `rotate(${inicio + segmento / 2} ${tx} ${ty})`,
            'text-anchor': 'middle',
            'dominant-baseline': 'middle',
            fill: '#0f172a',
            'font-size': 9,
            'font-weight': 700,
        });
        texto.textContent = opcion.nombre;

        grupo.append(trazo, texto);
        rueda.append(grupo);

        return trazo;
    });

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
        angulo = (angulo + VELOCIDAD * delta) % 360;
        dibujar();

        requestAnimationFrame(girar);
    };

    // La flecha esta fija arriba (0 grados): se calcula que segmento de la
    // rueda queda justo debajo de ella con el angulo actual.
    const opcionBajoLaFlecha = () => Math.floor(((360 - angulo) % 360) / segmento);

    boton.addEventListener('click', () => {
        if (! girando) {
            return;
        }

        girando = false;
        boton.disabled = true;

        const indice = opcionBajoLaFlecha();

        dibujar();
        segmentos[indice].setAttribute('stroke', '#ffffff');
        segmentos[indice].setAttribute('stroke-width', 3);
        segmentos[indice].parentNode.parentNode.append(segmentos[indice].parentNode);

        resultado.textContent = `Elegiste: ${opciones[indice].nombre}`;
        resultado.classList.remove('hidden');

        campoOpcion.value = indice;
        setTimeout(() => formulario.submit(), PAUSA_ANTES_DE_ENVIAR);
    });

    dibujar();
    requestAnimationFrame(girar);
}
