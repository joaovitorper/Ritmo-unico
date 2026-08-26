<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$nomeUsuario = $_SESSION["usuario_nome"] ?? "Corredor";

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Home | Ritmo Único</title>

    <link
        rel="stylesheet"
        href="../css/home.css"
    >

</head>

<body>

    <main class="home-page">

        <div class="home-glow home-glow-left"></div>
        <div class="home-glow home-glow-right"></div>

        <div class="home-container">

            <img
                src="../assets/img/identidade Visual/Final logo.png"
                alt="Logo Ritmo Único"
                class="home-logo"
            >

            <span class="home-tag">
                Tecnologia para corredores
            </span>

            <section class="home-header">

                <span class="home-label">
                    Ritmo Único
                </span>

                <h1>
                    Seu ritmo. Sua evolução.
                </h1>

                <p>
                    Olá, <?= htmlspecialchars($nomeUsuario) ?>!
                    Acompanhe suas corridas, seus objetivos e sua evolução em um só lugar.
                </p>

            </section>

            <section class="home-cards">

                <a
                    href="dashboard.php"
                    class="home-card"
                >

                    <span class="home-card-label">
                        Desempenho
                    </span>

                    <h2>
                        Dashboard
                    </h2>

                    <p>
                        Acompanhe seus dados e sua evolução.
                    </p>

                </a>

                <a
                    href="corrida.php"
                    class="home-card"
                >

                    <span class="home-card-label">
                        Treino
                    </span>

                    <h2>
                        Iniciar corrida
                    </h2>

                    <p>
                        Comece uma nova corrida e registre seu desempenho.
                    </p>

                </a>

                <a
                    href="historico.php"
                    class="home-card"
                >

                    <span class="home-card-label">
                        Atividades
                    </span>

                    <h2>
                        Histórico
                    </h2>

                    <p>
                        Veja suas corridas e acompanhe sua evolução.
                    </p>

                </a>

                <a
                    href="perfil.php"
                    class="home-card"
                >

                    <span class="home-card-label">
                        Conta
                    </span>

                    <h2>
                        Meu perfil
                    </h2>

                    <p>
                        Consulte e atualize suas informações.
                    </p>

                </a>

            </section>

            <section class="home-destaque">

                <span class="home-label">
                    Seu objetivo
                </span>

                <h2>
                    Continue no seu ritmo
                </h2>

                <p>
                    Registre seus treinos e acompanhe seu progresso para alcançar seus objetivos.
                </p>

                <a
                    href="corrida.php"
                    class="home-button"
                >
                    Iniciar corrida
                </a>

            </section>

            <nav class="home-menu">

                <a href="home.php">
                    Início
                </a>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="corrida.php">
                    Corrida
                </a>

                <a href="historico.php">
                    Histórico
                </a>

                <a href="mapa.php">
                    Mapa
                </a>

                <a href="perfil.php">
                    Perfil
                </a>

                <a href="configuracoes.php">
                    Configurações
                </a>

            </nav>

        </div>

    </main>

</body>

</html>