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
        "BankAccounts",
        "Users",
        "Games",
        "Categories",
        "GameCategory",
        "UserBibliothek",
        "Comments",
        "UserCommentReview",
        "Roles",
        "Permissions",
        "UserRole",
        "RolePermission",
        "TransactionHistory"
    ];

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    private function normalizeTable(string $table): array
    {
        $schema = null;
        $name = $table;
        if (strpos($table, '.') !== false) {
            [$schema, $name] = explode('.', $table, 2);
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $name) || ($schema !== null && !preg_match('/^[A-Za-z0-9_]+$/', $schema))) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid table name or schema"]);
            exit;
        }

        if (!in_array($name, $this->allowedTables, true)) {
            http_response_code(400);
            echo json_encode(["error" => "Table not allowed"]);
            exit;
        }

        $qualified = $schema !== null ? "[$schema].[$name]" : "[$name]";
        return ['name' => $name, 'schema' => $schema, 'qualified' => $qualified];
    }

    private function getPrimaryKey(string $tableName): string
    {
        $primaryKeys = [
            "BankAccounts" => "IBAN",
            "Users" => "ID",
            "Games" => "ID",
            "Categories" => "ID",
            "GameCategory" => "ID",
            "UserBibliothek" => "ID",
            "Comments" => "ID",
            "UserCommentReview" => "ID",
            "Roles" => "ID",
            "Permissions" => "ID",
            "UserRole" => "ID",
            "RolePermission" => "ID",
            "TransactionHistory" => "ID"
        ];
        return $primaryKeys[$tableName] ?? "ID";
    }

    public function getAll(string $table): array
    {
        $info = $this->normalizeTable($table);
        $sql = "SELECT * FROM " . $info['qualified'];
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(string $table, $id): array|false
    {
        $info = $this->normalizeTable($table);
        $pk = $this->getPrimaryKey($info['name']);
        $sql = "SELECT * FROM " . $info['qualified'] . " WHERE [$pk] = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function validateColumnName(string $col): bool
    {
        return (bool)preg_match('/^[A-Za-z0-9_]+$/', $col);
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
            return $this->conn->lastInsertId();
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

        $pk = $this->getPrimaryKey($info['name']);

        $fields = [];
        foreach ($data as $key => $value) {
            if (!$this->validateColumnName($key)) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid column name: $key"]);
                exit;
            }
            $fields[] = "[$key] = :$key";
        }
        $setPart = implode(", ", $fields);

        $sql = "UPDATE " . $info['qualified'] . " SET $setPart WHERE [$pk] = :id";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(":id", $id);

        return $stmt->execute();
    }

    public function delete(string $table, $id): bool
    {
        $info = $this->normalizeTable($table);
        $pk = $this->getPrimaryKey($info['name']);
        $sql = "DELETE FROM " . $info['qualified'] . " WHERE [$pk] = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }
}

$api = new API();

switch ($method) {
    case "GET":
        $result = $id ? $api->getById($table, $id) : $api->getAll($table);
        echo json_encode($result);
        break;

    case "POST":
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input) || empty($input)) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid or empty JSON body"]);
            exit;
        }
        $newId = $api->create($table, $input);
        echo json_encode(["success" => true, "id" => $newId]);
        break;

    case "PUT":
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id parameter"]);
            exit;
        }
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input) || empty($input)) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid or empty JSON body"]);
            exit;
        }
        $result = $api->update($table, $id, $input);
        echo json_encode(["success" => $result]);
        break;

    case "DELETE":
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id parameter"]);
            exit;
        }
        $result = $api->delete($table, $id);
        echo json_encode(["success" => $result]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
