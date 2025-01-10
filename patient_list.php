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

$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

$patients = [];

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM patients 
            WHERE cin LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR num LIKE '%$search%' 
            OR age LIKE '%$search%' 
            OR bloodtype LIKE '%$search%' 
            OR pcondition LIKE '%$search%' 
            OR firstname LIKE '%$search%' 
            OR lastname LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
    } else {
        $errorMsg = "No results found for '$search'.";
    }
} else {
    $patients = patient::selectAllpatients("patients", $conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="images\\emsiicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List</title>
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        /* Navbar */
        .navbar {
            background-color: #006b96;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0;
        }

        .navbar .logo {
            color: white;
            font-size: 24px;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            gap: 10px;
        }

        .navbar .nav-links a {
            background-color: #006b96;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
        }

        .navbar .nav-links a:hover {
            background-color: white;
            color: #006b96;
        }

        /* Content */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 30px 0;
        }

        /* Search and Add section */
        .actions-container {
            text-align: center;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-form {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button, 
        .add-button {
            background-color: #006b96;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        button:hover, 
        .add-button:hover {
            background-color: #005577;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
            color: black;
        }

        th {
            background-color: #006b96;
            color: white;
            padding: 15px;
            text-align: left;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            color: black;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* Action Buttons */
        .btn-edit {
            background-color: #006b96;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-edit:hover {
            background-color: #005577;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        /* Popup Styles */
        .popup-message {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #006b96;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 16px;
            z-index: 1000;
            text-align: center;
            animation: fadeOut 3s forwards;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .confirm-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 25px;
            z-index: 1000;
            display: none;
            text-align: center;
        }

        .confirm-popup p {
            margin-bottom: 20px;
            font-size: 16px;
            color: black;
        }

        .confirm-popup button {
            margin: 5px;
            padding: 10px 20px;
        }

        .btn-confirm {
            background-color: #dc3545;
            color: white;
        }

        .btn-cancel {
            background-color: #006b96;
            color: white;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            80% { opacity: 0.5; }
            100% { opacity: 0; display: none; }
        }
    </style>
    <script>
        function showDeletePopup(cin) {
            const popup = document.getElementById('confirmPopup');
            const confirmButton = document.getElementById('confirmDelete');
            
            confirmButton.onclick = function () {
                document.getElementById('deleteForm_' + cin).submit();
            };

            popup.style.display = 'block';
        }

        function closeDeletePopup() {
            document.getElementById('confirmPopup').style.display = 'none';
        }
    </script>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <a href="logout.php" class="logo">MOOrphine</a>
        <nav class="nav-links">
            <a href="admin.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h1>Patient List</h1>

        <!-- Notification Popup -->
        <?php if (isset($_GET['status'])): ?>
            <?php
            $message = '';
            $status = htmlspecialchars($_GET['status']);
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';

            if ($status === 'added' && $action === 'add') {
                $message = "Patient successfully added!";
            } elseif ($status === 'updated' && $action === 'edit') {
                $message = "Patient successfully updated!";
            } elseif ($status === 'deleted') {
                $message = "Patient successfully deleted!";
            } elseif ($status === 'error') {
                if ($action === 'add') {
                    $message = "Error adding patient. Please try again.";
                } elseif ($action === 'edit') {
                    $message = "Error updating patient. Please try again.";
                } else {
                    $message = "Error deleting patient. Please try again.";
                }
            }
            ?>
            <?php if (!empty($message)): ?>
                <div class="popup-message"><?= $message ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Search and Add Section -->
        <div class="actions-container">
            <form method="GET" action="patient_list.php" class="search-form">
                <input type="text" name="search" placeholder="Search patients..." 
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit">Search</button>
            </form>
            <a href="patient_add.php" class="add-button">Add Patient</a>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>CIN</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Age</th>
                    <th>Blood Type</th>
                    <th>Condition</th>
                    <th>Observation</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patients)): ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?= htmlspecialchars($patient['cin']); ?></td>
                            <td><?= htmlspecialchars($patient['firstname']); ?></td>
                            <td><?= htmlspecialchars($patient['lastname']); ?></td>
                            <td><?= htmlspecialchars($patient['age']); ?></td>
                            <td><?= htmlspecialchars($patient['bloodtype']); ?></td>
                            <td><?= htmlspecialchars($patient['pcondition']); ?></td>
                            <td><?= htmlspecialchars($patient['observation']); ?></td>
                            <td><?= htmlspecialchars($patient['num']); ?></td>
                            <td><?= htmlspecialchars($patient['email']); ?></td>
                            <td>
                                <form id="deleteForm_<?= htmlspecialchars($patient['cin']); ?>" style="display:inline;" method="POST" action="patient_delete.php">
                                    <input type="hidden" name="cin" value="<?= htmlspecialchars($patient['cin']); ?>">
                                    <button type="button" class="btn-delete" onclick="showDeletePopup('<?= htmlspecialchars($patient['cin']); ?>')">Delete</button>
                                </form>
                                <form style="display:inline;" method="GET" action="patient_edit.php">
                                    <input type="hidden" name="cin" value="<?= htmlspecialchars($patient['cin']); ?>">
                                    <button type="submit" class="btn-edit">Edit</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">
                            <?= isset($errorMsg) ? $errorMsg : "No patients found."; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Delete Confirmation Popup -->
        <div id="confirmPopup" class="confirm-popup">
            <p>Are you sure you want to delete this patient?</p>
            <button id="confirmDelete" class="btn-confirm">Yes</button>
            <button onclick="closeDeletePopup()" class="btn-cancel">Cancel</button>
        </div>
    </div>
</body>
</html>