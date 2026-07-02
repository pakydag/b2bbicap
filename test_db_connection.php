<?php

$host = '127.0.0.1';
$db   = 'b2bbicap';
$user = 'root';
$pass = '';
$port = "3306";
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 5, // Timeout breve per evitare attese lunghe
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "✅ Connessione al database riuscita!\n";
} catch (\PDOException $e) {
     echo "❌ ERRORE DI CONNESSIONE: " . $e->getMessage() . "\n";
     echo "Codice Errore: " . $e->getCode() . "\n";
}
