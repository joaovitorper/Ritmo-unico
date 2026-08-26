document.addEventListener("DOMContentLoaded", () => {

    // =========================
    // ELEMENTOS
    // =========================

    const voltarButton = document.getElementById("voltar");
    const limparButton = document.getElementById("limparHistorico");
    const listaHistorico = document.getElementById("listaHistorico");


    // =========================
    // VOLTAR PARA HOME
    // =========================

    if (voltarButton) {

        voltarButton.addEventListener("click", () => {
            window.location.href = "home.php";
        });

    }


    // =========================
    // LIMPAR HISTÓRICO
    // =========================

    if (limparButton) {

        limparButton.addEventListener("click", () => {

            const confirmar = confirm(
                "Tem certeza que deseja limpar seu histórico de corridas?"
            );

            if (!confirmar) {
                return;
            }

            // Remove o histórico salvo no navegador
            localStorage.removeItem("historicoCorridas");


            // Atualiza a lista
            if (listaHistorico) {

                listaHistorico.innerHTML = `
                    <p class="historico-vazio">
                        Nenhuma corrida registrada.
                    </p>
                `;

            }

            alert("Histórico limpo com sucesso.");

        });

    }

});