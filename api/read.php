<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

ob_start();

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$stmt = $user->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $users_arr = array();
    $users_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $user_item = array(
            "id" => $id,
            "nom" => $nom,
            "prenom" => $prenom,
            "email" => $email,
            "telephone" => $telephone,
            "date_creation" => $date_creation
        );
        array_push($users_arr["records"], $user_item);
    }

    http_response_code(200);

    // Nettoyer le buffer et forcer JSON
    ob_clean();
    echo json_encode($users_arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    http_response_code(404);
    // Réponse vide mais valide
    ob_clean();
    echo json_encode(array(
        "records" => array(),
        "message" => "Aucun utilisateur trouvé"
    ), JSON_UNESCAPED_UNICODE);
}
