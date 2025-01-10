<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Initialize the patient class
$patient = new patient();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = $_POST['cin'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $bloodtype = $_POST['bloodtype'];
    $pcondition = $_POST['pcondition'];
    $observation = $_POST['observation'];
    $num = $_POST['num']; 
    $email = $_POST['email'];


    if (!empty($cin) && !empty($firstname) && !empty($lastname) && !empty($age) && !empty($bloodtype) && !empty($pcondition) && !empty($observation) && !empty($num) && !empty($email)) {
        // Create the patient instance
        $patient->createPatient($cin, $firstname, $lastname, $age, $bloodtype, $pcondition, $observation, $num, $email);

        // Insert patient into the database
        $tableName = "patients"; 
        $patient->insertPatient($tableName, $conn);

        // Handle success or error messages
        if (!empty(patient::$successMsg)) {
            header("Location: patient_list.php?status=added&action=add");
            exit;
        } else {
            header("Location: patient_list.php?status=error&action=add&message=" . urlencode(patient::$errorMsg));
            exit;
        }
    } else {
        header("Location: patient_list.php?status=error&action=add&message=" . urlencode("All fields are required."));
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
    <title>Add patient</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Add patient</h1>

    <div class="form-container">
        <form method="POST" action="patient_add.php">

           <label for="cin">CIN:</label>
            <input type="text" id="cin" name="cin" required>

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="bloodtype">Blood type:</label>
            <input type="text" id="bloodtype" name="bloodtype" >

            <label for="pcondition">Condition:</label>
            <input type="text" id="pcondition" name="pcondition" >

            <label for="observation">Observation:</label>
            <input type="text" id="observation" name="observation" >

            <label for="num">Phone Number:</label>
            <input type="text" id="num" name="num" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>


            <button type="submit">Add Patient</button>
        </form>
    </div>
</body>
</html>
