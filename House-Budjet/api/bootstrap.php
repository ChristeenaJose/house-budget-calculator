<?php

function house_budjet_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dir = __DIR__ . '/database';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $path = $dir . '/database.sqlite';
    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec(file_get_contents($dir . '/schema.sql'));
    ensure_proof_type_column($pdo);
    seed_defaults($pdo);

    return $pdo;
}

function ensure_proof_type_column(PDO $pdo): void
{
    $columns = $pdo->query('PRAGMA table_info(expenses)')->fetchAll();
    foreach ($columns as $column) {
        if ($column['name'] === 'proof_type') {
            return;
        }
    }
    $pdo->exec("ALTER TABLE expenses ADD COLUMN proof_type TEXT NOT NULL DEFAULT 'none'");
}

function seed_defaults(PDO $pdo): void
{
    $now = gmdate('Y-m-d H:i:s');

    $expenseCategories = ['Faustina', 'Fiona'];
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE type = :type AND name = :name');
    $insert = $pdo->prepare('INSERT INTO categories (name, type, created_at, updated_at) VALUES (:name, :type, :created_at, :updated_at)');
    foreach ($expenseCategories as $name) {
        $stmt->execute(['type' => 'expense', 'name' => $name]);
        if ((int) $stmt->fetchColumn() === 0) {
            $insert->execute([
                'name' => $name,
                'type' => 'expense',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    $incomeCategories = ['Salary', 'Other'];
    foreach ($incomeCategories as $name) {
        $stmt->execute(['type' => 'income', 'name' => $name]);
        if ((int) $stmt->fetchColumn() === 0) {
            $insert->execute([
                'name' => $name,
                'type' => 'income',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    $supplierCount = (int) $pdo->query('SELECT COUNT(*) FROM suppliers')->fetchColumn();
    if ($supplierCount === 0) {
        $supplierInsert = $pdo->prepare('INSERT INTO suppliers (name, created_at, updated_at) VALUES (:name, :created_at, :updated_at)');
        foreach (['General', 'Market', 'Utilities', 'Transport', 'Household'] as $name) {
            $supplierInsert->execute([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return $_POST;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
}

function now_stamp(): string
{
    return gmdate('Y-m-d H:i:s');
}
