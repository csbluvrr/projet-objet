<?php
session_start();
include 'conndatabase.php';

// Ensure the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

// Initialize the doctor class
$doctor = new doctor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = $_POST['cin'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $specialite = $_POST['specialite'];
    $num = $_POST['num']; 
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($cin) && !empty($firstname) && !empty($lastname) && !empty($specialite) && !empty($num) && !empty($email) && !empty($password)) {
        // Create the doctor instance
        $doctor->createdoctor($cin, $firstname, $lastname, $specialite, $num, $email, $password);

        // Insert doctor into the database
        $tableName = "doctors"; 
        $doctor->insertdoctor($tableName, $conn);

        // Handle success or error messages
        if (!empty(doctor::$successMsg)) {
            header("Location: doctor_list.php?status=added&action=add");
            exit;
        } else {
            header("Location: doctor_list.php?status=error&action=add&message=" . urlencode(doctor::$errorMsg));
            exit;
        }
    } else {
        header("Location: doctor_list.php?status=error&action=add&message=" . urlencode("All fields are required."));
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
    <title>Add Doctor</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Add Doctor</h1>

    <div class="form-container">
        <form method="POST" action="doctor_add.php">

           <label for="cin">CIN:</label>
            <input type="text" id="cin" name="cin" required>

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required>

            <label for="specialite">Specialty:</label>
            <input type="text" id="specialite" name="specialite" required>

            <label for="num">Phone Number:</label>
            <input type="text" id="num" name="num" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Add Doctor</button>
        </form>
    </div>
</body>
</html>
