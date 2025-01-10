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

// Table name
$tableName = "admins";

if (isset($_GET['cin']) && !empty($_GET['cin'])) {
    $cin = $_GET['cin'];

    // Retrieve the admin data using selectAdminBycin
    $adminData = Admin::selectAdminBycin($tableName, $conn, $cin);

    if (!$adminData) {
        // Redirect if admin not found
        header("Location: admin_list.php?status=error&action=edit&message=" . urlencode(Admin::$errorMsg));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = $_POST['cin'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $num = $_POST['num'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($firstname) && !empty($lastname) && !empty($num) && !empty($email)) {
        // Create an Admin instance and set its properties
        $admin = new Admin();
        $admin->createAdmin($cin, $firstname, $lastname, $num, $email, $password);

        // Update the admin using the updateAdmin method
        if (Admin::updateAdmin($admin, $tableName, $conn, $cin)) {
            header("Location: admin_list.php?status=updated&action=edit");
            exit;
        } else {
            header("Location: admin_list.php?status=error&action=edit&message=" . urlencode(Admin::$errorMsg));
            exit;
        }
    } else {
        header("Location: admin_list.php?status=error&action=edit&message=" . urlencode("All fields except password are required."));
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
    <title>Edit Admin</title>
    <link rel="stylesheet" href="formstyle.css">
</head>
<body>
    <h1>Edit Admin</h1>

    <div class="form-container">
        <form method="POST" action="admin_edit.php">
            <input type="hidden" name="cin" value="<?= htmlspecialchars($adminData['cin']); ?>">

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($adminData['firstname']); ?>" required>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($adminData['lastname']); ?>" required>

            <label for="num">Phone Number:</label>
            <input type="text" id="num" name="num" value="<?= htmlspecialchars($adminData['num']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($adminData['email']); ?>" required>

            <label for="password">Password (Leave blank to keep current password):</label>
            <input type="password" id="password" name="password">

            <button type="submit">Update Admin</button>
        </form>
    </div>
</body>
</html>
