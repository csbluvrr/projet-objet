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
$tableName = "doctors";

if (isset($_GET['cin']) && !empty($_GET['cin'])) {
    $cin = $_GET['cin'];

    // Retrieve the doctor data using selectdoctorBycin
    $doctorData = doctor::selectdoctorBycin($tableName, $conn, $cin);

    if (!$doctorData) {
        // Redirect if doctor not found
        header("Location: doctor_list.php?status=error&action=edit&message=" . urlencode(doctor::$errorMsg));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = $_POST['cin'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $specialite = $_POST['specialite'];
    $num = $_POST['num'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($firstname) && !empty($lastname) && !empty($specialite) && !empty($num) && !empty($email)) {
        // Create an doctor instance and set its properties
        $doctor = new doctor();
        $doctor->createDoctor($cin, $firstname, $lastname, $specialite, $num, $email, $password);

        // Update the doctor using the updatedoctor method
        if (doctor::updateDoctor($doctor, $tableName, $conn, $cin)) {
            header("Location: doctor_list.php?status=updated&action=edit");
            exit;
        } else {
            header("Location: doctor_list.php?status=error&action=edit&message=" . urlencode(doctor::$errorMsg));
            exit;
        }
    } else {
        header("Location: doctor_list.php?status=error&action=edit&message=" . urlencode("All fields except password are required."));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="images\\emsiicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit doctor</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Edit Doctor</h1>

    <div class="form-container">
        <form method="POST" action="doctor_edit.php">
            <input type="hidden" name="cin" value="<?= htmlspecialchars($doctorData['cin']); ?>">

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($doctorData['firstname']); ?>" required>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($doctorData['lastname']); ?>" required>

            <label for="specialite">Specialty:</label>
            <input type="text" id="specialite" name="specialite" value="<?= htmlspecialchars($doctorData['specialite']); ?>" required>

            <label for="num">Phone Number:</label>
            <input type="text" id="num" name="num" value="<?= htmlspecialchars($doctorData['num']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($doctorData['email']); ?>" required>

            <label for="password">Password (Leave blank to keep current password):</label>
            <input type="password" id="password" name="password">

            <button type="submit">Update Doctor</button>
        </form>
    </div>
</body>
</html>
