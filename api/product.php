<?php
/**
 * Products api page. Returns product information in JSON format.
 * 
 * Products API uses GET request and returns information requested.
 * Accepts parameters:
 * - Product ID = x
 * - Details (Get details) = x,y,z
 * - Conditions = x=y
 * 
 * PHP version 7.0
 * 
 * @category Api
 * @package  BeautyHub_Products_API
 * @author   MF Softworks <mf@nygmarosebeauty.com>
 * @license  (C) 2018 MF Softworks
 * @link     https://github.com/nygmarose/beautyhub
 */
if (!defined("ABSPATH")) {
    define('ABSPATH', dirname(dirname(__FILE__)) . '/');
}
require_once ABSPATH . "scripts/functions.php";

/**
 * Get ID, Details & Conditions if set
 */
if (isset($_GET['id'])) {
    (is_numeric($_GET['id'])) ? $id = "= " . $_GET['id'] : $id = $_GET['id'];
}
if (isset($_GET['details'])) {
    $details = $_GET['details'];
}
if (isset($_GET['conditions'])) {
    $conditions = $_GET['conditions'];
}

// Create SQL connection
$conn = sqlConnect();

// Dynamically create SQL query
$sql = "SELECT ";
// Select $details or select * (all)
(isset($details)) ? $sql .= "$details" : $sql .= "*";
$sql .= " from products";
// If ID is given select ID, otherwise continue
(isset($id)) ? $sql .= " WHERE id $id" : null;
// If ID is given append condiitons as AND, otherwise set conditions with WHERE
(isset($id) && isset($conditions)) ? $sql .= " AND $conditions" : $sql .= " WHERE $conditions";
// End SQL query
$sql .= ";";

// If query executed successfully, print result in formatted JSON, else print formatted error.
if ($result = mysqli_query($conn, $sql)) {
    echo "<pre>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row, JSON_PRETTY_PRINT);
    }
    echo "</pre>";
} else {
    echo "<pre>";
    $error = [
        "error_code" => mysqli_errno($conn)
    ];
    echo json_encode($error, JSON_PRETTY_PRINT);
    echo "</pre>";
}
?>