<?php
session_start();
include 'conndatabase.php';

// Ensure the user is logged in
if (!isset($_SESSION['admin_email'])) {
    // Redirect to home.php if not logged in
    header("Location: home.php");
    exit();
}


$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cin'])) {
        $cin = $_POST['cin']; // Retrieve the CIN from the POST data
        
        // Try to delete the patient
        if (Patient::deletePatient("patients", $conn, $cin)) {
            header("Location: patient_list.php?status=deleted&action=delete");
            exit;
        } else {
            // Output error if deletion fails
            echo "Error: " . patient::$errorMsg;
            header("Location: patient_list.php?status=error&action=delete");
            exit;
        }
    } else {
        // If CIN is not set, show error
        header("Location: patient_list.php?status=error&action=delete");
        exit;
    }
} else {
    // Handle if the request is not POST
    header("Location: patient_list.php?status=error&action=delete");
    exit;
}
?>
