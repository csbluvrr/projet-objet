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

// Initialize the Admin class
$admin = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = $_POST['cin'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $num = $_POST['num']; 
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($cin) && !empty($firstname) && !empty($lastname) && !empty($num) && !empty($email) && !empty($password)) {
        // Create the admin instance
        $admin->createAdmin($cin, $firstname, $lastname, $num, $email, $password);

        // Insert admin into the database
        $tableName = "admins"; // Replace with your table name
        $admin->insertAdmin($tableName, $conn);

        // Handle success or error messages
        if (!empty(Admin::$successMsg)) {
            header("Location: admin_list.php?status=added&action=add");
            exit;
        } else {
            header("Location: admin_list.php?status=error&action=add&message=" . urlencode(Admin::$errorMsg));
            exit;
        }
    } else {
        header("Location: admin_list.php?status=error&action=add&message=" . urlencode("All fields are required."));
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
    <title>Add Admin</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Add Admin</h1>

    <div class="form-container">
        <form method="POST" action="admin_add.php">

           <label for="cin">CIN:</label>
            <input type="text" id="cin" name="cin" required>

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required>

            <label for="num">Phone Number:</label>
            <input type="text" id="num" name="num" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Add Admin</button>
        </form>
    </div>
</body>
</html>
