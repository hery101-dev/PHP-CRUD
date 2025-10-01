<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$keywords = isset($_GET['s']) ? $_GET['s'] : "";

if (!empty($keywords)) {
    $stmt = $user->search($keywords);
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
        echo json_encode($users_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Aucun utilisateur trouvé."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Mots-clés de recherche requis."));
}
