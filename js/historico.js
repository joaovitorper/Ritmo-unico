document.addEventListener("DOMContentLoaded", () => {

    console.log("Ritmo Único - historico.js carregado");


    // ==========================================
    // ELEMENTOS
    // ==========================================

    const voltarButton =
        document.getElementById("voltar");

    const limparButton =
        document.getElementById("limparHistorico");

    const listaCorridas =
        document.getElementById("listaCorridas") ||
        document.getElementById("listaHistorico");

    const historicoVazio =
        document.getElementById("historicoVazio");

    const distanciaTotal =
        document.getElementById("distanciaTotal");

    const totalCorridas =
        document.getElementById("totalCorridas");

    const tempoTotal =
        document.getElementById("tempoTotal");


    // ==========================================
    // LER HISTÓRICO
    // ==========================================

    async function obterHistorico() {

        try {

            const resposta = await fetch("../api/v1/corridas/listar.php", {
                method: "GET",
                credentials: "same-origin"
            });

            if (resposta.ok) {
                const dados = await resposta.json();
                const corridas = Array.isArray(dados.corridas) ? dados.corridas : [];

                if (corridas.length > 0) {
                    return corridas.map((corrida) => ({
                        ...corrida,
                        distancia: Number(corrida.distancia ?? 0),
                        tempo: Number(corrida.tempo ?? corrida.ritmo ?? 0),
                        calorias: Number(corrida.calorias ?? 0),
                        data: corrida.data_corrida || corrida.data || new Date().toISOString()
                    }));
                }
            }

        } catch (erro) {
            console.warn("API de histórico indisponível, usando fallback local:", erro);
        }

        try {

            const dados =
                localStorage.getItem(
                    "historicoCorridas"
                );

            if (!dados) {
                return [];
            }

            const historico =
                JSON.parse(dados);

            if (!Array.isArray(historico)) {
                return [];
            }

            return historico;

        } catch (erro) {

            console.error(
                "Erro ao carregar histórico:",
                erro
            );

            return [];
        }
    }


    // ==========================================
    // FORMATAR TEMPO
    // ==========================================

    function formatarTempo(segundos) {

        segundos =
            Number(segundos) || 0;

        const horas =
            Math.floor(segundos / 3600);

        const minutos =
            Math.floor(
                (segundos % 3600) / 60
            );

        const segundosRestantes =
            segundos % 60;

        return (
            String(horas).padStart(2, "0") +
            ":" +
            String(minutos).padStart(2, "0") +
            ":" +
            String(segundosRestantes).padStart(2, "0")
        );
    }


    // ==========================================
    // TEMPO RESUMIDO
    // ==========================================

    function formatarTempoResumo(segundos) {

        segundos =
            Number(segundos) || 0;

        const horas =
            Math.floor(segundos / 3600);

        const minutos =
            Math.floor(
                (segundos % 3600) / 60
            );

        return (
            horas +
            "h " +
            String(minutos).padStart(2, "0") +
            "min"
        );
    }


    // ==========================================
    // FORMATAR DATA
    // ==========================================

    function formatarData(data) {

        if (!data) {
            return "Data não informada";
        }

        const dataObj =
            new Date(data);

        if (isNaN(dataObj.getTime())) {
            return "Data não informada";
        }

        return dataObj.toLocaleString(
            "pt-BR",
            {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            }
        );
    }


    // ==========================================
    // ATUALIZAR RESUMO
    // ==========================================

    function atualizarResumo(historico) {

        let distancia =
            0;

        let tempo =
            0;


        historico.forEach(corrida => {

            distancia +=
                Number(
                    corrida.distancia
                ) || 0;

            tempo +=
                Number(
                    corrida.tempo
                ) || 0;

        });


        if (distanciaTotal) {

            distanciaTotal.textContent =
                distancia
                    .toFixed(2)
                    .replace(".", ",") +
                " km";
        }


        if (totalCorridas) {

            totalCorridas.textContent =
                historico.length;
        }


        if (tempoTotal) {

            tempoTotal.textContent =
                formatarTempoResumo(tempo);
        }
    }


    // ==========================================
    // MOSTRAR LISTA
    // ==========================================

    function atualizarLista(historico) {

        if (!listaCorridas) {
            return;
        }


        listaCorridas.innerHTML = "";


        // Sem corridas

        if (historico.length === 0) {

            if (historicoVazio) {
                historicoVazio.style.display =
                    "block";
            }

            return;
        }


        if (historicoVazio) {
            historicoVazio.style.display =
                "none";
        }


        // Mais recente primeiro

        const corridas =
            [...historico].reverse();


        corridas.forEach(
            (corrida, indice) => {

                const item =
                    document.createElement("div");

                item.className =
                    "historico-corrida";


                const distancia =
                    Number(
                        corrida.distancia
                    ) || 0;


                const tempo =
                    Number(
                        corrida.tempo
                    ) || 0;


                const calorias =
                    Number(
                        corrida.calorias
                    ) || 0;


                item.innerHTML = `

                    <div class="historico-corrida-info">

                        <span class="historico-corrida-data">
                            ${formatarData(corrida.data)}
                        </span>

                        <strong>
                            Corrida ${historico.length - indice}
                        </strong>

                    </div>


                    <div class="historico-corrida-metricas">

                        <div>
                            <span>Distância</span>
                            <strong>
                                ${distancia
                                    .toFixed(2)
                                    .replace(".", ",")} km
                            </strong>
                        </div>


                        <div>
                            <span>Tempo</span>
                            <strong>
                                ${formatarTempo(tempo)}
                            </strong>
                        </div>


                        <div>
                            <span>Calorias</span>
                            <strong>
                                ${calorias} kcal
                            </strong>
                        </div>

                    </div>
                `;


                listaCorridas.appendChild(item);
            }
        );
    }


    // ==========================================
    // ATUALIZAR TUDO
    // ==========================================

    async function atualizarHistorico() {

        const historico = await obterHistorico();

        atualizarResumo(
            historico
        );

        atualizarLista(
            historico
        );

        console.log(
            "Histórico carregado:",
            historico
        );
    }


    // ==========================================
    // VOLTAR
    // ==========================================

    if (voltarButton) {

        voltarButton.addEventListener(
            "click",
            event => {

                event.preventDefault();

                window.location.href =
                    "home.php";
            }
        );
    }


    // ==========================================
    // LIMPAR HISTÓRICO
    // ==========================================

    if (limparButton) {

        limparButton.addEventListener(
            "click",
            () => {

                const historico =
                    obterHistorico();


                if (historico.length === 0) {

                    alert(
                        "Não existe nenhum histórico para limpar."
                    );

                    return;
                }


                const confirmar =
                    confirm(
                        "Tem certeza que deseja apagar todas as corridas do histórico?"
                    );


                if (!confirmar) {
                    return;
                }


                localStorage.removeItem(
                    "historicoCorridas"
                );


                atualizarHistorico();


                alert(
                    "Histórico limpo com sucesso!"
                );

            }
        );
    }


    // ==========================================
    // INICIAR
    // ==========================================

    atualizarHistorico();


    console.log(
        "✅ Sistema de histórico pronto."
    );

});
