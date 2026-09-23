const formPerfil = document.getElementById("formPerfil");

if (formPerfil) {
    const nome = document.getElementById("nome");
    const email = document.getElementById("email");
    const idade = document.getElementById("idade");
    const objetivo = document.getElementById("objetivo");
    const perfilStatus = document.getElementById("perfilStatus");

    const erroNome = document.getElementById("erroNome");
    const erroEmail = document.getElementById("erroEmail");
    const erroIdade = document.getElementById("erroIdade");
    const erroObjetivo = document.getElementById("erroObjetivo");

    formPerfil.addEventListener("submit", async function (event) {
        event.preventDefault();

        let valido = true;

        document.querySelectorAll(".erro").forEach(function (elemento) {
            elemento.textContent = "";
        });

        const nomeValor = nome.value.trim();
        const emailValor = email.value.trim();
        const idadeValor = idade.value.trim();
        const objetivoValor = objetivo.value.trim();

        if (nomeValor.length < 3) {
            erroNome.textContent = "Digite seu nome completo.";
            valido = false;
        } else if (!nomeValor.includes(" ")) {
            erroNome.textContent = "Digite seu nome e sobrenome.";
            valido = false;
        }

        const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailValor) {
            erroEmail.textContent = "Digite seu e-mail.";
            valido = false;
        } else if (!emailValido.test(emailValor)) {
            erroEmail.textContent = "Digite um e-mail válido.";
            valido = false;
        }

        if (idadeValor !== "") {
            const idadeNumero = Number(idadeValor);

            if (!Number.isInteger(idadeNumero)) {
                erroIdade.textContent = "Digite uma idade válida.";
                valido = false;
            } else if (idadeNumero < 13 || idadeNumero > 120) {
                erroIdade.textContent = "Digite uma idade válida.";
                valido = false;
            }
        }

        if (!objetivoValor) {
            erroObjetivo.textContent = "";
        }

        if (!valido) {
            return;
        }

        const formData = new FormData(formPerfil);

        try {
            const resposta = await fetch("editar-perfil.php", {
                method: "POST",
                body: formData
            });

            const dados = await resposta.json();

            if (!dados || !dados.sucesso) {
                throw new Error(dados?.mensagem || "Não foi possível salvar o perfil.");
            }

            perfilStatus.textContent = dados.mensagem || "Perfil salvo com sucesso!";
            perfilStatus.style.color = "#41D8FF";

            setTimeout(function () {
                window.location.href = "perfil.php";
            }, 1000);
        } catch (erro) {
            perfilStatus.textContent = erro.message;
            perfilStatus.style.color = "#ff7b7b";
        }
    });
}