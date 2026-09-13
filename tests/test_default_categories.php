<?php
ob_start();
include __DIR__ . '/../functions.php';
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

$uniqueMonth = 'TestCat_' . time();
$result = $classVar->insertMonth($uniqueMonth, '2026', 'Category test');
assert_true($result === true, 'insertMonth succeeds');

$months = $classVar->getMonth();
$monthId = 0;
foreach ($months as $month) {
	if ($month['name'] === $uniqueMonth) {
		$monthId = $month['id'];
		break;
	}
}
assert_true($monthId > 0, 'created month is retrievable');

$categories = $classVar->getCategories($monthId);
$names = array();
if ($categories) {
	foreach ($categories as $category) {
		$names[] = $category['name'];
	}
}
assert_true(in_array('Faustina', $names, true), 'Faustina category exists');
assert_true(in_array('Fiona', $names, true), 'Fiona category exists');

$classVar->ensureDefaultCategories($monthId);
$categoriesAfter = $classVar->getCategories($monthId);
assert_true(count($categoriesAfter) === count($categories), 'default categories are not duplicated');

$existingMonth = 'Existing_' . time();
$sql = "INSERT INTO month (name, year, description) VALUES ('" . $existingMonth . "', '2026', 'no cats yet')";
$classVar->conn->query($sql);
$oldMonthId = $classVar->conn->insert_id;
assert_true($classVar->getCategories($oldMonthId) === false, 'existing month starts without categories');
$classVar->ensureDefaultCategories($oldMonthId);
$backfilled = $classVar->getCategories($oldMonthId);
$backfillNames = array();
if ($backfilled) {
	foreach ($backfilled as $category) {
		$backfillNames[] = $category['name'];
	}
}
assert_true(in_array('Faustina', $backfillNames, true) && in_array('Fiona', $backfillNames, true), 'existing months receive Faustina and Fiona');

$faustinaId = 0;
foreach ($backfilled as $category) {
	if ($category['name'] === 'Faustina') {
		$faustinaId = $category['id'];
		break;
	}
}
$expense = array(
	'monthId' => $oldMonthId,
	'catid' => $faustinaId,
	'name' => 'Groceries',
	'price' => 25.50,
	'date' => '2026-09-13',
	'catOrNot' => 'N',
	'desc' => 'Assigned to Faustina'
);
assert_true($classVar->insertProduct($expense) === true, 'expense can be assigned to Faustina');
$sum = $classVar->getProductSum($oldMonthId, $faustinaId);
assert_true(floatval($sum) == 25.50, 'Faustina category total includes the expense');

if ($failures > 0) {
	echo "\n$failures test(s) failed\n";
	exit(1);
}
echo "\nAll tests passed\n";
exit(0);
