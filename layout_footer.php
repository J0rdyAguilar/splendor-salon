</div>

<footer class="text-center text-gold mt-16 py-6 border-t border-neutral-800">
    Splendor © <?php echo date("Y"); ?>
</footer>

    <style>
    /* REPARACIÓN DEFINITIVA DEL INPUT DATE */
    input[type="date"] {
        appearance: auto !important;
        -webkit-appearance: auto !important;
        -moz-appearance: auto !important;
        pointer-events: auto !important;
        opacity: 1 !important;
        background-color: #000 !important;
    }
    </style>

    <script>
    const desde = document.getElementById('desde');
    const hasta = document.getElementById('hasta');
    const popup = document.getElementById('popup-calendario');
    const calendario = document.getElementById('calendario');
    const titulo = document.getElementById('titulo-calendario');

    let mesActual = new Date();
    let seleccion = 'desde';

    // Meses en español
    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    // Abrir calendario
    desde.addEventListener('click', () => abrir('desde'));
    hasta.addEventListener('click', () => abrir('hasta'));

    function abrir(campo) {
        seleccion = campo;
        popup.classList.remove('hidden');
        generarCalendario(mesActual);
    }

    // GENERAR CALENDARIO
    function generarCalendario(fechaBase) {
        calendario.innerHTML = "";

        let year = fechaBase.getFullYear();
        let month = fechaBase.getMonth();
        let firstDay = new Date(year, month, 1);
        let lastDay = new Date(year, month + 1, 0);

        // Título del mes en español
        titulo.textContent = `${meses[month]} ${year}`;

        // Días de la semana
        const dias = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
        dias.forEach(d => calendario.innerHTML += `<div class='text-gold font-semibold'>${d}</div>`);

        // Espacios antes del primer día
        let pad = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
        for (let i = 0; i < pad; i++) calendario.innerHTML += "<div></div>";

        // Días del mes
        for (let d = 1; d <= lastDay.getDate(); d++) {
            let fecha = new Date(year, month, d);
            let iso = fecha.toISOString().substring(0,10);

            calendario.innerHTML += `
                <div class="p-3 rounded border border-neutral-700 bg-neutral-800 hover:border-gold cursor-pointer"
                    onclick="seleccionar('${iso}')">
                    ${d}
                </div>
            `;
        }
    }

    // Seleccionar fecha
    function seleccionar(f) {
        document.getElementById(seleccion).value = f;
    }

    // Navegar meses
    document.getElementById("prevMes").addEventListener("click", () => {
        mesActual = new Date(mesActual.getFullYear(), mesActual.getMonth() - 1, 1);
        generarCalendario(mesActual);
    });

    document.getElementById("sigMes").addEventListener("click", () => {
        mesActual = new Date(mesActual.getFullYear(), mesActual.getMonth() + 1, 1);
        generarCalendario(mesActual);
    });

    // Botones rápidos
    document.querySelectorAll("[data-range]").forEach(btn => {
        btn.addEventListener("click", () => {
            let h = new Date(), d, a;

            switch(btn.dataset.range) {
                case "hoy":
                    d = a = h;
                    break;
                case "ayer":
                    d = a = new Date(h.getFullYear(), h.getMonth(), h.getDate() - 1);
                    break;
                case "7":
                    a = h;
                    d = new Date(h.getFullYear(), h.getMonth(), h.getDate() - 7);
                    break;
                case "mes":
                    d = new Date(h.getFullYear(), h.getMonth(), 1);
                    a = h;
                    break;
                case "mespasado":
                    d = new Date(h.getFullYear(), h.getMonth() - 1, 1);
                    a = new Date(h.getFullYear(), h.getMonth(), 0);
                    break;
            }

            desde.value = d.toISOString().substring(0,10);
            hasta.value = a.toISOString().substring(0,10);
        });
    });
    </script>
</body>
</html>
