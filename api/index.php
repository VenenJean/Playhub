<?php
// Require necessary files
require "Database.php";
require "CrudAPI.php";

header('Content-Type: application/json');  // Ensure proper JSON response

$pdo = (new Database())->pdo();

// Reading query parameters
$table = $_GET["table"] ?? null;
$id    = $_GET["id"] ?? null;

// Validate table parameter
if (!$table) {
    http_response_code(400);
    echo json_encode(["error" => "Missing table name"]);
    exit;
}

$context = [
    // If you later add authentication/session, you can set this to a username/user_id.
    'actor'      => $_SERVER['PHP_AUTH_USER'] ?? ($_SERVER['REMOTE_USER'] ?? null),
    'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
];

$api = new CrudAPI($pdo, $table, $context);
$method = $_SERVER["REQUEST_METHOD"]; // Get request method e.g. GET POST PUT DELETE

// Handle requests based on HTTP method
try {
    switch ($method) {

        case "GET":
            echo json_encode($id ? $api->read($id) : $api->readAll());
            break;

        case "POST":
            $data = json_decode(file_get_contents("php://input"), true); // As associative array
            echo json_encode($api->create($data));
            break;

        case "PUT":
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Missing id"]);
                break;
            }
            $data = json_decode(file_get_contents("php://input"), true);  // As associative array
            echo json_encode($api->update($id, $data));
            break;

        case "DELETE":
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "Missing id"]);
                break;
            }
            echo json_encode($api->delete($id));
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => $e->getMessage()]);
}
