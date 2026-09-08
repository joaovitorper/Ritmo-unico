<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corrida | Ritmo Único</title>

    <link rel="stylesheet" href="corrida.css">
</head>

<body>

    <main class="corrida-page">

        <div class="corrida-glow corrida-glow-left"></div>
        <div class="corrida-glow corrida-glow-right"></div>

        <div class="corrida-container">

            <div class="corrida-content">

                <div class="corrida-logo">
                    ↗
                </div>

                <span class="corrida-subtitle">
                    Tecnologia para corredores
                </span>

                <span class="corrida-tag">
                    Ritmo Único
                </span>

                <h1>
                    Sua corrida. Sua evolução.
                </h1>

                <p class="corrida-description">
                    Registre seu treino e acompanhe seu desempenho em tempo real.
                </p>

                <section class="corrida-box">

                    <div class="corrida-box-header">
                        <div class="corrida-label">
                            TREINO
                        </div>

                        <h2>
                            Corrida em andamento
                        </h2>

                        <p class="corrida-box-description">
                            Mantenha seu ritmo e acompanhe suas métricas.
                        </p>
                    </div>

                    <div class="corrida-timer-box">

                        <div class="corrida-timer-label">
                            TEMPO DE CORRIDA
                        </div>

                        <div class="corrida-timer" id="timer">
                            00:00:00
                        </div>

                    </div>

                    <div class="corrida-metrics">

                        <div class="corrida-metric">
                            <span class="corrida-metric-label">
                                Distância
                            </span>

                            <div>
                                <strong class="corrida-metric-value" id="distance">
                                    0.00
                                </strong>

                                <span class="corrida-metric-unit">
                                    km
                                </span>
                            </div>
                        </div>

                        <div class="corrida-metric">
                            <span class="corrida-metric-label">
                                Ritmo
                            </span>

                            <div>
                                <strong class="corrida-metric-value" id="pace">
                                    0:00
                                </strong>

                                <span class="corrida-metric-unit">
                                    /km
                                </span>
                            </div>
                        </div>

                        <div class="corrida-metric">
                            <span class="corrida-metric-label">
                                Calorias
                            </span>

                            <div>
                                <strong class="corrida-metric-value" id="calories">
                                    0
                                </strong>

                                <span class="corrida-metric-unit">
                                    kcal
                                </span>
                            </div>
                        </div>

                    </div>

                    <div class="corrida-actions">

                        <button
                            class="corrida-button corrida-button-primary"
                            id="startButton"
                            onclick="toggleRun()">
                            Iniciar corrida
                        </button>

                        <button
                            class="corrida-button corrida-button-secondary"
                            onclick="resetRun()">
                            Encerrar
                        </button>

                    </div>

                </section>

                <section class="corrida-goal">

                    <div class="corrida-goal-header">

                        <span class="corrida-goal-title">
                            Objetivo da corrida
                        </span>

                        <span class="corrida-goal-value" id="goalValue">
                            0.00 / 5 km
                        </span>

                    </div>

                    <div class="corrida-progress">
                        <div
                            class="corrida-progress-bar"
                            id="progressBar">
                        </div>
                    </div>

                </section>

                <nav class="corrida-navigation">

                    <div class="corrida-nav-item">Início</div>
                    <div class="corrida-nav-item">Dashboard</div>
                    <div class="corrida-nav-item active">Corrida</div>
                    <div class="corrida-nav-item">Histórico</div>
                    <div class="corrida-nav-item">Mapa</div>
                    <div class="corrida-nav-item">Perfil</div>
                    <div class="corrida-nav-item">Configurações</div>

                </nav>

            </div>

        </div>

    </main>

    <script>
        let seconds = 0;
        let running = false;
        let interval = null;

        const timer = document.getElementById("timer");
        const startButton = document.getElementById("startButton");

        function formatTime(totalSeconds) {
            const hours = Math.floor(totalSeconds / 3600);

            const minutes = Math.floor(
                (totalSeconds % 3600) / 60
            );

            const secs = totalSeconds % 60;

            return (
                String(hours).padStart(2, "0") +
                ":" +
                String(minutes).padStart(2, "0") +
                ":" +
                String(secs).padStart(2, "0")
            );
        }

        function updateRun() {
            seconds++;

            timer.textContent = formatTime(seconds);

            const distance = seconds / 600;

            document.getElementById("distance").textContent =
                distance.toFixed(2);

            document.getElementById("calories").textContent =
                Math.floor(seconds * 0.15);

            const paceSeconds = seconds / distance;

            const paceMinutes =
                Math.floor(paceSeconds / 60);

            const paceSecondsRest =
                Math.floor(paceSeconds % 60);

            document.getElementById("pace").textContent =
                paceMinutes +
                ":" +
                String(paceSecondsRest).padStart(2, "0");

            const progress =
                Math.min((distance / 5) * 100, 100);

            document.getElementById("progressBar").style.width =
                progress + "%";

            document.getElementById("goalValue").textContent =
                distance.toFixed(2) + " / 5 km";
        }

        function toggleRun() {
            if (!running) {

                running = true;

                startButton.textContent =
                    "Pausar corrida";

                interval = setInterval(updateRun, 1000);

            } else {

                running = false;

                clearInterval(interval);

                startButton.textContent =
                    "Continuar corrida";
            }
        }

        function resetRun() {
            running = false;

            clearInterval(interval);

            seconds = 0;

            timer.textContent = "00:00:00";

            document.getElementById("distance").textContent = "0.00";
            document.getElementById("pace").textContent = "0:00";
            document.getElementById("calories").textContent = "0";

            document.getElementById("progressBar").style.width = "0%";
            document.getElementById("goalValue").textContent = "0.00 / 5 km";

            startButton.textContent = "Iniciar corrida";
        }
    </script>

</body>
</html>