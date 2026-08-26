document.addEventListener("DOMContentLoaded", () => {

    // =========================
    // VALIDAÇÃO DOS LINKS
    // =========================

    const links = document.querySelectorAll("a");

    links.forEach((link) => {

        link.addEventListener("click", (event) => {

            const destino = link.getAttribute("href");

            if (
                !destino ||
                destino.trim() === "" ||
                destino === "#"
            ) {
                event.preventDefault();

                alert("Este link não possui um destino válido.");
            }
        });
    });


    // =========================
    // VALIDAÇÃO DO GPS
    // =========================

    const btnGPS = document.getElementById("btnGPS");

    if (btnGPS) {

        btnGPS.addEventListener("click", () => {

            if (!navigator.geolocation) {

                alert(
                    "Seu navegador não suporta localização GPS."
                );

                return;
            }

            navigator.geolocation.getCurrentPosition(

                () => {
                    alert("Localização GPS autorizada.");
                },

                () => {
                    alert(
                        "Não foi possível acessar sua localização."
                    );
                }
            );
        });
    }


    // =========================
    // VALIDAÇÃO DAS NOTIFICAÇÕES
    // =========================

    const notificacoes =
        document.getElementById("notificacoes");

    if (notificacoes) {

        notificacoes.addEventListener("change", () => {

            if (notificacoes.checked) {
                console.log("Notificações ativadas.");
            } else {
                console.log("Notificações desativadas.");
            }

        });
    }

});