const formCadastro = document.getElementById("formCadastro");

const nome = document.getElementById("nome");
const email = document.getElementById("email");
const dataNascimento = document.getElementById("data-nascimento");
const senha = document.getElementById("senha");
const confirmarSenha = document.getElementById("confirmar-senha");
const termos = document.getElementById("termos");

const hoje = new Date().toISOString().split("T")[0];

if (dataNascimento) {
    dataNascimento.max = hoje;
}

function emailValido(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

if (formCadastro) {

    formCadastro.addEventListener("submit", function (event) {

        event.preventDefault();

        let valido = true;

        document.querySelectorAll(".erro").forEach(function (elemento) {
            elemento.textContent = "";
        });

        // NOME
        const nomeValor = nome.value.trim();

        if (!nomeValor) {

            document.getElementById("erroNome").textContent =
                "Digite seu nome completo.";

            valido = false;

        } else if (nomeValor.length < 3) {

            document.getElementById("erroNome").textContent =
                "O nome deve ter pelo menos 3 caracteres.";

            valido = false;

        } else if (!nomeValor.includes(" ")) {

            document.getElementById("erroNome").textContent =
                "Digite seu nome e sobrenome.";

            valido = false;
        }

        // E-MAIL
        const emailValor = email.value.trim().toLowerCase();

        if (!emailValor) {

            document.getElementById("erroEmail").textContent =
                "Digite seu e-mail.";

            valido = false;

        } else if (!emailValido(emailValor)) {

            document.getElementById("erroEmail").textContent =
                "Digite um e-mail válido.";

            valido = false;
        }

        // DATA DE NASCIMENTO
        if (!dataNascimento.value) {

            document.getElementById("erroData").textContent =
                "Informe sua data de nascimento.";

            valido = false;

        } else {

            const nascimento = new Date(
                dataNascimento.value + "T00:00:00"
            );

            const hojeData = new Date();

            let idade =
                hojeData.getFullYear() -
                nascimento.getFullYear();

            const mes =
                hojeData.getMonth() -
                nascimento.getMonth();

            if (
                mes < 0 ||
                (
                    mes === 0 &&
                    hojeData.getDate() < nascimento.getDate()
                )
            ) {
                idade--;
            }

            if (nascimento > hojeData) {

                document.getElementById("erroData").textContent =
                    "A data de nascimento não pode ser futura.";

                valido = false;

            } else if (idade < 13) {

                document.getElementById("erroData").textContent =
                    "É necessário ter pelo menos 13 anos.";

                valido = false;

            } else if (idade > 120) {

                document.getElementById("erroData").textContent =
                    "Digite uma data de nascimento válida.";

                valido = false;
            }
        }

        // SENHA
        const senhaValor = senha.value;

        if (!senhaValor) {

            document.getElementById("erroSenha").textContent =
                "Digite uma senha.";

            valido = false;

        } else if (senhaValor.length < 6) {

            document.getElementById("erroSenha").textContent =
                "A senha deve ter pelo menos 6 caracteres.";

            valido = false;
        }

        // CONFIRMAR SENHA
        const confirmarSenhaValor = confirmarSenha.value;

        if (!confirmarSenhaValor) {

            document.getElementById("erroConfirmarSenha").textContent =
                "Confirme sua senha.";

            valido = false;

        } else if (confirmarSenhaValor !== senhaValor) {

            document.getElementById("erroConfirmarSenha").textContent =
                "As senhas não são iguais.";

            valido = false;
        }

        // TERMOS
        if (!termos.checked) {

            document.getElementById("erroTermos").textContent =
                "Você precisa aceitar os termos de uso.";

            valido = false;
        }

        // ENVIA PARA O PHP
        if (valido) {
            formCadastro.submit();
        }

    });

}