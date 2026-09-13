<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Criar conta | Ritmo Único</title>

    <link rel="stylesheet" href="../css/cadastro.css">
</head>

<body>

<div class="cadastro-page">

    <span class="cadastro-glow cadastro-glow-left"></span>
    <span class="cadastro-glow cadastro-glow-right"></span>

    <main class="cadastro-container">

        <div class="cadastro-content">

            <h1>Ritmo Único</h1>

            <span class="cadastro-tag">
                Tecnologia para corredores
            </span>

            <div class="cadastro-box">

                <h2>Crie sua conta</h2>

                <p class="cadastro-description">
                    Cadastre-se para acompanhar sua evolução na corrida.
                </p>

                <form
                    id="formCadastro"
                    action="../src/cadastrar.php"
                    method="POST"
                >

                    <div class="input-group">

                        <label for="nome">
                            Nome completo
                        </label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            maxlength="150"
                            autocomplete="name"
                            required
                        >

                        <span class="erro" id="erroNome"></span>

                    </div>

                    <div class="input-group">

                        <label for="email">
                            E-mail
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="150"
                            autocomplete="email"
                            required
                        >

                        <span class="erro" id="erroEmail"></span>

                    </div>

                    <div class="input-group">

                        <label for="data-nascimento">
                            Data de nascimento
                        </label>

                        <input
                            type="date"
                            id="data-nascimento"
                            name="data_nascimento"
                            autocomplete="bday"
                            required
                        >

                        <span class="erro" id="erroData"></span>

                    </div>

                    <div class="input-group">

                        <label for="senha">
                            Senha
                        </label>

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >

                        <span class="erro" id="erroSenha"></span>

                    </div>

                    <div class="input-group">

                        <label for="confirmar-senha">
                            Confirmar senha
                        </label>

                        <input
                            type="password"
                            id="confirmar-senha"
                            name="confirmar_senha"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >

                        <span class="erro" id="erroConfirmarSenha"></span>

                    </div>

                    <label class="terms" for="termos">

                        <input
                            type="checkbox"
                            id="termos"
                            name="termos"
                            required
                        >

                        <span>
                            Aceito os termos de uso da plataforma.

                            <span
                                class="erro"
                                id="erroTermos"
                            ></span>
                        </span>

                    </label>

                    <button
                        type="submit"
                        class="cadastro-button"
                    >
                        Criar conta
                    </button>

                </form>

                <div class="divider">
                    <span>ou</span>
                </div>

                <p class="already-account">

                    Já possui uma conta?

                    <a href="login.php">
                        Entrar
                    </a>

                </p>

            </div>

            <a
                href="../index.php"
                class="back-home"
            >
                ← Voltar para o início
            </a>

        </div>

    </main>

</div>

</body>
</html>