<?php
session_start();
include 'conndatabase.php';

// Ensure the user is logged in
if (!isset($_SESSION['admin_email'])) {
    // Redirect to home.php if not logged in
    header("Location: home.php");
    exit();
}

$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

$Rendezvous = [];

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM Rendezvous 
            WHERE id LIKE '%$search%' 
            OR date_rdv LIKE '%$search%' 
            OR heure LIKE '%$search%' 
            OR id_patient LIKE '%$search%'
            OR id_doctor LIKE '%$search%' ";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $Rendezvous[] = $row;
        }
    } else {
        $errorMsg = "No results found for '$search'.";
    }
} else {
    $Rendezvous = Rendezvous::selectAllRendezvous("Rendezvous", $conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="images\hopitallogo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendezvous List</title>
    <style>
        /* Your existing CSS code - keep it exactly as is */
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

        .search-input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-button {
            padding: 10px 20px;
            background-color: #006b96;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .add-button {
            background-color: #006b96;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .add-button:hover {
            background-color: #005577;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
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
        function showDeletePopup(id) {
            const popup = document.getElementById('confirmPopup');
            const confirmButton = document.getElementById('confirmDelete');
            
            confirmButton.onclick = function () {
                document.getElementById('deleteForm_' + id).submit();
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
        <h1>Rendezvous List</h1>

        <!-- Notification Popup -->
        <?php if (isset($_GET['status'])): ?>
            <?php
            $message = '';
            $status = htmlspecialchars($_GET['status']);
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';

            if ($status === 'added' && $action === 'add') {
                $message = "Rendezvous successfully added!";
            } elseif ($status === 'updated' && $action === 'edit') {
                $message = "Rendezvous successfully updated!";
            } elseif ($status === 'deleted') {
                $message = "Rendezvous successfully deleted!";
            } elseif ($status === 'error') {
                $message = "An error occurred. Please try again.";
            }
            ?>
            <?php if (!empty($message)): ?>
                <div class="popup-message"><?= $message ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Search and Add Section -->
        <div class="actions-container">
            <form method="GET" action="Rendezvous_list.php" class="search-form">
                <input type="text" name="search" placeholder="Search Rendezvous..." 
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                    class="search-input">
                <button type="submit" class="search-button">Search</button>
            </form>
            <a href="Rendezvous_add.php" class="add-button">Add Rendezvous</a>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>ID Patient</th>
                    <th>ID Docteur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($Rendezvous)): ?>
                    <?php foreach ($Rendezvous as $rdv): ?>
                        <tr>
                            <td><?= htmlspecialchars($rdv['id']); ?></td>
                            <td><?= htmlspecialchars($rdv['date_rdv']); ?></td>
                            <td><?= htmlspecialchars($rdv['heure']); ?></td>
                            <td><?= htmlspecialchars($rdv['id_patient']); ?></td>
                            <td><?= htmlspecialchars($rdv['id_doctor']); ?></td>
                            <td>
                                <form id="deleteForm_<?= htmlspecialchars($rdv['id']); ?>" style="display:inline;" method="POST" action="Rendezvous_delete.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($rdv['id']); ?>">
                                    <button type="button" class="btn-delete" onclick="showDeletePopup(<?= htmlspecialchars($rdv['id']); ?>)">Delete</button>
                                </form>
                                <form style="display:inline;" method="GET" action="Rendezvous_edit.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($rdv['id']); ?>">
                                    <button type="submit" class="btn-edit">Edit</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <?= isset($errorMsg) ? $errorMsg : "No Rendezvous found."; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Delete Confirmation Popup -->
        <div id="confirmPopup" class="confirm-popup">
            <p>Are you sure you want to delete this Rendezvous?</p>
            <button id="confirmDelete" class="btn-confirm">Yes</button>
            <button onclick="closeDeletePopup()" class="btn-cancel">Cancel</button>
        </div>
    </div>
</body>
</html>
