<?php
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db_name = "ibayandatabase";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $backup_dir = __DIR__ . "/backups";
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }

    $backup_file = $backup_dir . "/" . $db_name . "_backup_" . date("Y-m-d_H-i-s") . ".sql";

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $sqlScript = "";
    foreach ($tables as $table) {
        $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $sqlScript .= "\n\n" . $createStmt['Create Table'] . ";\n\n";
        $rows = $pdo->query("SELECT * FROM `$table`");
        $numColumns = $rows->columnCount();

        foreach ($rows as $row) {
            $sqlScript .= "INSERT INTO `$table` VALUES(";
            for ($j = 0; $j < $numColumns; $j++) {
                $value = $row[$j];
                $sqlScript .= isset($value) ? $pdo->quote($value) : "NULL";
                if ($j < ($numColumns - 1)) $sqlScript .= ",";
            }
            $sqlScript .= ");\n";
        }
        $sqlScript .= "\n";
    }

    file_put_contents($backup_file, $sqlScript);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file));
    readfile($backup_file);
    exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
