<?php
session_start();
include 'conndatabase.php';

// Ensure the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cin'])) {
        $cin = $_POST['cin']; // Retrieve the CIN from the POST data
        
        // Try to delete the doctor
        if (Doctor::deletedoctor("doctors", $conn, $cin)) {
            header("Location: doctor_list.php?status=deleted&action=delete");
            exit;
        } else {
            // Output error if deletion fails
            echo "Error: " . doctor::$errorMsg;
            header("Location: doctor_list.php?status=error&action=delete");
            exit;
        }
    } else {
        // If CIN is not set, show error
        header("Location: doctor_list.php?status=error&action=delete");
        exit;
    }
} else {
    // Handle if the request is not POST
    header("Location: doctor_list.php?status=error&action=delete");
    exit;
}
?>
