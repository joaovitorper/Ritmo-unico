document.addEventListener("DOMContentLoaded", function () {

    console.log("Ritmo Único - corrida.js carregado");


    // ==========================================
    // ELEMENTOS
    // ==========================================

    const iniciarBtn =
        document.getElementById("iniciarCorrida");

    const pausarBtn =
        document.getElementById("pausarCorrida");

    const finalizarBtn =
        document.getElementById("finalizarCorrida");

    const tempoEl =
        document.getElementById("tempo");

    const distanciaEl =
        document.getElementById("distancia");

    const paceEl =
        document.getElementById("pace");

    const caloriasEl =
        document.getElementById("calorias");

    const gpsStatusEl =
        document.getElementById("gpsStatus");

    const goalValueEl =
        document.getElementById("goalValue");

    const progressBarEl =
        document.getElementById("progressBar");


    // ==========================================
    // CONFIGURAÇÕES
    // ==========================================

    const META_KM = 5;

    const PESO_KG = 70;

    const API_CORRIDAS = "../api/v1/corridas/cadastrar.php";


    const opcoesGPS = {

        enableHighAccuracy: true,

        maximumAge: 0,

        timeout: 15000

    };


    // ==========================================
    // VARIÁVEIS
    // ==========================================

    let segundos = 0;

    let distanciaKm = 0;

    let timerInterval = null;

    let watchID = null;

    let corridaIniciada = false;

    let corridaPausada = false;

    let ultimaPosicao = null;

    let rota = [];


    // ==========================================
    // STATUS GPS
    // ==========================================

    function atualizarGPSStatus(texto) {

        if (gpsStatusEl) {

            gpsStatusEl.textContent = texto;

        }

        console.log(texto);

    }


    // ==========================================
    // FORMATAR TEMPO
    // ==========================================

    function formatarTempo(totalSegundos) {

        const horas =
            Math.floor(totalSegundos / 3600);

        const minutos =
            Math.floor(
                (totalSegundos % 3600) / 60
            );

        const segundosRestantes =
            totalSegundos % 60;


        return (
            String(horas).padStart(2, "0") +
            ":" +
            String(minutos).padStart(2, "0") +
            ":" +
            String(segundosRestantes).padStart(2, "0")
        );

    }


    // ==========================================
    // DISTÂNCIA GPS
    // ==========================================

    function calcularDistancia(
        latitude1,
        longitude1,
        latitude2,
        longitude2
    ) {

        const R = 6371;

        const dLat =
            (latitude2 - latitude1) *
            Math.PI / 180;

        const dLon =
            (longitude2 - longitude1) *
            Math.PI / 180;


        const a =
            Math.sin(dLat / 2) *
            Math.sin(dLat / 2) +

            Math.cos(latitude1 * Math.PI / 180) *
            Math.cos(latitude2 * Math.PI / 180) *

            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);


        const c =
            2 *
            Math.atan2(
                Math.sqrt(a),
                Math.sqrt(1 - a)
            );


        return R * c;

    }


    // ==========================================
    // ATUALIZAR TEMPO NA TELA
    // ==========================================

    function atualizarTempoTela() {

        if (!tempoEl) {
            return;
        }

        tempoEl.textContent =
            formatarTempo(segundos);

    }


    // ==========================================
    // PACE
    // ==========================================

    function atualizarPace() {

        if (!paceEl) {
            return;
        }


        if (
            distanciaKm <= 0 ||
            segundos <= 0
        ) {

            paceEl.textContent = "--:--";

            return;

        }


        const segundosPorKm =
            segundos / distanciaKm;


        if (!isFinite(segundosPorKm)) {

            paceEl.textContent = "--:--";

            return;

        }


        const minutos =
            Math.floor(
                segundosPorKm / 60
            );


        const segundosRestantes =
            Math.floor(
                segundosPorKm % 60
            );


        paceEl.textContent =
            String(minutos).padStart(2, "0") +
            ":" +
            String(segundosRestantes).padStart(2, "0");

    }


    // ==========================================
    // DISTÂNCIA
    // ==========================================

    function atualizarDistancia() {

        if (distanciaEl) {

            distanciaEl.textContent =
                distanciaKm
                    .toFixed(2)
                    .replace(".", ",");

        }


        const progresso =
            Math.min(
                (distanciaKm / META_KM) * 100,
                100
            );


        if (progressBarEl) {

            progressBarEl.style.width =
                progresso + "%";

        }


        if (goalValueEl) {

            goalValueEl.textContent =
                distanciaKm
                    .toFixed(2)
                    .replace(".", ",") +
                " / " +
                META_KM
                    .toFixed(2)
                    .replace(".", ",") +
                " km";

        }

    }


    // ==========================================
    // CALORIAS
    // ==========================================

    function atualizarCalorias() {

        const calorias =
            distanciaKm * PESO_KG;


        if (caloriasEl) {

            caloriasEl.textContent =
                Math.round(calorias);

        }

    }


    // ==========================================
    // RECEBER GPS
    // ==========================================

    function receberPosicao(position) {

        if (!corridaIniciada) {
            return;
        }


        const latitude =
            position.coords.latitude;

        const longitude =
            position.coords.longitude;

        const accuracy =
            position.coords.accuracy;


        console.log("GPS:", {
            latitude,
            longitude,
            accuracy
        });


        if (accuracy > 100) {

            atualizarGPSStatus(
                "📍 GPS conectado - precisão baixa"
            );

        } else {

            atualizarGPSStatus(
                "📍 GPS conectado"
            );

        }


        const ponto = {

            latitude: latitude,

            longitude: longitude,

            accuracy: accuracy,

            timestamp: Date.now()

        };


        rota.push(ponto);


        // Durante pausa não soma distância

        if (corridaPausada) {

            ultimaPosicao = {

                latitude,
                longitude

            };

            return;

        }


        if (ultimaPosicao !== null) {

            const trecho =
                calcularDistancia(

                    ultimaPosicao.latitude,
                    ultimaPosicao.longitude,

                    latitude,
                    longitude

                );


            console.log(
                "Trecho:",
                trecho,
                "km"
            );


            // Ignora menos de 3 metros

            if (trecho >= 0.003) {

                // Ignora salto maior que 1 km

                if (trecho <= 1) {

                    distanciaKm += trecho;

                    atualizarDistancia();

                    atualizarCalorias();

                    atualizarPace();

                }

            }

        }


        ultimaPosicao = {

            latitude,

            longitude

        };

    }


    // ==========================================
    // ERRO GPS
    // ==========================================

    function erroGPS(error) {

        console.error(
            "Erro GPS:",
            error
        );


        switch (error.code) {

            case 1:

                atualizarGPSStatus(
                    "❌ Permissão de localização negada"
                );

                break;


            case 2:

                atualizarGPSStatus(
                    "❌ Localização indisponível"
                );

                break;


            case 3:

                atualizarGPSStatus(
                    "⚠️ Tempo para localizar esgotado"
                );

                break;


            default:

                atualizarGPSStatus(
                    "❌ Erro ao obter localização"
                );

        }

    }


    // ==========================================
    // INICIAR GPS
    // ==========================================

    function iniciarGPS() {

        if (!navigator.geolocation) {

            atualizarGPSStatus(
                "❌ Este navegador não suporta GPS"
            );

            return;

        }


        atualizarGPSStatus(
            "📍 Solicitando localização..."
        );


        navigator.geolocation.getCurrentPosition(

            function (position) {

                receberPosicao(position);


                watchID =
                    navigator.geolocation.watchPosition(

                        receberPosicao,

                        erroGPS,

                        opcoesGPS

                    );


                console.log(
                    "Monitoramento GPS:",
                    watchID
                );

            },

            erroGPS,

            opcoesGPS

        );

    }


    // ==========================================
    // PARAR GPS
    // ==========================================

    function pararGPS() {

        if (watchID !== null) {

            navigator.geolocation.clearWatch(
                watchID
            );

            watchID = null;

        }


        ultimaPosicao = null;

    }


    // ==========================================
    // TIMER
    // ==========================================

    function iniciarTimer() {

        if (timerInterval !== null) {
            return;
        }


        timerInterval =
            setInterval(function () {

                segundos++;

                atualizarTempoTela();

                atualizarPace();

            }, 1000);

    }


    // ==========================================
    // PARAR TIMER
    // ==========================================

    function pararTimer() {

        if (timerInterval !== null) {

            clearInterval(
                timerInterval
            );

            timerInterval = null;

        }

    }


    // ==========================================
    // SALVAR CORRIDA NO SERVIDOR
    // ==========================================

    async function salvarCorridaNoServidor(corrida) {

        try {

            const resposta = await fetch(API_CORRIDAS, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json; charset=utf-8"
                },
                credentials: "same-origin",
                body: JSON.stringify(corrida)
            });

            const dados = await resposta.json();

            if (!resposta.ok || !dados.sucesso) {
                throw new Error(dados.mensagem || "Erro ao salvar corrida no servidor.");
            }

            console.log("Corrida salva no banco:", dados);

            return dados;

        } catch (erro) {

            console.error("Falha ao salvar corrida no servidor:", erro);

            try {
                const historico = JSON.parse(localStorage.getItem("historicoCorridas") || "[]");
                const lista = Array.isArray(historico) ? historico : [];
                lista.push(corrida);
                localStorage.setItem("historicoCorridas", JSON.stringify(lista));
            } catch (fallbackErro) {
                console.error("Erro ao salvar fallback local:", fallbackErro);
            }

            throw erro;
        }
    }


    // ==========================================
    // INICIAR CORRIDA
    // ==========================================

    iniciarBtn.addEventListener(
        "click",
        function () {

            if (corridaIniciada) {
                return;
            }


            if (!navigator.geolocation) {

                alert(
                    "Seu navegador não suporta localização."
                );

                return;

            }


            corridaIniciada = true;

            corridaPausada = false;


            iniciarBtn.style.display =
                "none";


            pausarBtn.style.display =
                "inline-flex";


            finalizarBtn.style.display =
                "inline-flex";


            pausarBtn.textContent =
                "⏸ Pausar";


            atualizarGPSStatus(
                "📍 Localizando..."
            );


            iniciarGPS();

            iniciarTimer();


            console.log(
                "🏃 Corrida iniciada"
            );

        }
    );


    // ==========================================
    // PAUSAR / CONTINUAR
    // ==========================================

    pausarBtn.addEventListener(
        "click",
        function () {

            if (!corridaIniciada) {
                return;
            }


            if (!corridaPausada) {

                corridaPausada = true;

                pararTimer();


                pausarBtn.textContent =
                    "▶ Continuar";


                atualizarGPSStatus(
                    "⏸ Corrida pausada"
                );

            } else {

                corridaPausada = false;


                // Não soma do ponto antigo

                ultimaPosicao = null;


                iniciarTimer();


                pausarBtn.textContent =
                    "⏸ Pausar";


                atualizarGPSStatus(
                    "📍 GPS conectado"
                );

            }

        }
    );


    // ==========================================
    // FINALIZAR CORRIDA
    // ==========================================

    finalizarBtn.addEventListener(
        "click",
        function () {

            if (!corridaIniciada) {
                return;
            }


            const confirmou =
                confirm(
                    "Deseja realmente finalizar a corrida?"
                );


            if (!confirmou) {
                return;
            }


            pararTimer();

            pararGPS();


            const corrida = {

                data_corrida:
                    new Date().toISOString(),

                tempo:
                    segundos,

                distancia:
                    Number(
                        distanciaKm.toFixed(3)
                    ),

                calorias:
                    Math.round(
                        distanciaKm * PESO_KG
                    ),

                ritmo:
                    distanciaKm > 0
                        ? (segundos / distanciaKm).toFixed(2)
                        : "0",

                pace:
                    distanciaKm > 0
                        ? (segundos / distanciaKm).toFixed(2)
                        : "0",

                rota:
                    rota

            };


            salvarCorridaNoServidor(corrida)
                .then(() => {
                    alert(
                        "🏁 Corrida finalizada!\n\n" +
                        "Distância: " +
                        distanciaKm
                            .toFixed(2)
                            .replace(".", ",") +
                        " km\n" +
                        "Tempo: " +
                        formatarTempo(segundos) +
                        "\n" +
                        "Calorias: " +
                        Math.round(
                            distanciaKm * PESO_KG
                        ) +
                        " kcal"
                    );
                })
                .catch(() => {
                    alert(
                        "🏁 Corrida finalizada localmente.\n\n" +
                        "Distância: " +
                        distanciaKm
                            .toFixed(2)
                            .replace(".", ",") +
                        " km\n" +
                        "Tempo: " +
                        formatarTempo(segundos) +
                        "\n" +
                        "Calorias: " +
                        Math.round(
                            distanciaKm * PESO_KG
                        ) +
                        " kcal"
                    );
                })
                .finally(() => {
                    resetarCorrida();
                });

        }
    );


    // ==========================================
    // RESETAR CORRIDA
    // ==========================================

    function resetarCorrida() {

        corridaIniciada = false;

        corridaPausada = false;


        segundos = 0;

        distanciaKm = 0;


        ultimaPosicao = null;

        rota = [];


        pararTimer();

        pararGPS();


        iniciarBtn.style.display =
            "inline-flex";


        pausarBtn.style.display =
            "none";


        finalizarBtn.style.display =
            "none";


        pausarBtn.textContent =
            "⏸ Pausar";


        atualizarTempoTela();

        atualizarDistancia();

        atualizarCalorias();

        atualizarPace();


        atualizarGPSStatus(
            "📍 GPS aguardando"
        );

    }


    // ==========================================
    // ESTADO INICIAL
    // ==========================================

    atualizarTempoTela();

    atualizarDistancia();

    atualizarCalorias();

    atualizarPace();


    console.log(
        "✅ Sistema de corrida pronto."
    );

});
