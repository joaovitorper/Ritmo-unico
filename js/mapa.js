document.addEventListener("DOMContentLoaded", () => {

    const mapaElement = document.getElementById("mapa");
    const carregando = document.getElementById("mapa-carregando");
    const statusGPS = document.getElementById("statusGPS");

    const distanciaElement = document.getElementById("distanciaMapa");
    const tempoElement = document.getElementById("tempoMapa");
    const ritmoElement = document.getElementById("ritmoMapa");

    let mapa;
    let marcador;
    let percurso;
    let pontos = [];

    let distanciaTotal = 0;
    let ultimaPosicao = null;

    let tempoInicio = null;
    let intervaloTempo = null;

    // =========================
    // VERIFICAR GPS
    // =========================

    if (!navigator.geolocation) {
        statusGPS.textContent = "❌ GPS não disponível";
        carregando.textContent =
            "Seu navegador não suporta localização.";
        return;
    }

    // =========================
    // CRIAR MAPA
    // =========================

    mapa = L.map("mapa");

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution: "&copy; OpenStreetMap contributors"
        }
    ).addTo(mapa);

    // =========================
    // DISTÂNCIA ENTRE PONTOS
    // =========================

    function calcularDistancia(lat1, lon1, lat2, lon2) {

        const R = 6371;

        const dLat =
            (lat2 - lat1) * Math.PI / 180;

        const dLon =
            (lon2 - lon1) * Math.PI / 180;

        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1 * Math.PI / 180) *
            Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) ** 2;

        const c =
            2 * Math.atan2(
                Math.sqrt(a),
                Math.sqrt(1 - a)
            );

        return R * c;
    }

    // =========================
    // FORMATAR TEMPO
    // =========================

    function formatarTempo(segundos) {

        const horas =
            Math.floor(segundos / 3600);

        const minutos =
            Math.floor((segundos % 3600) / 60);

        const segundosRestantes =
            segundos % 60;

        if (horas > 0) {

            return (
                String(horas).padStart(2, "0") +
                ":" +
                String(minutos).padStart(2, "0") +
                ":" +
                String(segundosRestantes).padStart(2, "0")
            );

        }

        return (
            String(minutos).padStart(2, "0") +
            ":" +
            String(segundosRestantes).padStart(2, "0")
        );
    }

    // =========================
    // ATUALIZAR TEMPO
    // =========================

    function atualizarTempo() {

        if (!tempoInicio) return;

        const agora = Date.now();

        const segundos =
            Math.floor(
                (agora - tempoInicio) / 1000
            );

        tempoElement.textContent =
            formatarTempo(segundos);

        // Ritmo
        if (distanciaTotal > 0) {

            const minutos =
                segundos / 60;

            const ritmo =
                minutos / distanciaTotal;

            const ritmoMin =
                Math.floor(ritmo);

            const ritmoSeg =
                Math.floor(
                    (ritmo - ritmoMin) * 60
                );

            ritmoElement.textContent =
                `${ritmoMin}:${String(ritmoSeg).padStart(2, "0")} /km`;
        }
    }

    // =========================
    // GPS
    // =========================

    navigator.geolocation.watchPosition(

        (position) => {

            const latitude =
                position.coords.latitude;

            const longitude =
                position.coords.longitude;

            const precisao =
                position.coords.accuracy;

            const novaPosicao =
                [latitude, longitude];

            // GPS funcionando
            statusGPS.textContent =
                `📍 GPS ativo • Precisão ${Math.round(precisao)} m`;

            carregando.style.display = "none";

            // Primeiro ponto
            if (!ultimaPosicao) {

                mapa.setView(
                    novaPosicao,
                    17
                );

                marcador = L.marker(
                    novaPosicao
                )
                .addTo(mapa)
                .bindPopup(
                    "Você está aqui"
                );

                pontos.push(
                    novaPosicao
                );

                percurso = L.polyline(
                    pontos,
                    {
                        color: "#00a86b",
                        weight: 5
                    }
                ).addTo(mapa);

                ultimaPosicao = {
                    latitude,
                    longitude
                };

                tempoInicio = Date.now();

                intervaloTempo =
                    setInterval(
                        atualizarTempo,
                        1000
                    );

                return;
            }

            // =========================
            // CALCULAR DISTÂNCIA
            // =========================

            const distancia =
                calcularDistancia(
                    ultimaPosicao.latitude,
                    ultimaPosicao.longitude,
                    latitude,
                    longitude
                );

            // Ignorar pequenas variações do GPS
            if (distancia >= 0.005) {

                distanciaTotal += distancia;

                distanciaElement.textContent =
                    distanciaTotal.toFixed(2)
                    .replace(".", ",") +
                    " km";

                ultimaPosicao = {
                    latitude,
                    longitude
                };

                pontos.push(
                    novaPosicao
                );

                percurso.setLatLngs(
                    pontos
                );
            }

            // Atualizar marcador
            if (marcador) {

                marcador.setLatLng(
                    novaPosicao
                );
            }

            // Acompanhar corredor
            mapa.panTo(
                novaPosicao
            );

        },

        (erro) => {

            switch (erro.code) {

                case 1:
                    statusGPS.textContent =
                        "❌ Permissão de localização negada";
                    break;

                case 2:
                    statusGPS.textContent =
                        "❌ Localização indisponível";
                    break;

                case 3:
                    statusGPS.textContent =
                        "❌ Tempo limite do GPS";
                    break;

                default:
                    statusGPS.textContent =
                        "❌ Erro ao acessar GPS";
            }

            carregando.textContent =
                "Não foi possível obter sua localização.";
        },

        {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 10000
        }
    );

});