<?php
session_start();
include 'conndatabase.php';

// Check if doctor is logged in
if (!isset($_SESSION['doctor_cin'])) {
    header('Location: home.php');
    exit();
}

$doctorCin = $_SESSION['doctor_cin']; // Get the logged-in doctor's CIN

$db = new Connection();
$conn = $db->dbconnect();
$db->selectdb("hopitaldb");

$RendezvousList = [];

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    // Filter by doctor cin
    $sql = "SELECT * FROM Rendezvous 
            WHERE id_doctor = '$doctorCin' 
            AND (id LIKE '%$search%' 
            OR date_rdv LIKE '%$search%' 
            OR heure LIKE '%$search%' 
            OR id_patient LIKE '%$search%')";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $RendezvousList[] = $row;
        }
    } else {
        $errorMsg = "No results found for '$search'.";
    }
} else {
    // Get all rendezvous for this doctor
    $sql = "SELECT * FROM Rendezvous WHERE id_doctor = '$doctorCin'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $RendezvousList[] = $row;
        }
    } else {
        $errorMsg = "No Rendezvous found.";
    }
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

        /* Navbar */
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
        .search-container {
            text-align: center;
            margin: 20px 0;
        }

        .search-form {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #006b96;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-button {
            background-color: #006b96;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container">
            <a href="doctor.php" class="logo">MOOrphine</a>
            <nav class="nav-links">
                <a href="doctor.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <h1>Rendezvous List</h1>

        <!-- Search and Add Section -->
        <div class="search-container">
            <form method="GET" action="Rendezvous_list_doc.php" class="search-form">
                <input type="text" name="search" placeholder="Search Rendezvous..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit">Search</button>
            </form>
            <a href="Rendezvous_add_doc.php" class="add-button">Add Rendezvous</a>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Hour</th>
                    <th>ID Patient</th>
                    <th>ID Doctor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($RendezvousList)): ?>
                    <?php foreach ($RendezvousList as $rendezvous): ?>
                        <tr>
                            <td><?= $rendezvous['id']; ?></td>
                            <td><?= htmlspecialchars($rendezvous['date_rdv']); ?></td>
                            <td><?= htmlspecialchars($rendezvous['heure']); ?></td>
                            <td><?= htmlspecialchars($rendezvous['id_patient']); ?></td>
                            <td><?= htmlspecialchars($rendezvous['id_doctor']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5"><?= isset($errorMsg) ? $errorMsg : "No Rendezvous found."; ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>