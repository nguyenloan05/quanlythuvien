<?php
require_once __DIR__ . '/../data/connect.php';
require_once __DIR__ . '/../data/repositories/ViPhamRepo.php';
$database = new Database();
$db = $database->getConnection();
$repo = new ViPhamRepo($db);

echo "Count Total: " . $repo->countTotal('') . "\n";
$data = $repo->searchVP('', 0, 10);
echo "Search Count: " . count($data) . "\n";
if (count($data) > 0) {
    echo "First row PM: " . $data[0]['ma_pm'] . "\n";
}
?>
