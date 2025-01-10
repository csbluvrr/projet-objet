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

// Table name
$tableName = "rendezvous";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the Rendezvous data using selectRendezvousByid
    $RendezvousData = Rendezvous::selectRendezvousById($tableName, $conn, $id);

    if (!$RendezvousData) {
        // Redirect if Rendezvous not found
        header("Location: Rendezvous_list.php?status=error&action=edit&messid_patient=" . urlencode(Rendezvous::$errorMsg));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $date_rdv = $_POST['date_rdv'];
    $heure = $_POST['heure'];
    $id_patient = $_POST['id_patient'];
    $id_doctor = $_POST['id_doctor'];
    

    if (!empty($date_rdv) && !empty($heure) && !empty($id_patient) && !empty($id_doctor)) {
        // Create an Rendezvous instance and set its properties
        $Rendezvous = new Rendezvous();
        $Rendezvous->createRendezvous( $date_rdv, $heure, $id_patient, $id_doctor);

        // Update the Rendezvous using the updateRendezvous method
        if (Rendezvous::updateRendezvous($Rendezvous, $tableName, $conn, $id)) {
            header("Location: Rendezvous_list.php?status=updated&action=edit");
            exit;
        } else {
            header("Location: Rendezvous_list.php?status=error&action=edit&messid_patient=" . urlencode(Rendezvous::$errorMsg));
            exit;
        }
    } else {
        header("Location: Rendezvous_list.php?status=error&action=edit&messid_patient=" . urlencode("All fields except password are required."));
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
    <title>Edit Rendezvous</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Edit Rendezvous</h1>

    <div class="form-container">
        <form method="POST" action="Rendezvous_edit.php">
            <input type="hidden" name="id" value="<?= $RendezvousData['id']; ?>">

            <label for="date_rdv">date du rendez-vous :</label>
            <input type="text" id="date_rdv" name="date_rdv" value="<?= htmlspecialchars($RendezvousData['date_rdv']); ?>" required>

            <label for="heure">heure du rendez-vous :</label>
            <input type="text" id="heure" name="heure" value="<?= htmlspecialchars($RendezvousData['heure']); ?>" required>

            <label for="id_patient">id patient:</label>
            <input type="number" id="id_patient" name="id_patient" value="<?= htmlspecialchars($RendezvousData['id_patient']); ?>" required>

            <label for="id_doctor">id docteur :</label>
            <input type="text" id="id_doctor" name="id_doctor" value="<?= htmlspecialchars($RendezvousData['id_doctor']); ?>" required>


            <button type="submit">Update Rendezvous</button>
        </form>
    </div>
</body>
</html>
