<?php

$host = "sql306.infinityfree.com";
$usuario = "if0_42995131";
$senha = "Ritmo2026Site";
$banco = "if0_42995131_ritmounico";

$conexao = new mysqli($host, $usuario, $senha, $banco, 3306);

if ($conexao->connect_error) {
    die("Erro na conexão com o banco: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");
