const formLogin = document.getElementById("formLogin");
const email = document.getElementById("email");
const senha = document.getElementById("senha");

const recuperarSenha = document.getElementById("recuperarSenha");
const loginArea = document.getElementById("loginArea");
const recuperacao = document.getElementById("recuperacao");
const formRecuperacao = document.getElementById("formRecuperacao");
const voltarLogin = document.getElementById("voltarLogin");
const emailRecuperacao = document.getElementById("emailRecuperacao");

function emailValido(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

formLogin.addEventListener("submit", function(event) {

    event.preventDefault();

    let valido = true;

    document.querySelectorAll(".erro").forEach(function(elemento) {
        elemento.textContent = "";
    });

    const emailValor = email.value.trim();

    if (!emailValor) {

        document.getElementById("erroEmail").textContent =
            "Digite seu e-mail.";

        valido = false;

    } else if (!emailValido(emailValor)) {

        document.getElementById("erroEmail").textContent =
            "Digite um e-mail válido.";

        valido = false;
    }

    if (!senha.value) {

        document.getElementById("erroSenha").textContent =
            "Digite sua senha.";

        valido = false;

    } else if (senha.value.length < 6) {

        document.getElementById("erroSenha").textContent =
            "A senha deve ter pelo menos 6 caracteres.";

        valido = false;
    }

    if (valido) {

        alert("Dados de login validados com sucesso!");

    }

});

recuperarSenha.addEventListener("click", function(event) {

    event.preventDefault();

    loginArea.style.display = "none";
    recuperacao.style.display = "block";

    emailRecuperacao.value = "";

    document.getElementById("erroRecuperacao").textContent = "";

});

voltarLogin.addEventListener("click", function() {

    recuperacao.style.display = "none";
    loginArea.style.display = "block";

    document.getElementById("erroRecuperacao").textContent = "";

});

formRecuperacao.addEventListener("submit", function(event) {

    event.preventDefault();

    const erroRecuperacao =
        document.getElementById("erroRecuperacao");

    const emailValor =
        emailRecuperacao.value.trim();

    erroRecuperacao.textContent = "";

    if (!emailValor) {

        erroRecuperacao.textContent =
            "Digite seu e-mail.";

        return;
    }

    if (!emailValido(emailValor)) {

        erroRecuperacao.textContent =
            "Digite um e-mail válido.";

        return;
    }

    alert(
        "Se esse e-mail estiver cadastrado, você receberá as instruções para recuperar sua senha."
    );

});