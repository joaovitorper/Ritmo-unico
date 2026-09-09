<?php
// Inicia a sessão se necessário
session_start();

// Configurações de conexão com o banco de dados (ajuste se necessário)
$host = 'localhost';
$dbname = 'ritmo_unico'; // Altere para o nome do seu banco de dados
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtém os valores enviados (funciona tanto para formulário padrão quanto para JSON/AJAX)
    $input = json_decode(file_get_contents('php://input'), true);
    
    $tempo = $_POST['tempo'] ?? $input['tempo'] ?? null;
    $distancia = $_POST['distancia'] ?? $input['distancia'] ?? null;
    $pace = $_POST['pace'] ?? $input['pace'] ?? null;
    $calorias = $_POST['calorias'] ?? $input['calorias'] ?? null;
    $data_corrida = date('Y-m-d H:i:s');

    // Validação básica
    if (!$tempo || !$distancia) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos da corrida.']);
        exit;
    }

    try {
        // Query de inserção na tabela de corridas
        $sql = "INSERT INTO corridas (tempo, distancia, pace, calorias, data_corrida) 
                VALUES (:tempo, :distancia, :pace, :calorias, :data_corrida)";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tempo', $tempo);
        $stmt->bindParam(':distancia', $distancia);
        $stmt->bindParam(':pace', $pace);
        $stmt->bindParam(':calorias', $calorias);
        $stmt->bindParam(':data_corrida', $data_corrida);

        if ($stmt->execute()) {
            // Se for requisição AJAX/JSON
            if ($input) {
                echo json_encode(['success' => true, 'message' => 'Corrida salva com sucesso!']);
            } else {
                // Se for formulário padrão, redireciona para o histórico
                header("Location: historico.php");
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar a corrida.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>