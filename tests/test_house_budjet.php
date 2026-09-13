<?php
ob_start();
require __DIR__ . '/../House-Budjet/api/bootstrap.php';
ob_end_clean();

$failures = 0;
function assert_true($condition, $message) {
    global $failures;
    if (!$condition) {
        $failures++;
        echo "FAIL: $message\n";
    } else {
        echo "PASS: $message\n";
    }
}

$pdo = house_budjet_db();
$categories = $pdo->query("SELECT name FROM categories WHERE type = 'expense' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
assert_true(in_array('Faustina', $categories, true), 'Faustina expense category exists');
assert_true(in_array('Fiona', $categories, true), 'Fiona expense category exists');

$columns = $pdo->query('PRAGMA table_info(expenses)')->fetchAll();
$names = array_column($columns, 'name');
assert_true(in_array('proof_type', $names, true), 'expenses.proof_type column exists');

$faustinaId = (int) $pdo->query("SELECT id FROM categories WHERE name = 'Faustina'")->fetchColumn();
$supplierId = (int) $pdo->query('SELECT id FROM suppliers LIMIT 1')->fetchColumn();
$stmt = $pdo->prepare('INSERT INTO expenses (date, category_id, amount, description, proof_type, supplier_id, vat_amount, updated_at, created_at)
    VALUES (:date, :category_id, :amount, :description, :proof_type, :supplier_id, :vat_amount, :updated_at, :created_at)');
$now = gmdate('Y-m-d H:i:s');
$stmt->execute([
    'date' => '2026-09-13 00:00:00',
    'category_id' => $faustinaId,
    'amount' => 44,
    'description' => null,
    'proof_type' => 'none',
    'supplier_id' => $supplierId,
    'vat_amount' => 0,
    'updated_at' => $now,
    'created_at' => $now,
]);
$id = (int) $pdo->lastInsertId();
$row = $pdo->query('SELECT * FROM expenses WHERE id = ' . $id)->fetch();
assert_true($row && $row['proof_type'] === 'none' && (float) $row['amount'] === 44.0, 'expense insert with proof_type succeeds');

if ($failures > 0) {
    echo "\n$failures test(s) failed\n";
    exit(1);
}
echo "\nAll House-Budjet tests passed\n";
exit(0);
