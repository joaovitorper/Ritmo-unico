<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Configurações | Ritmo Único</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #090E1A;
            color: #fff;
            display: flex;
            justify-content: center;
            padding: 60px 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
        }

        .card {
            background: #141C2C;
            border: 1px solid rgba(255, 255, 255, .06);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .35);
        }

        .subtitle {
            color: #57C7FF;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 34px;
            margin-bottom: 8px;
        }

        .description {
            color: #9AA4B8;
            margin-bottom: 35px;
        }

        .section {
            margin-bottom: 35px;
        }

        .section h2 {
            font-size: 20px;
            margin-bottom: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
        }

        label {
            color: #AEB8CC;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input {
            background: #0D1525;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 14px;
            padding: 15px;
            color: #fff;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #58C8FF;
        }

        .switch {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px;
            background: #0D1525;
            border-radius: 16px;
            margin-bottom: 15px;
            color: #AEB8CC;
        }

        .switch input {
            width: 20px;
            height: 20px;
            accent-color: #58C8FF;
        }

        .button {
            width: 100%;
            padding: 17px;
            border: none;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(90deg, #5667F2, #58C8FF);
            transition: .3s;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(88, 200, 255, .25);
        }

        .voltar {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #58C8FF;
            text-decoration: none;
            font-size: 14px;
        }

        .voltar:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {

            .card {
                padding: 25px;
            }

            h1 {
                font-size: 28px;
            }

        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <div class="subtitle">
            CONFIGURAÇÕES
        </div>

        <h1>
            Personalize sua conta
        </h1>

        <p class="description">
            Altere suas informações e preferências de uso.
        </p>

        <form
            action="../config/atualizar_configuracoes.php"
            method="POST"
        >

            <div class="section">

                <h2>
                    Perfil
                </h2>

                <div class="field">

                    <label for="nome">
                        Nome
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Seu nome"
                        autocomplete="name"
                    >

                </div>

                <div class="field">

                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="email@exemplo.com"
                        autocomplete="email"
                    >

                </div>

                <div class="field">

                    <label for="objetivo">
                        Objetivo semanal (km)
                    </label>

                    <input
                        type="number"
                        id="objetivo"
                        name="objetivo"
                        placeholder="20"
                        min="1"
                    >

                </div>

            </div>

            <div class="section">

                <h2>
                    Preferências
                </h2>

                <label class="switch">

                    <span>
                        Receber notificações
                    </span>

                    <input
                        type="checkbox"
                        name="notificacoes"
                        value="1"
                        checked
                    >

                </label>

                <label class="switch">

                    <span>
                        Modo escuro
                    </span>

                    <input
                        type="checkbox"
                        name="modo_escuro"
                        value="1"
                        checked
                    >

                </label>

                <label class="switch">

                    <span>
                        Compartilhar estatísticas
                    </span>

                    <input
                        type="checkbox"
                        name="compartilhar_estatisticas"
                        value="1"
                    >

                </label>

            </div>

            <button
                type="submit"
                class="button"
            >
                Salvar Alterações
            </button>

        </form>

        <a
            href="home.php"
            class="voltar"
        >
            ← Voltar para início
        </a>

    </div>

</div>

</body>

</html>