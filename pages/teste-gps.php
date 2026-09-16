
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste GPS | Ritmo Único</title>

    <style>
        body {
            background: #080c14;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px 20px;
        }

        .box {
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 20px;
            background: #111827;
        }

        button {
            padding: 15px 25px;
            border: none;
            border-radius: 10px;
            background: #41d8ff;
            color: #080c14;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
        }

        #resultado {
            margin-top: 25px;
            line-height: 1.8;
        }
    </style>
</head>

<body>

<div class="box">

    <h1>📍 Teste GPS</h1>

    <p>Ritmo Único</p>

    <button onclick="testarGPS()">
        Testar GPS
    </button>

    <div id="resultado">
        GPS aguardando...
    </div>

</div>

<script>

function testarGPS() {

    const resultado = document.getElementById("resultado");

    if (!navigator.geolocation) {
        resultado.innerHTML =
            "❌ Este navegador não suporta GPS.";
        return;
    }

    resultado.innerHTML =
        "📍 Solicitando localização...";

    navigator.geolocation.getCurrentPosition(

        function(posicao) {

            const latitude = posicao.coords.latitude;
            const longitude = posicao.coords.longitude;
            const precisao = posicao.coords.accuracy;

            resultado.innerHTML = `
                ✅ GPS funcionando!<br><br>

                Latitude: ${latitude}<br>
                Longitude: ${longitude}<br>
                Precisão: ${precisao.toFixed(2)} metros
            `;
        },

        function(erro) {

            resultado.innerHTML =
                "❌ Erro ao obter GPS: " + erro.message;
        },

        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

</script>

</body>
</html>
