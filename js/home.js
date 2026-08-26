document.addEventListener("DOMContentLoaded", () => {

    // =========================
    // LINKS DA HOME
    // =========================

    const links = document.querySelectorAll(
        ".home-cards a, .home-button, .home-menu a"
    );

    links.forEach((link) => {

        link.addEventListener("click", (event) => {

            const destino = link.getAttribute("href");

            // Verifica se o link possui um destino válido
            if (!destino || destino.trim() === "" || destino === "#") {

                event.preventDefault();

                alert("Este link não possui um destino válido.");

                return;
            }

        });

    });

    // =========================
    // CARD DE DESTAQUE
    // =========================

    const destaque = document.querySelector(".home-destaque");

    if (destaque) {
        destaque.setAttribute(
            "aria-label",
            "Área de objetivo e início da corrida"
        );
    }

    // =========================
    // MENU ATIVO
    // =========================

    const menuLinks = document.querySelectorAll(".home-menu a");

    const paginaAtual =
        window.location.pathname.split("/").pop();

    menuLinks.forEach((link) => {

        const destino =
            link.getAttribute("href");

        if (destino === paginaAtual) {
            link.classList.add("active");
        }

    });

});