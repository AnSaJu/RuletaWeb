const SVG_NS = 'http://www.w3.org/2000/svg';

export const RADIO = 100;

const punto = (grados, radio) => {
    const rad = (grados * Math.PI) / 180;

    return [radio * Math.sin(rad), -radio * Math.cos(rad)];
};

const crear = (nombre, atributos = {}) => {
    const el = document.createElementNS(SVG_NS, nombre);
    Object.entries(atributos).forEach(([clave, valor]) => el.setAttribute(clave, valor));

    return el;
};

/**
 * Dibuja los segmentos dentro del grupo SVG `rueda`. El angulo de cada uno es
 * proporcional a su `tamano`. Los angulos se miden en grados, en sentido
 * horario y empezando desde la flecha (arriba).
 */
export function dibujarRueda(rueda, opciones) {
    rueda.replaceChildren();

    const total = opciones.reduce((suma, opcion) => suma + Number(opcion.tamano), 0);
    let inicio = 0;

    return opciones.map((opcion) => {
        const angulo = (360 * Number(opcion.tamano)) / total;
        const fin = inicio + angulo;
        const [x0, y0] = punto(inicio, RADIO);
        const [x1, y1] = punto(fin, RADIO);

        const grupo = crear('g');
        const trazo = crear('path', {
            d: `M0,0 L${x0},${y0} A${RADIO},${RADIO} 0 ${angulo > 180 ? 1 : 0} 1 ${x1},${y1} Z`,
            fill: opcion.color,
            stroke: '#0f172a',
            'stroke-width': 1.5,
        });

        // En segmentos angostos el texto se lee a lo largo del radio para que quepa.
        const radial = angulo < 70;
        const medio = inicio + angulo / 2;
        const [tx, ty] = punto(medio, RADIO * (radial ? 0.58 : 0.62));
        const disponible = radial
            ? RADIO * 0.75
            : 2 * RADIO * 0.62 * Math.sin((Math.min(angulo, 180) * Math.PI) / 360);
        const tamanoTexto = Math.min(radial ? 8 : 9, disponible / (Math.max(opcion.nombre.length, 1) * 0.62));

        const texto = crear('text', {
            x: tx,
            y: ty,
            transform: `rotate(${radial ? medio - 90 : medio} ${tx} ${ty})`,
            'text-anchor': 'middle',
            'dominant-baseline': 'middle',
            fill: '#0f172a',
            'font-size': tamanoTexto,
            'font-weight': 700,
        });
        texto.textContent = opcion.nombre;

        grupo.append(trazo, texto);
        rueda.append(grupo);

        const segmento = { opcion, inicio, fin, trazo, grupo };
        inicio = fin;

        return segmento;
    });
}

/**
 * La flecha esta fija arriba (0 grados): devuelve el segmento de la rueda que
 * queda justo debajo de ella cuando la rueda ha girado `angulo` grados.
 */
export function segmentoBajoLaFlecha(segmentos, angulo) {
    const enLaRueda = (360 - (angulo % 360)) % 360;

    return segmentos.find((s) => enLaRueda >= s.inicio && enLaRueda < s.fin) ?? segmentos[segmentos.length - 1];
}
