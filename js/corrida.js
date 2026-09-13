document.addEventListener("DOMContentLoaded", function () {

    console.log("Ritmo Único - corrida.js carregado");

    const iniciarBtn = document.getElementById("iniciarCorrida");
    const pausarBtn = document.getElementById("pausarCorrida");
    const finalizarBtn = document.getElementById("finalizarCorrida");

    const tempoEl = document.getElementById("tempo");
    const distanciaEl = document.getElementById("distancia");
    const paceEl = document.getElementById("pace");
    const caloriasEl = document.getElementById("calorias");
    const gpsStatusEl = document.getElementById("gpsStatus");
    const goalValueEl = document.getElementById("goalValue");
    const progressBarEl = document.getElementById("progressBar");

    const META_KM = 5;
    const PESO_KG = 70;

    let segundos = 0;
    let distanciaKm = 0;

    let timerInterval = null;
    let watchID = null;

    let corridaIniciada = false;
    let corridaPausada = false;

    let ultimaPosicao = null;
    let rota = [];

    const opcoesGPS = {
        enableHighAccuracy: true,
        maximumAge: 0,
        timeout: 15000
    };

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

        const horas = Math.floor(totalSegundos / 3600);

        const minutos = Math.floor(
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
    // CALCULAR DISTÂNCIA
    // ==========================================

    function calcularDistancia(
        latitude1,
        longitude1,
        latitude2,
        longitude2
    ) {

        const R = 6371;

        const dLat =
            (latitude2 - latitude1) * Math.PI / 180;

        const dLon =
            (longitude2 - longitude1) * Math.PI / 180;

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
    // ATUALIZAR TEMPO
    // ==========================================

    function atualizarTempo() {

        segundos++;

        if (tempoEl) {
            tempoEl.textContent =
                formatarTempo(segundos);
        }

        atualizarPace();
    }

    // ==========================================
    // PACE
    // ==========================================

    function atualizarPace() {

        if (!paceEl) {
            return;
        }

        if (distanciaKm <= 0) {

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
            Math.floor(segundosPorKm / 60);

        const segundosRestantes =
            Math.floor(segundosPorKm % 60);

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

        console.log("GPS recebido:", {
            latitude: latitude,
            longitude: longitude,
            accuracy: accuracy
        });

        if (accuracy > 100) {

            atualizarGPSStatus(
                "📍 GPS com baixa precisão..."
            );

            return;
        }

        atualizarGPSStatus(
            "📍 GPS conectado"
        );

        const ponto = {
            latitude: latitude,
            longitude: longitude,
            accuracy: accuracy,
            timestamp: Date.now()
        };

        rota.push(ponto);

        // Não calcula distância enquanto estiver pausado
        if (corridaPausada) {

            ultimaPosicao = {
                latitude: latitude,
                longitude: longitude
            };

            return;
        }

        if (ultimaPosicao !== null) {

            const distanciaTrecho =
                calcularDistancia(
                    ultimaPosicao.latitude,
                    ultimaPosicao.longitude,
                    latitude,
                    longitude
                );

            console.log(
                "Distância do trecho:",
                distanciaTrecho,
                "km"
            );

            // Ignora movimentos muito pequenos
            if (distanciaTrecho >= 0.003) {

                // Ignora salto absurdo do GPS
                if (distanciaTrecho <= 1) {

                    distanciaKm +=
                        distanciaTrecho;

                    atualizarDistancia();
                    atualizarCalorias();
                    atualizarPace();
                }
            }
        }

        ultimaPosicao = {
            latitude: latitude,
            longitude: longitude
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

        if (error.code === 1) {

            atualizarGPSStatus(
                "❌ Permissão de localização negada"
            );

            alert(
                "A localização foi bloqueada pelo navegador.\n\n" +
                "No Chrome, permita o acesso à localização para este site."
            );

        } else if (error.code === 2) {

            atualizarGPSStatus(
                "❌ Localização indisponível"
            );

        } else if (error.code === 3) {

            atualizarGPSStatus(
                "⚠️ Tempo para localizar esgotado"
            );

        } else {

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

        // Primeiro pega uma posição
        navigator.geolocation.getCurrentPosition(

            function (position) {

                console.log(
                    "Primeira localização recebida"
                );

                receberPosicao(position);

                // Depois começa a acompanhar
                watchID =
                    navigator.geolocation.watchPosition(
                        receberPosicao,
                        erroGPS,
                        opcoesGPS
                    );

                console.log(
                    "Monitoramento GPS iniciado:",
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

            console.log(
                "GPS parado:",
                watchID
            );

            watchID = null;
        }
    }

    // ==========================================
    // INICIAR TIMER
    // ==========================================

    function iniciarTimer() {

        if (timerInterval !== null) {
            return;
        }

        timerInterval =
            setInterval(
                atualizarTempo,
                1000
            );

        console.log("Timer iniciado");
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

            console.log("Timer parado");
        }
    }

    // ==========================================
    // INICIAR CORRIDA
    // ==========================================

    if (iniciarBtn) {

        iniciarBtn.addEventListener(
            "click",
            function () {

                if (corridaIniciada) {
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
    }

    // ==========================================
    // PAUSAR / CONTINUAR
    // ==========================================

    if (pausarBtn) {

        pausarBtn.addEventListener(
            "click",
            function () {

                if (!corridaIniciada) {
                    return;
                }

                // PAUSAR
                if (!corridaPausada) {

                    corridaPausada = true;

                    pararTimer();

                    pausarBtn.textContent =
                        "▶ Continuar";

                    atualizarGPSStatus(
                        "⏸ GPS pausado"
                    );

                    console.log(
                        "⏸ Corrida pausada"
                    );

                }

                // CONTINUAR
                else {

                    corridaPausada = false;

                    // Evita medir distância
                    // do ponto antigo até o novo
                    ultimaPosicao = null;

                    iniciarTimer();

                    pausarBtn.textContent =
                        "⏸ Pausar";

                    atualizarGPSStatus(
                        "📍 GPS conectado"
                    );

                    console.log(
                        "▶ Corrida continuada"
                    );
                }
            }
        );
    }

    // ==========================================
    // FINALIZAR CORRIDA
    // ==========================================

    if (finalizarBtn) {

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

                    data:
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

                    rota:
                        rota
                };

                // ==================================
                // SALVAR NO LOCALSTORAGE
                // ==================================

                let historico = [];

                try {

                    historico =
                        JSON.parse(
                            localStorage.getItem(
                                "historicoCorridas"
                            )
                        ) || [];

                } catch (e) {

                    console.error(
                        "Erro ao ler histórico:",
                        e
                    );

                    historico = [];
                }

                historico.push(corrida);

                localStorage.setItem(
                    "historicoCorridas",
                    JSON.stringify(historico)
                );

                console.log(
                    "Corrida salva:",
                    corrida
                );

                alert(
                    "Corrida finalizada!\n\n" +

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

                // ==================================
                // RESETAR
                // ==================================

                corridaIniciada = false;
                corridaPausada = false;

                segundos = 0;
                distanciaKm = 0;

                ultimaPosicao = null;
                rota = [];

                iniciarBtn.style.display =
                    "inline-flex";

                pausarBtn.style.display =
                    "none";

                finalizarBtn.style.display =
                    "none";

                pausarBtn.textContent =
                    "⏸ Pausar";

                tempoEl.textContent =
                    "00:00:00";

                atualizarDistancia();
                atualizarCalorias();
                atualizarPace();

                atualizarGPSStatus(
                    "📍 GPS aguardando"
                );

                console.log(
                    "⏹ Corrida finalizada"
                );
            }
        );
    }

    // ==========================================
    // ESTADO INICIAL
    // ==========================================

    atualizarDistancia();
    atualizarCalorias();
    atualizarPace();

    console.log(
        "✅ Sistema de corrida pronto."
    );

});