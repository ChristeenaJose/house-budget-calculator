<?php

require __DIR__ . '/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^/api#', '', $path);
$path = rtrim($path, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = house_budjet_db();

    if ($method === 'GET' && $path === '/health') {
        json_response(['status' => 'online', 'app' => 'House-Budjet']);
        return;
    }

    if ($method === 'GET' && $path === '/categories') {
        $type = $_GET['type'] ?? null;
        if ($type) {
            $stmt = $pdo->prepare('SELECT * FROM categories WHERE type = :type ORDER BY name');
            $stmt->execute(['type' => $type]);
            json_response($stmt->fetchAll());
            return;
        }
        json_response($pdo->query('SELECT * FROM categories ORDER BY type, name')->fetchAll());
        return;
    }

    if ($method === 'GET' && $path === '/suppliers') {
        json_response($pdo->query('SELECT * FROM suppliers ORDER BY name')->fetchAll());
        return;
    }

    if ($method === 'GET' && $path === '/expenses') {
        $sql = 'SELECT expenses.*, categories.name AS category_name, suppliers.name AS supplier_name
                FROM expenses
                JOIN categories ON categories.id = expenses.category_id
                LEFT JOIN suppliers ON suppliers.id = expenses.supplier_id
                ORDER BY expenses.date DESC, expenses.id DESC';
        json_response($pdo->query($sql)->fetchAll());
        return;
    }

    if ($method === 'POST' && $path === '/expenses') {
        $input = json_input();
        $date = $input['date'] ?? date('Y-m-d');
        $categoryId = (int) ($input['category_id'] ?? 0);
        $amount = (float) ($input['amount'] ?? 0);
        $description = $input['description'] ?? null;
        $proofType = $input['proof_type'] ?? 'none';
        $supplierId = isset($input['supplier_id']) && $input['supplier_id'] !== '' ? (int) $input['supplier_id'] : null;
        $vatAmount = (float) ($input['vat_amount'] ?? 0);

        if ($categoryId <= 0 || $amount <= 0) {
            json_response(['message' => 'Category and amount are required'], 422);
            return;
        }

        $allowedProof = ['none', 'receipt', 'invoice', 'photo'];
        if (!in_array($proofType, $allowedProof, true)) {
            $proofType = 'none';
        }

        $now = now_stamp();
        $stmt = $pdo->prepare('INSERT INTO expenses (date, category_id, amount, description, proof_type, supplier_id, vat_amount, updated_at, created_at)
            VALUES (:date, :category_id, :amount, :description, :proof_type, :supplier_id, :vat_amount, :updated_at, :created_at)');
        $stmt->execute([
            'date' => $date,
            'category_id' => $categoryId,
            'amount' => $amount,
            'description' => $description,
            'proof_type' => $proofType,
            'supplier_id' => $supplierId,
            'vat_amount' => $vatAmount,
            'updated_at' => $now,
            'created_at' => $now,
        ]);

        $id = (int) $pdo->lastInsertId();
        $row = $pdo->query('SELECT expenses.*, categories.name AS category_name, suppliers.name AS supplier_name
            FROM expenses
            JOIN categories ON categories.id = expenses.category_id
            LEFT JOIN suppliers ON suppliers.id = expenses.supplier_id
            WHERE expenses.id = ' . $id)->fetch();
        json_response($row, 201);
        return;
    }

    if ($method === 'GET' && $path === '/incomes') {
        $sql = 'SELECT incomes.*, categories.name AS category_name
                FROM incomes
                JOIN categories ON categories.id = incomes.category_id
                ORDER BY incomes.date DESC, incomes.id DESC';
        json_response($pdo->query($sql)->fetchAll());
        return;
    }

    if ($method === 'POST' && $path === '/incomes') {
        $input = json_input();
        $date = $input['date'] ?? date('Y-m-d');
        $categoryId = (int) ($input['category_id'] ?? 0);
        $amount = (float) ($input['amount'] ?? 0);
        $description = $input['description'] ?? null;
        if ($categoryId <= 0 || $amount <= 0) {
            json_response(['message' => 'Category and amount are required'], 422);
            return;
        }
        $now = now_stamp();
        $stmt = $pdo->prepare('INSERT INTO incomes (date, category_id, amount, description, updated_at, created_at)
            VALUES (:date, :category_id, :amount, :description, :updated_at, :created_at)');
        $stmt->execute([
            'date' => $date,
            'category_id' => $categoryId,
            'amount' => $amount,
            'description' => $description,
            'updated_at' => $now,
            'created_at' => $now,
        ]);
        json_response(['id' => (int) $pdo->lastInsertId()], 201);
        return;
    }

    if ($method === 'GET' && $path === '/dashboard') {
        $income = (float) $pdo->query('SELECT COALESCE(SUM(amount), 0) FROM incomes')->fetchColumn();
        $expense = (float) $pdo->query('SELECT COALESCE(SUM(amount), 0) FROM expenses')->fetchColumn();
        json_response([
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
        ]);
        return;
    }

    if ($method === 'GET' && $path === '/report') {
        $byCategory = $pdo->query('SELECT categories.name, COALESCE(SUM(expenses.amount), 0) AS total
            FROM categories
            LEFT JOIN expenses ON expenses.category_id = categories.id
            WHERE categories.type = "expense"
            GROUP BY categories.id
            ORDER BY categories.name')->fetchAll();
        json_response(['by_category' => $byCategory]);
        return;
    }

    json_response(['message' => 'Not found'], 404);
} catch (Throwable $e) {
    json_response(['message' => $e->getMessage()], 500);
}
