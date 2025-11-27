<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require "Database.php";
require "CrudAPI.php";

$pdo = (new Database())->pdo();

$table = $_GET["table"] ?? null;
$id    = $_GET["id"] ?? null;

if (!$table) {
    http_response_code(400);
    echo json_encode(["error" => "Missing table name"]);
    exit;
}

$api = new CrudAPI($pdo, $table);
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    case "GET":
        echo json_encode($id ? $api->read($id) : $api->readAll());
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode($api->create($data));
        break;

    case "PUT":
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            break;
        }
        $data = json_decode(file_get_contents("php://input"), true);
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
