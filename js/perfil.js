const formPerfil = document.getElementById("formPerfil");

const nome = document.getElementById("nome");
const email = document.getElementById("email");
const idade = document.getElementById("idade");
const objetivo = document.getElementById("objetivo");

const erroNome = document.getElementById("erroNome");
const erroEmail = document.getElementById("erroEmail");
const erroIdade = document.getElementById("erroIdade");
const erroObjetivo = document.getElementById("erroObjetivo");

formPerfil.addEventListener("submit", function(event) {

    event.preventDefault();

    let valido = true;

    document.querySelectorAll(".erro").forEach(function(elemento) {
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

    const emailValido =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailValor) {

        erroEmail.textContent = "Digite seu e-mail.";

        valido = false;

    } else if (!emailValido.test(emailValor)) {

        erroEmail.textContent = "Digite um e-mail válido.";

        valido = false;
    }

    const idadeNumero = Number(idadeValor);

    if (!idadeValor) {

        erroIdade.textContent = "Informe sua idade.";

        valido = false;

    } else if (!Number.isInteger(idadeNumero)) {

        erroIdade.textContent = "Digite uma idade válida.";

        valido = false;

    } else if (idadeNumero < 13) {

        erroIdade.textContent =
            "É necessário ter pelo menos 13 anos.";

        valido = false;

    } else if (idadeNumero > 120) {

        erroIdade.textContent =
            "Digite uma idade válida.";

        valido = false;
    }

    if (!objetivoValor) {

        erroObjetivo.textContent =
            "Selecione seu objetivo.";

        valido = false;
    }

    if (valido) {

        alert("Perfil atualizado com sucesso!");

    }

});