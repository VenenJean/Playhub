<?php

class CrudAPI
{
    private $pdo;
    private $table;

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
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

        if ($stmt->columnCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ["id" => $result["id"] ?? null];
        }

        return ["id" => $this->pdo->lastInsertId()];
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
        $columns = $this->getColumns();
        $data = array_intersect_key($data, array_flip($columns));

        $set = implode(", ", array_map(fn($k) => "$k = :$k", array_keys($data)));

        $sql = "UPDATE {$this->table} SET $set WHERE id = :id";

        $data["id"] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /** DELETE */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
