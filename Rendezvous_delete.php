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
    if (isset($_POST['id'])) {
        $id = $_POST['id']; // Retrieve the id from the POST data
        
        // Try to delete the Rendezvous
        if (Rendezvous::deleteRendezvous("Rendezvous", $conn, $id)) {
            header("Location: Rendezvous_list.php?status=deleted&action=delete");
            exit;
        } else {
            // Output error if deletion fails
            echo "Error: " . Rendezvous::$errorMsg;
            header("Location: Rendezvous_list.php?status=error&action=delete");
            exit;
        }
    } else {
        // If id is not set, show error
        header("Location: Rendezvous_list.php?status=error&action=delete");
        exit;
    }
} else {
    // Handle if the request is not POST
    header("Location: Rendezvous_list.php?status=error&action=delete");
    exit;
}
?>