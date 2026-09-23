<?php
// Modo diagnóstico: true mostra erros de PHP na tela (útil se a página ficar em branco).
// Depois que tudo estiver funcionando, troque para false.
$MODO_DIAGNOSTICO = true;

if ($MODO_DIAGNOSTICO) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

session_start();

function redirecionarLogin()
{
    $base = dirname($_SERVER['PHP_SELF'] ?? '/');
    $base = rtrim($base, '/');
    $login = ($base === '' || $base === '.') ? '/login.php' : $base . '/login.php';

    header('Location: ' . $login);
    exit;
}

// Evita que o botão "voltar" do navegador mostre a página depois de sair
header('Cache-Control: no-store, no-cache, must-revalidate');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    redirecionarLogin();
}

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function gerarToken()
{
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes(32));
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }
    return md5(uniqid((string) mt_rand(), true));
}

function tokensIguais($a, $b)
{
    return function_exists('hash_equals') ? hash_equals($a, $b) : $a === $b;
}

$nome  = isset($_SESSION['usuario_nome'])  ? trim((string) $_SESSION['usuario_nome'])  : '';
$email = isset($_SESSION['usuario_email']) ? trim((string) $_SESSION['usuario_email']) : '';

if ($nome === '') {
    $nome = 'Usuário';
}

// Token para proteger o botão de sair
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = gerarToken();
}

// Sair da conta (via POST, confirmado na janela da página)
$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $acao === 'sair') {

    $tokenEnviado = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';

    if (tokensIguais($_SESSION['csrf'], $tokenEnviado)) {

        $_SESSION = array();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        redirecionarLogin();
    }
}

// Valores já escapados para usar no HTML
$nomeH        = e($nome);
$emailTexto   = $email !== '' ? e($email) : 'Seu perfil';
$emailPerfil  = $email !== '' ? e($email) : 'Não informado';
$csrf         = e($_SESSION['csrf']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Configurações - Ritmo Único</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/configuracoes.css">
</head>

<body>

<main class="config-page">

    <div class="config-glow config-glow-left"></div>
    <div class="config-glow config-glow-right"></div>

    <div class="config-container">

        <a href="mapa.php" class="back-home" id="voltar">← Voltar</a>

        <section class="config-content">

            <h1>Configurações</h1>

            <p class="config-tag">
                Personalize sua experiência no Ritmo Único
            </p>

            <div class="config-box">

                <div class="profile-header">

                    <div class="profile-icon" aria-hidden="true">👤</div>

                    <div>
                        <h2><?= $nomeH ?></h2>
                        <p><?= $emailTexto ?></p>
                    </div>

                </div>

                <div class="config-section">

                    <h3>Conta</h3>

                    <button type="button" class="config-option" data-abrir="janelaPerfil">

                        <span class="option-icon" aria-hidden="true">👤</span>

                        <span class="option-text">
                            <strong>Dados do perfil</strong>
                            <span>Visualize suas informações pessoais</span>
                        </span>

                        <span class="arrow" aria-hidden="true">›</span>

                    </button>

                    <button type="button" class="config-option" data-em-breve="Alterar senha ainda não está disponível.">

                        <span class="option-icon" aria-hidden="true">🔒</span>

                        <span class="option-text">
                            <strong>Segurança</strong>
                            <span>Gerencie sua senha e segurança</span>
                        </span>

                        <span class="arrow" aria-hidden="true">›</span>

                    </button>

                </div>

                <div class="config-section">

                    <h3>Preferências</h3>

                    <label class="config-option config-toggle">

                        <span class="option-icon" aria-hidden="true">🔔</span>

                        <span class="option-text">
                            <strong>Notificações</strong>
                            <span id="notifStatus">Receber notificações do aplicativo</span>
                        </span>

                        <span class="switch">
                            <input type="checkbox" id="notifPermission">
                            <span class="slider"></span>
                        </span>

                    </label>

                    <label class="config-option config-toggle">

                        <span class="option-icon" aria-hidden="true">📍</span>

                        <span class="option-text">
                            <strong>Localização</strong>
                            <span id="gpsStatus">Permitir uso do GPS durante as corridas</span>
                        </span>

                        <span class="switch">
                            <input type="checkbox" id="gpsPermission">
                            <span class="slider"></span>
                        </span>

                    </label>

                </div>

                <div class="config-section">

                    <h3>Sistema</h3>

                    <button type="button" class="config-option" id="abrirLocal">

                        <span class="option-icon" aria-hidden="true">🎯</span>

                        <span class="option-text">
                            <strong>Minha localização</strong>
                            <span>Veja onde o GPS está te encontrando agora</span>
                        </span>

                        <span class="arrow" aria-hidden="true">›</span>

                    </button>

                    <button type="button" class="config-option" id="abrirCorrida">

                        <span class="option-icon" aria-hidden="true">🏃</span>

                        <span class="option-text">
                            <strong>Testar GPS de corrida</strong>
                            <span>Veja tempo, distância e ritmo em tempo real</span>
                        </span>

                        <span class="arrow" aria-hidden="true">›</span>

                    </button>

                    <a href="mapa.php" class="config-option">

                        <span class="option-icon" aria-hidden="true">🗺️</span>

                        <span class="option-text">
                            <strong>Mapa e GPS</strong>
                            <span>Acesse sua localização e o mapa</span>
                        </span>

                        <span class="arrow" aria-hidden="true">›</span>

                    </a>

                </div>

                <div class="logout-area">

                    <button type="button" class="logout-button" data-abrir="janelaSair">
                        🚪 Sair da conta
                    </button>

                </div>

            </div>

            <p class="version">Ritmo Único • Configurações</p>

        </section>

    </div>

</main>

<!-- Janela: dados do perfil -->
<dialog class="modal" id="janelaPerfil" aria-labelledby="tituloPerfil">
    <div class="modal-card">
        <h2 id="tituloPerfil">Dados do perfil</h2>
        <p>Estas são as informações da sua conta.</p>

        <dl class="modal-data">
            <div>
                <dt>Nome</dt>
                <dd><?= $nomeH ?></dd>
            </div>
            <div>
                <dt>E-mail</dt>
                <dd><?= $emailPerfil ?></dd>
            </div>
        </dl>

        <div class="modal-actions">
            <button type="button" class="btn btn-primary" data-fechar>Fechar</button>
        </div>
    </div>
</dialog>

<!-- Janela: minha localização -->
<dialog class="modal" id="janelaLocal" aria-labelledby="tituloLocal">
    <div class="modal-card">
        <h2 id="tituloLocal">Minha localização</h2>
        <p id="locEstado">Procurando sinal do GPS…</p>
        <p class="ajuda" id="locAjuda" hidden></p>

        <dl class="modal-data" id="locDados" hidden>
            <div>
                <dt>Latitude</dt>
                <dd id="locLat">–</dd>
            </div>
            <div>
                <dt>Longitude</dt>
                <dd id="locLng">–</dd>
            </div>
            <div>
                <dt>Precisão</dt>
                <dd id="locPrecisao">–</dd>
            </div>
        </dl>

        <button type="button" class="btn btn-aprox" id="locAproximada" hidden>Usar localização aproximada</button>

        <iframe class="map-frame" id="locMapa" title="Mapa com a sua localização" hidden loading="lazy"></iframe>

        <div class="modal-actions">
            <button type="button" class="btn" id="atualizarLocal">Atualizar</button>
            <button type="button" class="btn btn-primary" data-fechar>Fechar</button>
        </div>
    </div>
</dialog>

<!-- Janela: testar GPS de corrida -->
<dialog class="modal" id="janelaCorrida" aria-labelledby="tituloCorrida">
    <div class="modal-card">
        <h2 id="tituloCorrida">Testar GPS de corrida</h2>
        <p class="run-status" id="corridaStatus">Procurando sinal do GPS…</p>
        <p class="ajuda" id="corridaAjuda" hidden></p>

        <div class="run-time" id="corridaTempo" aria-live="off">00:00</div>

        <div class="run-stats">
            <div class="run-stat"><span>Distância</span><strong id="corridaDist">0,00 km</strong></div>
            <div class="run-stat"><span>Ritmo médio</span><strong id="corridaRitmo">--:-- /km</strong></div>
            <div class="run-stat"><span>Velocidade</span><strong id="corridaVel">-- km/h</strong></div>
            <div class="run-stat"><span>Precisão do GPS</span><strong id="corridaPrec">--</strong></div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-primary" id="corridaBotao">Iniciar</button>
            <button type="button" class="btn" data-fechar>Fechar</button>
        </div>

        <p class="run-nota">Deixe a tela ligada e o navegador aberto. Com a tela apagada, o celular pode pausar o GPS.</p>
    </div>
</dialog>

<!-- Janela: confirmar saída -->
<dialog class="modal" id="janelaSair" aria-labelledby="tituloSair">
    <form class="modal-card" method="post" action="logout.php">
        <h2 id="tituloSair">Sair da conta?</h2>
        <p>Você precisará entrar novamente para usar o Ritmo Único.</p>

        <input type="hidden" name="csrf" value="<?= $csrf ?>">

        <div class="modal-actions">
            <button type="button" class="btn" data-fechar>Cancelar</button>
            <button type="submit" class="btn btn-danger">Sair da conta</button>
        </div>
    </form>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
(function () {

    /* ---------- Utilitários ---------- */

    function lerPref(chave, padrao) {
        try {
            var v = localStorage.getItem(chave);
            return v === null ? padrao : v === 'true';
        } catch (e) {
            return padrao;
        }
    }

    function salvarPref(chave, valor) {
        try {
            localStorage.setItem(chave, valor ? 'true' : 'false');
        } catch (e) {}
    }

    var toastEl = document.getElementById('toast');
    var toastTimer;

    function avisar(msg) {
        toastEl.textContent = msg;
        toastEl.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            toastEl.classList.remove('show');
        }, 2800);
    }

    /* ---------- Voltar ---------- */

    var voltar = document.getElementById('voltar');

    voltar.addEventListener('click', function (e) {
        // Volta para a página anterior; se não houver, segue o link (mapa.php)
        if (document.referrer && history.length > 1) {
            e.preventDefault();
            history.back();
        }
    });

    /* ---------- Janelas ---------- */

    document.querySelectorAll('[data-abrir]').forEach(function (botao) {
        botao.addEventListener('click', function () {
            var janela = document.getElementById(botao.dataset.abrir);
            if (janela && typeof janela.showModal === 'function') {
                janela.showModal();
            }
        });
    });

    document.querySelectorAll('dialog.modal').forEach(function (janela) {

        // Clique fora do cartão fecha a janela
        janela.addEventListener('click', function (e) {
            if (e.target === janela) janela.close();
        });

        janela.querySelectorAll('[data-fechar]').forEach(function (botao) {
            botao.addEventListener('click', function () {
                janela.close();
            });
        });
    });

    document.querySelectorAll('[data-em-breve]').forEach(function (botao) {
        botao.addEventListener('click', function () {
            avisar(botao.dataset.emBreve);
        });
    });

    /* ---------- Notificações ---------- */

    var notif       = document.getElementById('notifPermission');
    var notifStatus = document.getElementById('notifStatus');
    var temNotif    = 'Notification' in window;

    function mostrarStatusNotif(texto, alerta) {
        notifStatus.textContent = texto;
        notifStatus.classList.toggle('is-warn', !!alerta);
    }

    function estadoNotif() {
        if (!temNotif) {
            notif.checked = false;
            mostrarStatusNotif('Este navegador não suporta notificações.', true);
        } else if (Notification.permission === 'denied') {
            notif.checked = false;
            salvarPref('notificacoes', false);
            mostrarStatusNotif('Bloqueadas no navegador. Libere nas permissões do site.', true);
        } else if (Notification.permission === 'granted') {
            notif.checked = lerPref('notificacoes', true);
            mostrarStatusNotif(notif.checked ? 'Ativas neste dispositivo' : 'Desativadas', false);
        } else {
            notif.checked = false;
            mostrarStatusNotif('Ative para receber avisos do aplicativo', false);
        }
    }

    function pedirPermissaoNotif() {
        return new Promise(function (resolve) {
            try {
                // Navegadores antigos usam callback, os novos devolvem uma Promise
                var r = Notification.requestPermission(resolve);
                if (r && typeof r.then === 'function') r.then(resolve);
            } catch (e) {
                resolve('denied');
            }
        });
    }

    estadoNotif();

    notif.addEventListener('change', function () {

        if (!notif.checked) {
            salvarPref('notificacoes', false);
            mostrarStatusNotif('Desativadas', false);
            avisar('Notificações desativadas');
            return;
        }

        if (!temNotif) {
            estadoNotif();
            avisar('Este navegador não suporta notificações');
            return;
        }

        pedirPermissaoNotif().then(function (resultado) {
            if (resultado !== 'granted') {
                estadoNotif();
                avisar('Permissão de notificações negada');
                return;
            }

            salvarPref('notificacoes', true);
            mostrarStatusNotif('Ativas neste dispositivo', false);
            avisar('Notificações ativadas');

            // Notificação de teste, para mostrar que está funcionando
            try {
                new Notification('Ritmo Único', { body: 'Notificações ativadas com sucesso.' });
            } catch (e) {}
        });
    });

    /* ---------- GPS / Localização ---------- */

    var gps         = document.getElementById('gpsPermission');
    var gpsStatus   = document.getElementById('gpsStatus');
    var TEXTO_ATIVO = 'Ativo durante as corridas';
    var TEXTO_OFF   = 'Desativado';
    var TEXTO_PEDIR = 'Ative para permitir o uso do GPS';

    var temGeo = 'geolocation' in navigator;
    // O navegador só libera o GPS em HTTPS (ou em localhost)
    var seguro = window.isSecureContext !== false;

    function mostrarStatus(texto, alerta) {
        gpsStatus.textContent = texto;
        gpsStatus.classList.toggle('is-warn', !!alerta);
    }

    function descreverErro(erro) {
        if (!temGeo) {
            return {
                curto: 'Sem GPS neste dispositivo',
                longo: 'Este dispositivo ou navegador não tem GPS disponível.'
            };
        }
        if (!seguro) {
            return {
                curto: 'O GPS precisa de HTTPS',
                longo: 'O navegador só libera o GPS em conexão segura. Abra o site com https://.'
            };
        }
        switch (erro && erro.code) {
            case 1:
                return {
                    curto: 'Localização bloqueada no navegador',
                    longo: 'Localização bloqueada. Toque no cadeado ao lado do endereço do site e permita a localização.'
                };
            case 3:
                return {
                    curto: 'O GPS demorou para responder',
                    longo: 'O GPS demorou para responder. Tente de novo em um local aberto.'
                };
            default:
                return {
                    curto: 'Não foi possível achar sua posição',
                    longo: 'Não foi possível achar sua posição. Confira se o GPS do aparelho está ligado.'
                };
        }
    }

    // Passo a passo para liberar a localização, conforme o aparelho
    function textoLiberar() {
        var ua = navigator.userAgent || '';
        if (/iPhone|iPad|iPod/i.test(ua)) {
            return 'Como liberar: toque em "aA" na barra de endereço, depois em Configurações do site, Localização e Permitir. Depois, tente de novo.';
        }
        if (/Android/i.test(ua)) {
            return 'Como liberar: toque no ícone à esquerda do endereço, depois em Permissões, Localização e Permitir. Depois, tente de novo.';
        }
        return 'Como liberar: clique no ícone à esquerda do endereço do site, ative a Localização e tente de novo.';
    }

    function mostrarAjuda(elemento, erro) {
        var bloqueado = erro && erro.code === 1 && temGeo && seguro;
        elemento.hidden = !bloqueado;
        if (bloqueado) elemento.textContent = textoLiberar();
    }

    // Guarda a última posição para outras telas (ex.: mapa.php) poderem usar
    function guardarPosicao(pos) {
        try {
            localStorage.setItem('ultimaLocalizacao', JSON.stringify({
                lat: pos.coords.latitude,
                lng: pos.coords.longitude,
                precisao: pos.coords.accuracy,
                quando: Date.now()
            }));
        } catch (e) {}
    }

    // Tenta com alta precisão; se falhar por sinal ou tempo, tenta de novo com precisão normal
    function obterPosicao(sucesso, falha) {
        if (!temGeo || !seguro) {
            falha({ code: 0 });
            return;
        }

        try {
            navigator.geolocation.getCurrentPosition(
                sucesso,
                function (erro) {
                    if (erro.code === 1) {
                        falha(erro);
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        sucesso,
                        falha,
                        { enableHighAccuracy: false, timeout: 12000, maximumAge: 60000 }
                    );
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
            );
        } catch (e) {
            falha({ code: 0 });
        }
    }

    function estadoNormal() {
        mostrarStatus(gps.checked ? TEXTO_ATIVO : TEXTO_OFF, false);
    }

    function aplicarPermissao(estado) {
        if (estado === 'denied') {
            gps.checked = false;
            salvarPref('gpsPermission', false);
            mostrarStatus('Bloqueado no navegador. Libere a localização nas permissões do site.', true);
        } else if (estado === 'granted') {
            gps.checked = lerPref('gpsPermission', true);
            estadoNormal();
        } else {
            gps.checked = false;
            mostrarStatus(TEXTO_PEDIR, false);
        }
    }

    // Estado inicial: mostra o que o navegador realmente permite
    if (!temGeo || !seguro) {
        gps.checked = false;
        salvarPref('gpsPermission', false);
        mostrarStatus(descreverErro().longo, true);
    } else if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: 'geolocation' }).then(function (p) {
            aplicarPermissao(p.state);
            p.onchange = function () { aplicarPermissao(p.state); };
        }).catch(function () {
            gps.checked = lerPref('gpsPermission', false);
            estadoNormal();
        });
    } else {
        gps.checked = lerPref('gpsPermission', false);
        estadoNormal();
    }

    gps.addEventListener('change', function () {

        if (!gps.checked) {
            salvarPref('gpsPermission', false);
            estadoNormal();
            avisar('GPS desativado');
            return;
        }

        mostrarStatus('Procurando sinal do GPS…', false);

        obterPosicao(
            function (pos) {
                guardarPosicao(pos);
                salvarPref('gpsPermission', true);
                estadoNormal();
                avisar('GPS ativado');
            },
            function (erro) {
                var msg = descreverErro(erro);
                gps.checked = false;
                salvarPref('gpsPermission', false);
                mostrarStatus(msg.longo, true);
                avisar(msg.curto);
            }
        );
    });

    /* ---------- Janela "Minha localização" ---------- */

    var janelaLocal   = document.getElementById('janelaLocal');
    var locEstado     = document.getElementById('locEstado');
    var locDados      = document.getElementById('locDados');
    var locMapa       = document.getElementById('locMapa');
    var atualizarLoc  = document.getElementById('atualizarLocal');
    var locAprox      = document.getElementById('locAproximada');

    function buscarLocalizacao() {
        locEstado.textContent = 'Procurando sinal do GPS…';
        mostrarAjuda(document.getElementById('locAjuda'), null);
        locAprox.hidden = true;
        locDados.hidden = true;
        locMapa.hidden = true;
        atualizarLoc.disabled = true;

        obterPosicao(
            function (pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;

                document.getElementById('locLat').textContent = lat.toFixed(5);
                document.getElementById('locLng').textContent = lng.toFixed(5);
                document.getElementById('locPrecisao').textContent =
                    'cerca de ' + Math.round(pos.coords.accuracy) + ' m';

                var dx = 0.004, dy = 0.0025;
                locMapa.src =
                    'https://www.openstreetmap.org/export/embed.html?bbox=' +
                    [lng - dx, lat - dy, lng + dx, lat + dy].join('%2C') +
                    '&layer=mapnik&marker=' + lat + '%2C' + lng;

                locEstado.textContent = 'Encontramos sua posição.';
                locDados.hidden = false;
                locMapa.hidden = false;
                atualizarLoc.disabled = false;

                // Se o GPS funcionou, o toggle passa a refletir isso
                guardarPosicao(pos);
                gps.checked = true;
                salvarPref('gpsPermission', true);
                estadoNormal();
            },
            function (erro) {
                locEstado.textContent = descreverErro(erro).longo;
                mostrarAjuda(document.getElementById('locAjuda'), erro);
                // Se o usuário não bloqueou de propósito, oferece uma posição aproximada
                locAprox.hidden = erro && erro.code === 1;
                atualizarLoc.disabled = false;
            }
        );
    }

    // Plano B (computador sem GPS, site sem HTTPS): posição aproximada pelo IP, nível de cidade
    locAprox.addEventListener('click', function () {
        locAprox.hidden = true;
        locEstado.textContent = 'Buscando localização aproximada…';

        fetch('https://ipwho.is/')
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d || d.success === false || typeof d.latitude !== 'number') {
                    throw new Error('sem dados');
                }

                var lat = d.latitude, lng = d.longitude;
                var lugar = [d.city, d.region].filter(Boolean).join(', ');

                document.getElementById('locLat').textContent = lat.toFixed(3);
                document.getElementById('locLng').textContent = lng.toFixed(3);
                document.getElementById('locPrecisao').textContent = 'aproximada (nível de cidade)';

                var dx = 0.06, dy = 0.04;
                locMapa.src =
                    'https://www.openstreetmap.org/export/embed.html?bbox=' +
                    [lng - dx, lat - dy, lng + dx, lat + dy].join('%2C') +
                    '&layer=mapnik&marker=' + lat + '%2C' + lng;

                locEstado.textContent =
                    'Localização aproximada' + (lugar ? ': ' + lugar : '') +
                    '. Não é o GPS do aparelho.';
                locDados.hidden = false;
                locMapa.hidden = false;
            })
            .catch(function () {
                locEstado.textContent = 'Não foi possível obter a localização aproximada agora.';
                locAprox.hidden = false;
            });
    });

    document.getElementById('abrirLocal').addEventListener('click', function () {
        if (typeof janelaLocal.showModal === 'function') {
            janelaLocal.showModal();
            buscarLocalizacao();
        }
    });

    atualizarLoc.addEventListener('click', buscarLocalizacao);

    /* ---------- Testar GPS de corrida ---------- */

    var janelaCorrida = document.getElementById('janelaCorrida');
    var cStatus  = document.getElementById('corridaStatus');
    var cAjuda   = document.getElementById('corridaAjuda');
    var cTempo   = document.getElementById('corridaTempo');
    var cDist    = document.getElementById('corridaDist');
    var cRitmo   = document.getElementById('corridaRitmo');
    var cVel     = document.getElementById('corridaVel');
    var cPrec    = document.getElementById('corridaPrec');
    var cBotao   = document.getElementById('corridaBotao');

    var PRECISAO_MAX = 40;   // ignora pontos com erro maior que 40 m
    var MOVIMENTO_MIN = 3;   // ignora "tremidas" menores que 3 m
    var VELOCIDADE_MAX = 12; // 12 m/s (43 km/h): acima disso é salto do GPS

    var corrida = novaCorrida();

    function novaCorrida() {
        return {
            watchId: null, estado: 'parado', ultimo: null, distancia: 0,
            acumulado: 0, inicio: 0, timer: null, tela: null, velocidade: null
        };
    }

    function dois(n) { return n < 10 ? '0' + n : '' + n; }

    function formatarTempo(ms) {
        var t = Math.floor(ms / 1000);
        var h = Math.floor(t / 3600);
        var m = Math.floor((t % 3600) / 60);
        var sg = t % 60;
        return (h > 0 ? h + ':' + dois(m) : dois(m)) + ':' + dois(sg);
    }

    function distanciaMetros(a, b) {
        var R = 6371000, rad = Math.PI / 180;
        var dLat = (b.lat - a.lat) * rad;
        var dLng = (b.lng - a.lng) * rad;
        var h = Math.pow(Math.sin(dLat / 2), 2) +
                Math.cos(a.lat * rad) * Math.cos(b.lat * rad) * Math.pow(Math.sin(dLng / 2), 2);
        return 2 * R * Math.asin(Math.sqrt(h));
    }

    function tempoDecorrido() {
        return corrida.acumulado + (corrida.estado === 'correndo' ? Date.now() - corrida.inicio : 0);
    }

    function statusCorrida(texto, alerta) {
        cStatus.textContent = texto;
        cStatus.classList.toggle('is-warn', !!alerta);
    }

    function atualizarPainel() {
        var ms = tempoDecorrido();
        var km = corrida.distancia / 1000;

        cTempo.textContent = formatarTempo(ms);
        cDist.textContent = km.toFixed(2).replace('.', ',') + ' km';

        if (km >= 0.02) {
            var minPorKm = (ms / 60000) / km;
            var min = Math.floor(minPorKm);
            var seg = Math.round((minPorKm - min) * 60);
            if (seg === 60) { min += 1; seg = 0; }
            cRitmo.textContent = min + ':' + dois(seg) + ' /km';
        } else {
            cRitmo.textContent = '--:-- /km';
        }

        cVel.textContent = corrida.velocidade === null
            ? '-- km/h'
            : (corrida.velocidade * 3.6).toFixed(1).replace('.', ',') + ' km/h';
    }

    function pedirTela() {
        if ('wakeLock' in navigator) {
            navigator.wakeLock.request('screen').then(function (l) {
                corrida.tela = l;
            }).catch(function () {});
        }
    }

    function liberarTela() {
        if (corrida.tela) {
            try { corrida.tela.release(); } catch (e) {}
            corrida.tela = null;
        }
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible' && corrida.estado === 'correndo' && !corrida.tela) {
            pedirTela();
        }
    });

    function aoReceberPosicao(pos) {
        var c = pos.coords;
        var ponto = { lat: c.latitude, lng: c.longitude, t: pos.timestamp };

        cPrec.textContent = '±' + Math.round(c.accuracy) + ' m';
        mostrarAjuda(cAjuda, null);

        // O GPS funcionou, então o toggle passa a refletir isso
        guardarPosicao(pos);
        if (!gps.checked) {
            gps.checked = true;
            salvarPref('gpsPermission', true);
            estadoNormal();
        }

        if (c.accuracy > PRECISAO_MAX) {
            statusCorrida('Sinal fraco (±' + Math.round(c.accuracy) + ' m). Vá para um local aberto.', true);
            return;
        }

        if (corrida.estado !== 'correndo') {
            statusCorrida(
                corrida.estado === 'pausado'
                    ? 'Pausado'
                    : 'GPS pronto (±' + Math.round(c.accuracy) + ' m). Toque em Iniciar.',
                false
            );
            return;
        }

        statusCorrida('Correndo', false);

        if (!corrida.ultimo) {
            corrida.ultimo = ponto;
            return;
        }

        var d  = distanciaMetros(corrida.ultimo, ponto);
        var dt = (ponto.t - corrida.ultimo.t) / 1000;

        if (dt > 30) {
            // Ficou muito tempo sem sinal: recomeça a medir daqui, sem somar o salto
            corrida.ultimo = ponto;
        } else if (dt > 0 && d / dt > VELOCIDADE_MAX) {
            // Salto impossível do GPS: descarta o ponto
        } else if (d >= MOVIMENTO_MIN) {
            corrida.distancia += d;
            corrida.ultimo = ponto;
            corrida.velocidade = (typeof c.speed === 'number' && c.speed >= 0) ? c.speed : (dt > 0 ? d / dt : null);
        } else if (typeof c.speed === 'number' && c.speed >= 0) {
            corrida.velocidade = c.speed;
        } else if (dt > 5) {
            corrida.velocidade = 0;
        }

        atualizarPainel();
    }

    function aoFalharPosicao(erro) {
        var msg = descreverErro(erro);
        statusCorrida(msg.longo, true);
        mostrarAjuda(cAjuda, erro);
        cPrec.textContent = '--';

        if (erro.code === 1) pararSinal(); // bloqueado: só volta a tentar ao tocar em Iniciar
    }

    function iniciarSinal() {
        if (corrida.watchId !== null) return;

        if (!temGeo || !seguro) {
            statusCorrida(descreverErro().longo, true);
            return;
        }

        statusCorrida('Procurando sinal do GPS…', false);

        try {
            corrida.watchId = navigator.geolocation.watchPosition(
                aoReceberPosicao,
                aoFalharPosicao,
                { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 }
            );
        } catch (e) {
            statusCorrida(descreverErro().longo, true);
        }
    }

    function pararSinal() {
        if (corrida.watchId !== null) {
            navigator.geolocation.clearWatch(corrida.watchId);
            corrida.watchId = null;
        }
    }

    cBotao.addEventListener('click', function () {

        if (corrida.estado === 'correndo') {
            corrida.acumulado = tempoDecorrido();
            corrida.estado = 'pausado';
            clearInterval(corrida.timer);
            liberarTela();
            cBotao.textContent = 'Retomar';
            statusCorrida('Pausado', false);
            atualizarPainel();
            return;
        }

        // Iniciar ou retomar
        iniciarSinal();
        corrida.ultimo = null;          // não soma o que andou durante a pausa
        corrida.inicio = Date.now();
        corrida.estado = 'correndo';
        corrida.timer = setInterval(atualizarPainel, 1000);
        pedirTela();
        cBotao.textContent = 'Pausar';
        statusCorrida('Correndo', false);
    });

    function zerarCorrida() {
        pararSinal();
        clearInterval(corrida.timer);
        liberarTela();
        corrida = novaCorrida();
        cBotao.textContent = 'Iniciar';
        cPrec.textContent = '--';
        mostrarAjuda(cAjuda, null);
        atualizarPainel();
    }

    document.getElementById('abrirCorrida').addEventListener('click', function () {
        if (typeof janelaCorrida.showModal === 'function') {
            zerarCorrida();
            janelaCorrida.showModal();
            iniciarSinal();
        }
    });

    // Fechar a janela encerra o GPS
    janelaCorrida.addEventListener('close', zerarCorrida);

})();
</script>

</body>
</html>