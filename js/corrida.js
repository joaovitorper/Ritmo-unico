document.addEventListener("DOMContentLoaded", () => {

    const iniciarButton =
        document.getElementById("iniciarCorrida");

    const finalizarButton =
        document.getElementById("finalizarCorrida");

    const tempoElement =
        document.getElementById("tempo");

    const distanciaElement =
        document.getElementById("distancia");

    let segundos = 0;
    let distancia = 0;
    let intervalo = null;
    let corridaIniciada = false;
    let watchID = null;
    let ultimaPosicao = null;


    // =========================
    // FORMATAR TEMPO
    // =========================

    function formatarTempo(segundos) {

        const horas = Math.floor(segundos / 3600);

        const minutos =
            Math.floor((segundos % 3600) / 60);

        const segundosRestantes =
            segundos % 60;

        return `${String(horas).padStart(2, "0")}:` +
               `${String(minutos).padStart(2, "0")}:` +
               `${String(segundosRestantes).padStart(2, "0")}`;
    }


    // =========================
    // CALCULAR DISTÂNCIA GPS
    // =========================

    function calcularDistancia(
        lat1,
        lon1,
        lat2,
        lon2
    ) {

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
    // ATUALIZAR DISTÂNCIA
    // =========================

    function atualizarDistancia(position) {

        if (!corridaIniciada) {
            return;
        }

        const latitude =
            position.coords.latitude;

        const longitude =
            position.coords.longitude;

        const precisao =
            position.coords.accuracy;


        // Ignora GPS com precisão muito baixa
        if (precisao > 50) {
            return;
        }


        // Primeiro ponto
        if (!ultimaPosicao) {

            ultimaPosicao = {
                latitude,
                longitude
            };

            return;
        }


        const novaDistancia =
            calcularDistancia(
                ultimaPosicao.latitude,
                ultimaPosicao.longitude,
                latitude,
                longitude
            );


        // Ignora movimentos muito pequenos
        if (novaDistancia >= 0.005) {

            distancia += novaDistancia;

            ultimaPosicao = {
                latitude,
                longitude
            };


            if (distanciaElement) {

                distanciaElement.textContent =
                    distancia
                        .toFixed(2)
                        .replace(".", ",") +
                    " km";

            }

        }

    }


    // =========================
    // ERRO DO GPS
    // =========================

    function erroGPS(error) {

        switch (error.code) {

            case 1:
                alert(
                    "Permissão de localização negada."
                );
                break;

            case 2:
                alert(
                    "Não foi possível obter sua localização."
                );
                break;

            case 3:
                alert(
                    "Tempo limite para obter o GPS."
                );
                break;

            default:
                alert(
                    "Erro ao acessar o GPS."
                );
        }

    }


    // =========================
    // INICIAR CORRIDA
    // =========================

    if (iniciarButton) {

        iniciarButton.addEventListener("click", () => {

            if (corridaIniciada) {
                return;
            }


            // Verifica GPS
            if (!navigator.geolocation) {

                alert(
                    "Seu navegador não suporta GPS."
                );

                return;
            }


            corridaIniciada = true;

            segundos = 0;
            distancia = 0;
            ultimaPosicao = null;


            iniciarButton.style.display = "none";


            if (finalizarButton) {
                finalizarButton.style.display = "block";
            }


            // =========================
            // CRONÔMETRO
            // =========================

            intervalo = setInterval(() => {

                segundos++;

                if (tempoElement) {

                    tempoElement.textContent =
                        formatarTempo(segundos);

                }

            }, 1000);


            // =========================
            // GPS REAL
            // =========================

            watchID =
                navigator.geolocation.watchPosition(
                    atualizarDistancia,
                    erroGPS,
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 10000
                    }
                );

        });

    }


    // =========================
    // FINALIZAR CORRIDA
    // =========================

    if (finalizarButton) {

        finalizarButton.addEventListener("click", () => {

            if (!corridaIniciada) {

                alert(
                    "Você precisa iniciar a corrida primeiro."
                );

                return;
            }


            // Para cronômetro
            clearInterval(intervalo);

            intervalo = null;


            // Para GPS
            if (watchID !== null) {

                navigator.geolocation.clearWatch(
                    watchID
                );

                watchID = null;
            }


            corridaIniciada = false;


            // =========================
            // VALIDAR TEMPO
            // =========================

            if (segundos <= 0) {

                alert(
                    "A corrida precisa ter pelo menos 1 segundo."
                );

                return;
            }


            // =========================
            // VALIDAR DISTÂNCIA
            // =========================

            if (distancia <= 0) {

                alert(
                    "Não foi possível registrar uma distância pelo GPS."
                );

                return;
            }


            // =========================
            // CRIAR CORRIDA
            // =========================

            const corrida = {

                tempo:
                    formatarTempo(segundos),

                distancia:
                    distancia.toFixed(2),

                data:
                    new Date()
                        .toLocaleDateString("pt-BR")

            };


            // =========================
            // CARREGAR HISTÓRICO
            // =========================

            let historico = [];

            try {

                historico = JSON.parse(
                    localStorage.getItem(
                        "historicoCorridas"
                    ) || "[]"
                );

                if (!Array.isArray(historico)) {
                    historico = [];
                }

            } catch (erro) {

                console.error(
                    "Erro ao carregar histórico:",
                    erro
                );

                historico = [];

            }


            // =========================
            // SALVAR
            // =========================

            historico.push(corrida);

            localStorage.setItem(
                "historicoCorridas",
                JSON.stringify(historico)
            );


            alert(
                "Corrida finalizada e salva com sucesso!"
            );


            window.location.href =
                "historico.php";

        });

    }

});