<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS, POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");


// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Buffer de sortie
ob_start();

try {
    // Vérifier la méthode (accepter DELETE et POST pour le debug)
    if ($_SERVER['REQUEST_METHOD'] != 'DELETE' && $_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception("Méthode non autorisée: " . $_SERVER['REQUEST_METHOD']);
    }

    // Lire les données brutes
    $rawInput = file_get_contents("php://input");
    error_log("Raw input: " . $rawInput);

    if (empty($rawInput)) {
        throw new Exception("Aucune donnée reçue dans le corps de la requête");
    }

    // Décoder le JSON
    $data = json_decode($rawInput);
    $jsonError = json_last_error();

    if ($jsonError !== JSON_ERROR_NONE) {
        throw new Exception("Erreur JSON: " . json_last_error_msg() . " - Data: " . $rawInput);
    }

    error_log("Parsed data: " . print_r($data, true));

    // Vérifier que l'ID est présent
    if (!isset($data->id)) {
        throw new Exception("Champ 'id' manquant dans les données JSON");
    }

    if (empty($data->id)) {
        throw new Exception("ID utilisateur vide");
    }

    // Validation de l'ID
    $userId = $data->id;
    if (!is_numeric($userId)) {
        throw new Exception("ID utilisateur invalide (non numérique): " . $userId);
    }

    $userId = intval($userId);
    if ($userId <= 0) {
        throw new Exception("ID utilisateur invalide (doit être positif): " . $userId);
    }

    error_log("User ID to delete: " . $userId);

    // Inclure les fichiers (avec vérification)
    $configPath = '../config/database.php';
    $modelPath = '../models/User.php';

    if (!file_exists($configPath)) {
        throw new Exception("Fichier database.php introuvable: " . $configPath);
    }

    if (!file_exists($modelPath)) {
        throw new Exception("Fichier User.php introuvable: " . $modelPath);
    }

    include_once $configPath;
    include_once $modelPath;

    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Impossible de se connecter à la base de données");
    }

    // Créer l'objet User
    $user = new User($db);
    $user->id = $userId;

    // Vérifier si l'utilisateur existe
    if (!$user->readOne()) {
        http_response_code(404);
        ob_clean();
        echo json_encode(array(
            "success" => false,
            "message" => "Utilisateur introuvable (ID: {$userId})"
        ), JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Sauvegarder les infos pour le message
    $userName = $user->prenom . " " . $user->nom;
    error_log("Deleting user: " . $userName);

    // Effectuer la suppression
    if ($user->delete()) {
        http_response_code(200);
        ob_clean();
        echo json_encode(array(
            "success" => true,
            "message" => "Utilisateur '{$userName}' supprimé avec succès",
            "deleted_id" => $userId
        ), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        ob_clean();
        echo json_encode(array(
            "success" => false,
            "message" => "Erreur lors de la suppression de l'utilisateur"
        ), JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    error_log("DELETE API Error: " . $errorMsg);

    http_response_code(400);
    ob_clean();
    echo json_encode(array(
        "success" => false,
        "message" => $errorMsg,
        "debug_info" => array(
            "method" => $_SERVER['REQUEST_METHOD'],
            "content_type" => $_SERVER['CONTENT_TYPE'] ?? 'undefined',
            "raw_input_length" => strlen(file_get_contents("php://input")),
            "php_version" => PHP_VERSION
        )
    ), JSON_UNESCAPED_UNICODE);
} finally {
    ob_end_flush();
}

error_log("=== DELETE API End ===");
