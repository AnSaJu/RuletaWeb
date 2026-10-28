const formulario = document.getElementById('form-landing');

if (formulario) {
    const vista = document.getElementById('vista-previa');
    const previa = (nombre) => vista.querySelector(`[data-previa="${nombre}"]`);
    const valor = (nombre) => formulario.elements[nombre].value;

    // Misma regla que App\Support\LandingPage::colorTextoBoton().
    const luminosidad = (hex) => {
        const [r, g, b] = [1, 3, 5].map((i) => parseInt(hex.slice(i, i + 2), 16));

        return (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    };

    const colorTextoBoton = (a, b) => ((luminosidad(a) + luminosidad(b)) / 2 > 0.55 ? '#0f172a' : '#ffffff');

    const texto = (nombre, contenido) => {
        const el = previa(nombre);
        el.textContent = contenido;
        el.hidden = contenido.trim() === '';
    };

    const titulo = (contenido) => {
        const el = previa('titulo');
        el.replaceChildren();

        contenido.split(/\*([^*]+)\*/).forEach((parte, i) => {
            if (parte === '') {
                return;
            }

            if (i % 2 === 1) {
                const resaltado = document.createElement('span');
                resaltado.className = 'tema-texto-degradado';
                resaltado.textContent = parte;
                el.append(resaltado);
            } else {
                el.append(parte);
            }
        });
    };

    const beneficios = (contenido) => {
        const lista = previa('beneficios');
        lista.replaceChildren();

        const lineas = contenido.split(/\r?\n/).map((linea) => linea.trim()).filter(Boolean);
        lista.hidden = lineas.length === 0;

        lineas.forEach((linea) => {
            const item = document.createElement('li');
            item.className = 'flex items-center gap-2';

            const marca = document.createElement('span');
            marca.className = 'tema-check flex h-4 w-4 shrink-0 items-center justify-center rounded-full text-[10px]';
            marca.textContent = '✓';

            item.append(marca, linea);
            lista.append(item);
        });
    };

    // El logo que se muestra: el archivo recien elegido o, si no hay, el que ya estaba guardado.
    const logo = previa('logo');
    const logoGuardado = logo.getAttribute('src') ?? '';
    let logoNuevo = null;

    const actualizarLogo = () => {
        const quitar = formulario.elements.quitar_logo?.checked ?? false;
        const origen = logoNuevo ?? logoGuardado;

        logo.hidden = quitar && ! logoNuevo || origen === '';

        if (origen !== '') {
            logo.src = origen;
        }
    };

    formulario.elements.logo.addEventListener('change', (evento) => {
        if (logoNuevo) {
            URL.revokeObjectURL(logoNuevo);
        }

        const archivo = evento.target.files[0];
        logoNuevo = archivo ? URL.createObjectURL(archivo) : null;
        actualizarLogo();
        previa('marca').hidden = logo.hidden && previa('nombre_principal').hidden;
    });

    const actualizar = () => {
        const [fondo, textoColor, acento1, acento2] = ['color_fondo', 'color_texto', 'color_acento_1', 'color_acento_2'].map(valor);

        vista.style.setProperty('--lp-fondo', fondo);
        vista.style.setProperty('--lp-texto', textoColor);
        vista.style.setProperty('--lp-acento-1', acento1);
        vista.style.setProperty('--lp-acento-2', acento2);
        vista.style.setProperty('--lp-texto-boton', colorTextoBoton(acento1, acento2));

        ['color_fondo', 'color_texto', 'color_acento_1', 'color_acento_2'].forEach((nombre) => {
            formulario.querySelector(`[data-valor="${nombre}"]`).textContent = valor(nombre);
        });

        texto('nombre_principal', valor('nombre_principal'));
        actualizarLogo();
        previa('marca').hidden = logo.hidden && previa('nombre_principal').hidden;

        texto('insignia', valor('insignia'));
        titulo(valor('titulo'));
        texto('descripcion', valor('descripcion'));
        beneficios(valor('beneficios'));
        texto('formulario_titulo', valor('formulario_titulo'));
        texto('formulario_subtitulo', valor('formulario_subtitulo'));
        texto('boton_texto', valor('boton_texto'));
    };

    formulario.addEventListener('input', actualizar);
    actualizar();
}
