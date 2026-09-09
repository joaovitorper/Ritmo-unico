document.addEventListener("DOMContentLoaded", () => {

    // ==========================================
    // ELEMENTOS DO HTML
    // ==========================================

    const iniciarButton = document.getElementById("iniciarCorrida");
    const finalizarButton = document.getElementById("finalizarCorrida");

    const tempoElement = document.getElementById("tempo");
    const distanciaElement = document.getElementById("distancia");
    const paceElement = document.getElementById("pace");
    const caloriasElement = document.getElementById("calorias");

    const goalValueElement = document.getElementById("goalValue");
    const progressBarElement = document.getElementById("progressBar");


    // ==========================================
    // CONFIGURAÇÕES
    // ==========================================

    // Meta da corrida
    const metaKm = 5;

    // Peso do usuário em kg
    // Depois podemos pegar isso do cadastro
    const pesoKg = 70;


    // Configuração do GPS
    const gpsOptions = {
        enableHighAccuracy: true,
        maximumAge: 0,
        timeout: 15000
    };


    // ==========================================
    // VARIÁVEIS DA CORRIDA
    // ==========================================

    let segundos = 0;

    let distancia = 0;

    let intervalo = null;

    let corridaIniciada = false;

    let watchID = null;

    let ultimaPosicao = null;


    // ==========================================
    // VERIFICAR ELEMENTOS
    // ==========================================

    if (!iniciarButton) {
        console.error("Elemento #iniciarCorrida não encontrado.");
        return;
    }

    if (!finalizarButton) {
        console.error("Elemento #finalizarCorrida não encontrado.");
        return;
    }


    // ==========================================
    // FORMATAR TEMPO
    // ==========================================

    function formatarTempo() {

        const horas = Math.floor(segundos / 3600);

        const minutos = Math.floor(
            (segundos % 3600) / 60
        );

        const segundosRestantes = segundos % 60;


        return (
            String(horas).padStart(2, "0") +
            ":" +
            String(minutos).padStart(2, "0") +
            ":" +
            String(segundosRestantes).padStart(2, "0")
        );
    }


    // ==========================================
    // CALCULAR DISTÂNCIA ENTRE DOIS PONTOS
    // ==========================================

    function calcularDistancia(
        latitude1,
        longitude1,
        latitude2,
        longitude2
    ) {

        // Raio da Terra em quilômetros
        const R = 6371;


        const dLatitude =
            (latitude2 - latitude1) *
            Math.PI / 180;


        const dLongitude =
            (longitude2 - longitude1) *
            Math.PI / 180;


        const a =
            Math.sin(dLatitude / 2) *
            Math.sin(dLatitude / 2) +

            Math.cos(latitude1 * Math.PI / 180) *
            Math.cos(latitude2 * Math.PI / 180) *

            Math.sin(dLongitude / 2) *
            Math.sin(dLongitude / 2);


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

        if (!tempoElement) {
            return;
        }


        tempoElement.textContent =
            formatarTempo();
    }


    // ==========================================
    // ATUALIZAR DISTÂNCIA
    // ==========================================

    function atualizarDistancia() {

        if (!distanciaElement) {
            return;
        }


        distanciaElement.textContent =
            distancia.toFixed(2) + " km";
    }


    // ==========================================
    // ATUALIZAR PACE
    // ==========================================

    function atualizarPace() {

        if (!paceElement) {
            return;
        }


        if (distancia <= 0 || segundos <= 0) {

            paceElement.textContent =
                "--:-- /km";

            return;
        }


        // Quantos segundos foram gastos por km
        const segundosPorKm =
            segundos / distancia;


        const minutos =
            Math.floor(
                segundosPorKm / 60
            );


        const segundosPace =
            Math.floor(
                segundosPorKm % 60
            );


        paceElement.textContent =
            minutos +
            ":" +
            String(segundosPace).padStart(2, "0") +
            " /km";
    }


    // ==========================================
    // ATUALIZAR CALORIAS
    // ==========================================

    function atualizarCalorias() {

        if (!caloriasElement) {
            return;
        }


        /*
         * Estimativa aproximada:
         * 1 kcal × peso × distância
         */

        const calorias =
            distancia * pesoKg;


        caloriasElement.textContent =
            Math.round(calorias) +
            " kcal";
    }


    // ==========================================
    // ATUALIZAR META
    // ==========================================

    function atualizarMeta() {

        const porcentagem =
            (distancia / metaKm) * 100;


        const porcentagemLimitada =
            Math.min(
                Math.max(porcentagem, 0),
                100
            );


        // Texto da meta

        if (goalValueElement) {

            goalValueElement.textContent =
                distancia.toFixed(2) +
                " / " +
                metaKm.toFixed(2) +
                " km";
        }


        // Barra

        if (progressBarElement) {

            progressBarElement.style.width =
                porcentagemLimitada + "%";
        }
    }


    // ==========================================
    // ATUALIZAR TELA COMPLETA
    // ==========================================

    function atualizarTela() {

        atualizarTempo();

        atualizarDistancia();

        atualizarPace();

        atualizarCalorias();

        atualizarMeta();
    }


    // ==========================================
    // INICIAR CRONÔMETRO
    // ==========================================

    function iniciarCronometro() {

        // Evita criar dois cronômetros
        if (intervalo !== null) {
            clearInterval(intervalo);
        }


        intervalo = setInterval(() => {

            segundos++;

            atualizarTempo();

            atualizarPace();

        }, 1000);
    }


    // ==========================================
    // PARAR CRONÔMETRO
    // ==========================================

    function pararCronometro() {

        if (intervalo !== null) {

            clearInterval(intervalo);

            intervalo = null;
        }
    }


    // ==========================================
    // INICIAR GPS
    // ==========================================

    function iniciarGPS() {

        watchID =
            navigator.geolocation.watchPosition(

                (position) => {

                    if (!corridaIniciada) {
                        return;
                    }


                    const novaPosicao = {

                        latitude:
                            position.coords.latitude,

                        longitude:
                            position.coords.longitude
                    };


                    // ==================================
                    // CALCULAR TRECHO PERCORRIDO
                    // ==================================

                    if (ultimaPosicao !== null) {

                        const trecho =
                            calcularDistancia(

                                ultimaPosicao.latitude,

                                ultimaPosicao.longitude,

                                novaPosicao.latitude,

                                novaPosicao.longitude
                            );


                        /*
                         * Ignora movimentos menores que 3 metros,
                         * que normalmente são pequenas oscilações
                         * do GPS.
                         *
                         * Também ignora saltos maiores que 1 km,
                         * que provavelmente são erro do GPS.
                         */

                        if (
                            trecho >= 0.003 &&
                            trecho < 1
                        ) {

                            distancia += trecho;

                            atualizarDistancia();

                            atualizarPace();

                            atualizarCalorias();

                            atualizarMeta();
                        }
                    }


                    // Guarda posição atual
                    ultimaPosicao =
                        novaPosicao;


                    // ==================================
                    // DEBUG
                    // ==================================

                    console.log(
                        "Latitude:",
                        novaPosicao.latitude
                    );

                    console.log(
                        "Longitude:",
                        novaPosicao.longitude
                    );

                    console.log(
                        "Precisão:",
                        position.coords.accuracy,
                        "metros"
                    );

                    console.log(
                        "Distância:",
                        distancia.toFixed(3),
                        "km"
                    );
                },


                (error) => {

                    console.error(
                        "Erro no GPS:",
                        error
                    );


                    switch (error.code) {

                        case error.PERMISSION_DENIED:

                            alert(
                                "A permissão de localização foi negada."
                            );

                            break;


                        case error.POSITION_UNAVAILABLE:

                            console.warn(
                                "Localização indisponível."
                            );

                            break;


                        case error.TIMEOUT:

                            console.warn(
                                "O GPS demorou muito para responder."
                            );

                            break;


                        default:

                            console.warn(
                                "Erro desconhecido no GPS."
                            );
                    }
                },


                gpsOptions
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
    // BOTÃO INICIAR CORRIDA
    // ==========================================

    iniciarButton.addEventListener(
        "click",
        () => {

            // Não inicia duas vezes
            if (corridaIniciada) {
                return;
            }


            // Verifica GPS
            if (!navigator.geolocation) {

                alert(
                    "Seu dispositivo ou navegador não oferece suporte à localização."
                );

                return;
            }


            // Bloqueia botão enquanto procura GPS
            iniciarButton.disabled = true;

            iniciarButton.textContent =
                "📍 Localizando...";


            // ==================================
            // PEGAR PRIMEIRA LOCALIZAÇÃO
            // ==================================

            navigator.geolocation.getCurrentPosition(

                (position) => {

                    // ==============================
                    // RESET DA CORRIDA
                    // ==============================

                    segundos = 0;

                    distancia = 0;

                    corridaIniciada = true;


                    // ==============================
                    // GUARDAR POSIÇÃO INICIAL
                    // ==============================

                    ultimaPosicao = {

                        latitude:
                            position.coords.latitude,

                        longitude:
                            position.coords.longitude
                    };


                    // ==============================
                    // TROCAR BOTÕES
                    // ==============================

                    iniciarButton.style.display =
                        "none";


                    finalizarButton.style.display =
                        "inline-block";


                    // ==============================
                    // INICIAR CRONÔMETRO
                    // ==============================

                    iniciarCronometro();


                    // ==============================
                    // INICIAR GPS CONTÍNUO
                    // ==============================

                    iniciarGPS();


                    // ==============================
                    // ATUALIZAR TELA
                    // ==============================

                    atualizarTela();


                    // ==============================
                    // CONSOLE
                    // ==============================

                    console.log(
                        "======================"
                    );

                    console.log(
                        "CORRIDA INICIADA"
                    );

                    console.log(
                        "Latitude:",
                        position.coords.latitude
                    );

                    console.log(
                        "Longitude:",
                        position.coords.longitude
                    );

                    console.log(
                        "Precisão:",
                        position.coords.accuracy,
                        "metros"
                    );

                    console.log(
                        "======================"
                    );
                },


                // ==================================
                // ERRO AO PEGAR GPS
                // ==================================

                (error) => {

                    iniciarButton.disabled =
                        false;


                    iniciarButton.textContent =
                        "🏃 Iniciar corrida";


                    switch (error.code) {

                        case error.PERMISSION_DENIED:

                            alert(
                                "Permissão de localização negada. Ative a localização e tente novamente."
                            );

                            break;


                        case error.POSITION_UNAVAILABLE:

                            alert(
                                "Não foi possível encontrar sua localização."
                            );

                            break;


                        case error.TIMEOUT:

                            alert(
                                "O GPS demorou muito para responder. Tente novamente."
                            );

                            break;


                        default:

                            alert(
                                "Não foi possível acessar sua localização."
                            );
                    }
                },


                gpsOptions
            );
        }
    );


    // ==========================================
    // BOTÃO FINALIZAR CORRIDA
    // ==========================================

    finalizarButton.addEventListener(
        "click",
        () => {

            if (!corridaIniciada) {
                return;
            }


            // ==============================
            // ENCERRA CORRIDA
            // ==============================

            corridaIniciada = false;


            // Para cronômetro
            pararCronometro();


            // Para GPS
            pararGPS();


            // Atualiza resultado final
            atualizarTela();


            // ==============================
            // TROCA BOTÕES
            // ==============================

            finalizarButton.style.display =
                "none";


            iniciarButton.style.display =
                "inline-block";


            iniciarButton.disabled =
                false;


            iniciarButton.textContent =
                "🏃 Nova corrida";


            // ==============================
            // RESULTADO NO CONSOLE
            // ==============================

            console.log(
                "======================"
            );

            console.log(
                "CORRIDA FINALIZADA"
            );

            console.log(
                "Tempo:",
                formatarTempo()
            );

            console.log(
                "Distância:",
                distancia.toFixed(2),
                "km"
            );

            console.log(
                "Pace:",
                paceElement
                    ? paceElement.textContent
                    : "--:-- /km"
            );

            console.log(
                "Calorias:",
                caloriasElement
                    ? caloriasElement.textContent
                    : "0 kcal"
            );

            console.log(
                "======================"
            );


            alert(
                "🏁 Corrida finalizada!\n\n" +
                "Tempo: " +
                formatarTempo() +
                "\n" +
                "Distância: " +
                distancia.toFixed(2) +
                " km\n" +
                "Pace: " +
                (
                    paceElement
                        ? paceElement.textContent
                        : "--:-- /km"
                ) +
                "\n" +
                "Calorias: " +
                (
                    caloriasElement
                        ? caloriasElement.textContent
                        : "0 kcal"
                )
            );
        }
    );


    // ==========================================
    // VALORES INICIAIS
    // ==========================================

    atualizarTela();

});
