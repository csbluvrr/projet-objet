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

$admins = [];

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM admins 
            WHERE cin LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR num LIKE '%$search%' 
            OR firstname LIKE '%$search%' 
            OR lastname LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
    } else {
        $errorMsg = "No results found for '$search'.";
    }
} else {
    $admins = Admin::selectAllAdmins("admins", $conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="images\\emsiicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin List</title>
    <style>
    /* Base styles */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
    }

    /* Container */
    .container {
        width: 90%;
        margin: auto;
    }

    /* Updated Navbar */
    .navbar {
        background-color: #006b96;
        padding: 15px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar .logo {
        font-size: 1.5rem;
        color: white;
        font-weight: bold;
        text-decoration: none;
    }

    .navbar .nav-links {
        display: flex;
        gap: 20px;
    }

    .navbar .nav-links a {
        color: white;
        text-decoration: none;
        font-size: 1rem;
        padding: 8px 12px;
        border-radius: 5px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .navbar .nav-links a:hover {
        background-color: white;
        color: #006b96;
    }

    /* Content */
    .main-container {
        width: 90%;
        margin: 0 auto;
        padding: 20px 0;
    }

    h1 {
        text-align: center;
        color: #333;
        margin: 30px 0;
    }

    /* Search and Add section */
    div[style*="text-align: center"] {
        margin: 20px 0;
    }

    input[type="text"] {
        padding: 8px;
        width: 300px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    button, 
    a[href="admin_add.php"] {
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
    a[href="admin_add.php"]:hover {
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
        transition: background-color 0.3s ease;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
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
            const popup = document.getElementById('confirmPopup');
            popup.style.display = 'none';
        }
    </script>
</head>
<body>
    <!-- Updated Navbar -->
    <header class="navbar">
        <div class="container">
            <a href="logout.php" class="logo">MOOrphine</a>
            <nav class="nav-links">
                <a href="admin.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <h1>Admin List</h1>

        <!-- Notification Popup -->
        <?php if (isset($_GET['status'])): ?>
            <?php
            $message = '';
            $status = htmlspecialchars($_GET['status']);
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';

            if ($status === 'added' && $action === 'add') {
                $message = "Admin successfully added!";
            } elseif ($status === 'updated' && $action === 'edit') {
                $message = "Admin successfully updated!";
            } elseif ($status === 'deleted') {
                $message = "Admin successfully deleted!";
            } elseif ($status === 'error') {
                if ($action === 'add') {
                    $message = "Error adding admin. Please try again.";
                } elseif ($action === 'edit') {
                    $message = "Error updating admin. Please try again.";
                } else {
                    $message = "Error deleting admin. Please try again.";
                }
            }
            ?>
            <?php if (!empty($message)): ?>
                <div class="popup-message"><?= $message ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Search Form -->
        <div style="text-align: center; margin-bottom: 20px;">
            <form method="GET" action="admin_list.php" style="margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Search admins..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit">Search</button>
            </form>
            <a href="admin_add.php">Add admin</a>
        </div>

        <!-- Admin Table -->
        <table>
            <thead>
                <tr>
                    <th>CIN</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($admins)): ?>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= htmlspecialchars($admin['cin']); ?></td>
                            <td><?= htmlspecialchars($admin['firstname']); ?></td>
                            <td><?= htmlspecialchars($admin['lastname']); ?></td>
                            <td><?= htmlspecialchars($admin['num']); ?></td>
                            <td><?= htmlspecialchars($admin['email']); ?></td>
                            <td>
                                <form id="deleteForm_<?= htmlspecialchars($admin['cin']); ?>" style="display:inline;" method="POST" action="admin_delete.php">
                                    <input type="hidden" name="cin" value="<?= htmlspecialchars($admin['cin']); ?>">
                                    <button type="button" class="btn-delete" onclick="showDeletePopup('<?= htmlspecialchars($admin['cin']); ?>')">Delete</button>
                                </form>
                                <form style="display:inline;" method="GET" action="admin_edit.php">
                                    <input type="hidden" name="cin" value="<?= htmlspecialchars($admin['cin']); ?>">
                                    <button type="submit" class="btn-edit">Edit</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <?= isset($errorMsg) ? $errorMsg : "No admins found."; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Delete Confirmation Popup -->
        <div id="confirmPopup" class="confirm-popup">
            <p>Are you sure you want to delete this admin?</p>
            <button id="confirmDelete" class="btn-confirm">Yes</button>
            <button onclick="closeDeletePopup()" class="btn-cancel">Cancel</button>
        </div>
    </div>
</body>
</html>
