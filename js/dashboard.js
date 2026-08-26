document.addEventListener("DOMContentLoaded", () => {

    // =========================
    // CARREGAR HISTÓRICO
    // =========================

    let historico = [];

    try {
        historico = JSON.parse(
            localStorage.getItem("historicoCorridas") || "[]"
        );

        if (!Array.isArray(historico)) {
            historico = [];
        }

    } catch (erro) {
        console.error("Erro ao carregar histórico:", erro);
        historico = [];
    }


    // =========================
    // CALCULAR DADOS
    // =========================

    const totalCorridas = historico.length;

    let totalDistancia = 0;
    let totalTempo = 0;

    historico.forEach((corrida) => {

        const distancia =
            parseFloat(corrida.distancia) || 0;

        totalDistancia += distancia;

        // Converte o tempo para segundos
        if (corrida.tempo) {

            const partes =
                corrida.tempo.split(":").map(Number);

            if (partes.length === 2) {

                totalTempo +=
                    (partes[0] * 60) +
                    partes[1];

            } else if (partes.length === 3) {

                totalTempo +=
                    (partes[0] * 3600) +
                    (partes[1] * 60) +
                    partes[2];

            }

        }

    });


    // =========================
    // DISTÂNCIA
    // =========================

    const distanciaElement =
        document.querySelector(
            ".dashboard-card:nth-child(1) strong"
        );

    if (distanciaElement) {

        distanciaElement.textContent =
            totalDistancia
                .toFixed(2)
                .replace(".", ",") + " km";

    }


    // =========================
    // TEMPO
    // =========================

    const tempoElement =
        document.querySelector(
            ".dashboard-card:nth-child(2) strong"
        );

    if (tempoElement) {

        const horas =
            Math.floor(totalTempo / 3600);

        const minutos =
            Math.floor((totalTempo % 3600) / 60);

        tempoElement.textContent =
            `${horas}h ${String(minutos).padStart(2, "0")}min`;

    }


    // =========================
    // CALORIAS
    // =========================

    const caloriasElement =
        document.querySelector(
            ".dashboard-card:nth-child(3) strong"
        );

    if (caloriasElement) {

        // Estimativa simples:
        // aproximadamente 60 kcal por km

        const calorias =
            Math.round(totalDistancia * 60);

        caloriasElement.textContent =
            calorias;

    }


    // =========================
    // OBJETIVO
    // =========================

    const objetivoElement =
        document.querySelector(
            ".dashboard-card:nth-child(4) strong"
        );

    if (objetivoElement) {

        // Meta semanal de exemplo: 20 km

        const metaSemanal = 20;

        let progresso =
            (totalDistancia / metaSemanal) * 100;

        progresso =
            Math.min(progresso, 100);

        objetivoElement.textContent =
            Math.round(progresso) + "%";

    }


    // =========================
    // BARRA DE EVOLUÇÃO
    // =========================

    const progressoElement =
        document.querySelector(
            ".evolucao-progresso"
        );

    const progressoTexto =
        document.querySelector(
            ".evolucao-info strong"
        );

    if (progressoElement && progressoTexto) {

        const metaSemanal = 20;

        let progresso =
            (totalDistancia / metaSemanal) * 100;

        progresso =
            Math.min(progresso, 100);

        progressoElement.style.width =
            `${progresso}%`;

        progressoTexto.textContent =
            Math.round(progresso) + "%";

    }


    // =========================
    // MENSAGEM DE DESEMPENHO
    // =========================

    const desempenhoTexto =
        document.querySelector(
            ".dashboard-box p"
        );

    if (desempenhoTexto) {

        if (totalCorridas === 0) {

            desempenhoTexto.textContent =
                "Comece sua primeira corrida para acompanhar sua evolução.";

        } else {

            desempenhoTexto.textContent =
                `Você já realizou ${totalCorridas} corrida${totalCorridas > 1 ? "s" : ""}. Continue evoluindo no seu ritmo!`;

        }

    }

});