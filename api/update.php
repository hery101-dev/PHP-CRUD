<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->nom) && !empty($data->prenom) && !empty($data->email)) {
    $user->id = $data->id;
    $user->nom = $data->nom;
    $user->prenom = $data->prenom;
    $user->email = $data->email;
    $user->telephone = $data->telephone ?? '';

    // Vérifier si l'email existe déjà
    if($user->emailExists()) {
        http_response_code(400);
        echo json_encode(array("message" => "Cette adresse email est déjà utilisée."));
        exit;
    }

    if($user->update()) {
        http_response_code(200);
        echo json_encode(array("message" => "Utilisateur mis à jour avec succès."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Impossible de mettre à jour l'utilisateur."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Impossible de mettre à jour l'utilisateur. Données incomplètes."));
}
