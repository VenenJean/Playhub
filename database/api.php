<?php
require_once 'db.php';
header("Content-Type: application/json; charset=UTF-8");

$table = $_GET['table'] ?? null;
$id = $_GET['id'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

if (!$table) {
    http_response_code(400);
    echo json_encode(["error" => "Missing table parameter"]);
    exit;
}

class API
{
    private $conn;
    private array $allowedTables = [
        "public_users",
        "public_games",
        "public_reviews",
        "public_users_games",
        "public_wishlists",
        "public_studios",
        "public_publishers_games",
        "public_developers_games",
        "game_categories",
        "game_games_categories",
        "game_platforms",
        "game_games_platforms",
        "hrbac_roles",
        "hrbac_permissions",
        "hrbac_users_roles",
        "hrbac_roles_inherits",
        "hrbac_roles_permissions"
    ];

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    private function normalizeTable(string $table): array
    {
        $schema = null;
        $name = $table;
        if (strpos($table, '.') !== false) [$schema, $name] = explode('.', $table, 2);
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name) || ($schema && !preg_match('/^[A-Za-z0-9_]+$/', $schema))) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid table name"]);
            exit;
        }
        if (!in_array($name, $this->allowedTables, true)) {
            http_response_code(400);
            echo json_encode(["error" => "Table not allowed"]);
            exit;
        }
        $qualified = $schema ? "[$schema].[$name]" : "[$name]";
        return ['name' => $name, 'schema' => $schema, 'qualified' => $qualified];
    }

    private function getPrimaryKey(string $tableName): string
    {
        return "id";
    }

    public function getAll(string $table): array
    {
        $info = $this->normalizeTable($table);
        $stmt = $this->conn->prepare("SELECT * FROM " . $info['qualified']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(string $table, $id): array|false
    {
        $info = $this->normalizeTable($table);
        $stmt = $this->conn->prepare("SELECT * FROM " . $info['qualified'] . " WHERE [id] = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function validateColumnName(string $col): bool
    {
        return preg_match('/^[A-Za-z0-9_]+$/', $col);
    }

    public function create(string $table, array $data): mixed
    {
        $info = $this->normalizeTable($table);

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "No data provided"]);
            exit;
        }

        $cols = array_keys($data);
        foreach ($cols as $c) {
            if (!$this->validateColumnName($c)) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid column name: $c"]);
                exit;
            }
        }

        $columnsEscaped = '[' . implode('], [', $cols) . ']';
        $placeholders = ':' . implode(', :', $cols);

        $sql = "INSERT INTO " . $info['qualified'] . " ($columnsEscaped) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        try {
            $idStmt = $this->conn->query("SELECT SCOPE_IDENTITY() AS id");
            $idRow = $idStmt->fetch(PDO::FETCH_ASSOC);
            return $idRow['id'] ?? true;
        } catch (Exception $e) {
            return true;
        }
    }


    public function update(string $table, $id, array $data): bool
    {
        $info = $this->normalizeTable($table);
        if (empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "No data provided"]);
            exit;
        }
        $fields = [];
        foreach ($data as $k => $v) {
            if (!$this->validateColumnName($k)) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid column: $k"]);
                exit;
            }
            $fields[] = "[$k] = :$k";
        }
        $stmt = $this->conn->prepare("UPDATE " . $info['qualified'] . " SET " . implode(", ", $fields) . " WHERE [id]=:id");
        foreach ($data as $k => $v) $stmt->bindValue(":$k", $v);
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }

    public function delete(string $table, $id): bool
    {
        $info = $this->normalizeTable($table);
        $stmt = $this->conn->prepare("DELETE FROM " . $info['qualified'] . " WHERE [id]=:id");
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }
}

$api = new API();
switch ($method) {
    case "GET":
        $res = $id ? $api->getById($table, $id) : $api->getAll($table);
        echo json_encode($res);
        break;
    case "POST":
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input) || empty($input)) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid/empty JSON"]);
            exit;
        }
        $newId = $api->create($table, $input);
        echo json_encode(["success" => true, "id" => $newId]);
        break;
    case "PUT":
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            exit;
        }
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input) || empty($input)) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid/empty JSON"]);
            exit;
        }
        $res = $api->update($table, $id, $input);
        echo json_encode(["success" => $res]);
        break;
    case "DELETE":
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            exit;
        }
        $res = $api->delete($table, $id);
        echo json_encode(["success" => $res]);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
