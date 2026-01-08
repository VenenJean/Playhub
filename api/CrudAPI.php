<?php

class CrudAPI
{
    private $pdo;
    private $table;
    private $context;

    public function __construct(PDO $pdo, string $table, array $context = [])
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->context = $context;
    }

    /**
     * Write a log row (best-effort; failures must never break CRUD).
     */
    private function writeLog(string $action, $recordId = null, $oldData = null, $newData = null): void
    {
        // Avoid recursion if someone ever edits the log table itself
        if (strtolower($this->table) === 'admin_logs') return;

        try {
            $sql = "INSERT INTO admin_logs (action, table_name, user_agent, old_data, new_data)
                    VALUES (:action, :table_name, :user_agent, :old_data, :new_data)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'action'     => $action,
                'table_name' => $this->table,
                'user_agent' => $this->context['user_agent'] ?? null,
                'old_data'   => $oldData !== null ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
                'new_data'   => $newData !== null ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            ]);
        } catch (Throwable $e) {
            // Swallow errors (e.g., log table missing) so the API keeps working.
        }
    }

    /** Get table columns dynamically (MSSQL VERSION) */
    private function getColumns()
    {
        $sql = "SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = :table";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["table" => $this->table]);
        return array_column($stmt->fetchAll(), "COLUMN_NAME");
    }

    /** CREATE */
    public function create(array $data)
    {
        $columns = $this->getColumns();

        // Remove non-existing columns
        $data = array_intersect_key($data, array_flip($columns));

        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $placeholders = implode(", ", array_map(fn($k) => ":$k", $keys));

        // INSERT with MSSQL syntax + return last inserted id
        $sql = "INSERT INTO {$this->table} ($fields) 
                VALUES ($placeholders); 
                SELECT SCOPE_IDENTITY() AS id;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        $id = null;
        if ($stmt->columnCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $result["id"] ?? null;
        } else {
            $id = $this->pdo->lastInsertId();
        }

        $this->writeLog('CREATE', $id, null, $data);
        return ["id" => $id];
    }

    /** READ ALL */
    public function readAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /** READ single by ID */
    public function read($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** UPDATE */
    public function update($id, array $data)
    {
        $before = $this->read($id);

        $columns = $this->getColumns();
        $data = array_intersect_key($data, array_flip($columns));

        $set = implode(", ", array_map(fn($k) => "$k = :$k", array_keys($data)));

        $sql = "UPDATE {$this->table} SET $set WHERE id = :id";

        $data["id"] = $id;

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($data);

        if ($ok) {
            $after = $this->read($id);
            $this->writeLog('UPDATE', $id, $before, $after);
        }

        return $ok;
    }

    /** DELETE */
    public function delete($id)
    {
        $before = $this->read($id);
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $ok = $stmt->execute([$id]);

        if ($ok) {
            $this->writeLog('DELETE', $id, $before, null);
        }

        return $ok;
    }
}
