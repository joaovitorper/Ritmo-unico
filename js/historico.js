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
    // LER HISTÓRICO DO BANCO
    // ==========================================

    function obterHistoricoLocal() {

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

            return historico.map(
                (corrida) => ({
                    id: corrida.id ?? null,
                    usuario_id: corrida.usuario_id ?? null,
                    distancia: Number(corrida.distancia ?? 0),
                    tempo: Number(corrida.tempo ?? 0),
                    ritmo: Number(corrida.ritmo ?? 0),
                    data: corrida.data_corrida || corrida.data || null,
                    calorias: corrida.calorias !== undefined && corrida.calorias !== null
                        ? Number(corrida.calorias)
                        : Math.round(Number(corrida.distancia ?? 0) * 70)
                })
            );

        } catch (erro) {

            console.error(
                "Erro ao carregar histórico local:",
                erro
            );

            return [];
        }
    }

    async function obterHistorico() {

        try {

            const resposta = await fetch(
                "../api/v1/corridas/listar.php",
                {
                    method: "GET",
                    credentials: "same-origin",
                    cache: "no-store"
                }
            );


            console.log(
                "Status da API:",
                resposta.status
            );


            if (
                resposta.status === 401 ||
                resposta.status === 403
            ) {

                console.warn(
                    "Usuário não autenticado. Usando fallback local."
                );

                return obterHistoricoLocal();
            }


            if (!resposta.ok) {

                throw new Error(
                    "Erro ao consultar o histórico."
                );
            }


            const dados =
                await resposta.json();


            console.log(
                "Dados recebidos da API:",
                dados
            );


            if (
                !dados ||
                !Array.isArray(dados.corridas)
            ) {

                console.warn(
                    "A API não retornou uma lista de corridas."
                );

                return obterHistoricoLocal();
            }


            return dados.corridas.map(
                (corrida) => {

                    return {

                        id:
                            corrida.id ?? null,

                        usuario_id:
                            corrida.usuario_id ?? null,

                        distancia:
                            Number(
                                corrida.distancia ?? 0
                            ),

                        tempo:
                            Number(
                                corrida.tempo ?? 0
                            ),

                        ritmo:
                            Number(
                                corrida.ritmo ?? 0
                            ),

                        data:
                            corrida.data_corrida ||
                            corrida.data ||
                            null,

                        calorias:
                            corrida.calorias !== undefined &&
                            corrida.calorias !== null
                                ? Number(corrida.calorias)
                                : Math.round(
                                    Number(
                                        corrida.distancia ?? 0
                                    ) * 70
                                )
                    };
                }
            );

        } catch (erro) {

            console.error(
                "Erro ao carregar histórico do banco:",
                erro
            );

            return obterHistoricoLocal();
        }
    }


    // ==========================================
    // FORMATAR TEMPO
    // ==========================================

    function formatarTempo(segundos) {

        segundos =
            Number(segundos) || 0;

        const horas =
            Math.floor(
                segundos / 3600
            );

        const minutos =
            Math.floor(
                (segundos % 3600) / 60
            );

        const segundosRestantes =
            Math.floor(
                segundos % 60
            );


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
            Math.floor(
                segundos / 3600
            );

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


        let dataObj;


        // MySQL:
        // 2026-09-23 13:30:00

        if (
            typeof data === "string" &&
            /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(data)
        ) {

            dataObj =
                new Date(
                    data.replace(" ", "T")
                );

        } else {

            dataObj =
                new Date(data);
        }


        if (
            isNaN(
                dataObj.getTime()
            )
        ) {

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


        historico.forEach(
            (corrida) => {

                distancia +=
                    Number(
                        corrida.distancia
                    ) || 0;

                tempo +=
                    Number(
                        corrida.tempo
                    ) || 0;
            }
        );


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
                formatarTempoResumo(
                    tempo
                );
        }
    }


    // ==========================================
    // MOSTRAR LISTA
    // ==========================================

    function atualizarLista(historico) {

        if (!listaCorridas) {

            console.error(
                "Elemento listaHistorico não encontrado."
            );

            return;
        }


        listaCorridas.innerHTML = "";


        // ==========================================
        // SEM CORRIDAS
        // ==========================================

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


        // ==========================================
        // MAIS RECENTE PRIMEIRO
        // ==========================================

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


                const numeroCorrida =
                    historico.length - indice;


                item.innerHTML = `

                    <div class="historico-corrida-info">

                        <span class="historico-corrida-data">
                            ${formatarData(corrida.data)}
                        </span>

                        <strong>
                            Corrida ${numeroCorrida}
                        </strong>

                    </div>


                    <div class="historico-corrida-metricas">

                        <div>

                            <span>
                                Distância
                            </span>

                            <strong>
                                ${
                                    distancia
                                        .toFixed(2)
                                        .replace(".", ",")
                                } km
                            </strong>

                        </div>


                        <div>

                            <span>
                                Tempo
                            </span>

                            <strong>
                                ${formatarTempo(tempo)}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Calorias
                            </span>

                            <strong>
                                ${calorias} kcal
                            </strong>

                        </div>

                    </div>
                `;


                listaCorridas.appendChild(
                    item
                );
            }
        );
    }


    // ==========================================
    // ATUALIZAR HISTÓRICO
    // ==========================================

    async function atualizarHistorico() {

        const historico =
            await obterHistorico();


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
            (event) => {

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
            async () => {

                const confirmar =
                    confirm(
                        "Tem certeza que deseja apagar todas as corridas do histórico?"
                    );


                if (!confirmar) {

                    return;
                }


                alert(
                    "As corridas salvas no banco precisam ser excluídas pelo sistema do servidor."
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