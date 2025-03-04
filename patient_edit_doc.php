<?php
session_start(); // Start the session


if (!isset($_SESSION['doctor_email'])) {
    // Redirect to home.php if not logged in
    header("Location: home.php");
    exit();
}

include 'conndatabase.php';

// Database connection
$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

// Table name
$tableName = "patients";

if (isset($_GET['cin']) && !empty($_GET['cin'])) {
    $cin = $_GET['cin'];

    // Retrieve the patient data using selectpatientBycin
    $patientData = patient::selectpatientBycin($tableName, $conn, $cin);

    if (!$patientData) {
        // Redirect if patient not found
        header("Location: patient_list_doc.php?status=error&action=edit&message=" . urlencode(patient::$errorMsg));
        exit;
    }
}

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

    if (!empty($firstname) && !empty($lastname) && !empty($age) && !empty($bloodtype) && !empty($pcondition) && !empty($observation) && !empty($num) && !empty($email)) {
        // Create an patient instance and set its properties
        $patient = new patient();
        $patient->createPatient($cin, $firstname, $lastname, $age, $bloodtype, $pcondition, $observation, $num, $email);

        // Update the patient using the updatepatient method
        if (patient::updatePatient($patient, $tableName, $conn, $cin)) {
            header("Location: patient_list_doc.php?status=updated&action=edit");
            exit;
        } else {
            header("Location: patient_list_doc.php?status=error&action=edit&message=" . urlencode(patient::$errorMsg));
            exit;
        }
    } else {
        header("Location: patient_list_doc.php?status=error&action=edit&message=" . urlencode("All fields except password are required."));
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
    <title>Edit patient</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Edit patient</h1>

    <div class="form-container">
        <form method="POST" action="patient_edit.php">
            <input type="hidden" name="cin" value="<?= htmlspecialchars($patientData['cin']); ?>">

            <input type="hidden"  name="firstname" value="<?= htmlspecialchars($patientData['firstname']); ?>" >

            <input type="hidden" * name="lastname" value="<?= htmlspecialchars($patientData['lastname']); ?>" >

            <input type="hidden"  name="age" value="<?= htmlspecialchars($patientData['age']); ?>">

            <input type="hidden"  name="bloodtype" value="<?= htmlspecialchars($patientData['bloodtype']); ?>" >

            <label for="pcondition">Condition:</label>
            <input type="text" id="pcondition" name="pcondition" value="<?= htmlspecialchars($patientData['pcondition']); ?>" >

            <label for="observation">Observation:</label>
            <input type="text" id="observation" name="observation" value="<?= htmlspecialchars($patientData['observation']); ?>" >

            <input type="hidden"  name="num" value="<?= htmlspecialchars($patientData['num']); ?>" >

            <input type="hidden"  name="email" value="<?= htmlspecialchars($patientData['email']); ?>" >

            <button type="submit">Update patient</button>
        </form>
    </div>
</body>
</html>
