<?php

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'ritmo_unico';

header('Content-Type: text/plain; charset=utf-8');

$connection = @new mysqli($host, $user, $password);
if ($connection->connect_error) {
    fwrite(STDERR, "Erro de conexão com o MySQL: " . $connection->connect_error . PHP_EOL);
    exit(1);
}

$connection->set_charset('utf8mb4');

$sqlCreateDatabase = "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
if (!$connection->query($sqlCreateDatabase)) {
    fwrite(STDERR, "Erro ao criar o banco: " . $connection->error . PHP_EOL);
    $connection->close();
    exit(1);
}

$connection->select_db($database);

$schema = <<<'SQL'
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_nascimento DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS corridas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    data_corrida DATETIME NOT NULL,
    distancia DECIMAL(10,3) NOT NULL DEFAULT 0,
    tempo VARCHAR(20) NOT NULL,
    ritmo VARCHAR(20) NULL,
    pace VARCHAR(20) NULL,
    calorias DECIMAL(10,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_corridas_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    corrida_id INT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    altitude DECIMAL(10,3) NULL,
    velocidade DECIMAL(10,3) NULL,
    distancia DECIMAL(10,3) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_gps_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_gps_corridas FOREIGN KEY (corrida_id) REFERENCES corridas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX IF NOT EXISTS idx_corridas_usuario ON corridas(usuario_id);
CREATE INDEX IF NOT EXISTS idx_corridas_data ON corridas(data_corrida);
CREATE INDEX IF NOT EXISTS idx_gps_usuario ON gps(usuario_id);
CREATE INDEX IF NOT EXISTS idx_gps_corrida ON gps(corrida_id);
SQL;

if (!$connection->multi_query($schema)) {
    fwrite(STDERR, "Erro ao criar tabelas: " . $connection->error . PHP_EOL);
    $connection->close();
    exit(1);
}

while ($connection->next_result()) {
    if ($res = $connection->store_result()) {
        $res->free();
    }
}

$connection->close();

echo "Banco e tabelas prontos: {$database}" . PHP_EOL;

echo "Use: php -S localhost:8000" . PHP_EOL;
