<?php 
session_start();
include 'conndatabase.php';

// Ensure the user is logged in
if (!isset($_SESSION['admin_email'])) {
    // Redirect to home.php if not logged in
    header("Location: home.php");
    exit();
}

// Database connection
$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

// Initialize the Rendezvous class
$Rendezvous = new Rendezvous();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_rdv = $_POST['date_rdv'];
    $heure = $_POST['heure'];
    $id_p = $_POST['id_p']; 
    $id_d = $_POST['id_d'];

    if (!empty($date_rdv) && !empty($heure) && !empty($id_p) && !empty($id_d)) {
        // Create the Rendezvous instance
        $Rendezvous->createRendezvous($date_rdv, $heure, $id_p, $id_d);

        // Insert Rendezvous into the database
        $tableName = "Rendezvous"; // Replace with your table name
        $Rendezvous->insertRendezvous($tableName, $conn);

        // Handle success or error messages
        if (!empty(Rendezvous::$successMsg)) {
            header("Location: Rendezvous_list.php?status=added&action=add");
            exit;
        } else {
            header("Location: Rendezvous_list.php?status=error&action=add&message=" . urlencode(Rendezvous::$errorMsg));
            exit;
        }
    } else {
        header("Location: Rendezvous_list.php?status=error&action=add&message=" . urlencode("All fields are required."));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="images\hopitallogo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Rendezvous</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Add Rendezvous</h1>

    <div class="form-container">
        <form method="POST" action="Rendezvous_add.php">

            <label for="date_rdv">date du rendez-vous :</label>
            <input type="text" id="date_rdv" name="date_rdv" required>

            <label for="heure">heure du rendez-vous :</label>
            <input type="text" id="heure" name="heure" required>

            <label for="id_p">id patient :</label>
            <input type="text" id="id_p" name="id_p" required>

            <label for="id_d">id docteur :</label>
            <input type="id_d" id="id_d" name="id_d" required>


            <button type="submit">Add Rendezvous</button>
        </form>
    </div>
</body>
</html>
