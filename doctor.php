
<?php
session_start(); // Start the session


if (!isset($_SESSION['doctor_email'])) {
    // Redirect to home.php if not logged in
    header("Location: home.php");
    exit();
}

// Fetch session variables
$doctorCin = $_SESSION['doctor_cin'];
$doctorFirstname = $_SESSION['doctor_firstname'];
$doctorLastname = $_SESSION['doctor_lastname'];
$doctorSpecialite = $_SESSION['doctor_specialite'];
$doctorNum = $_SESSION['doctor_num'];
$doctorEmail = $_SESSION['doctor_email'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Page</title>
    <style>
        /* Global Styles */
        body {
            background: url('images//admpic.jpg') no-repeat center center/cover;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #006b96;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        .container {
            text-align: center;
            margin-top: 100px;
        }

        h1 {
            color: #006b96;
            margin-bottom: 20px;
        }

        
        .profile-section {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px; /* Adjust the height as per your design */
    text-align: center;
    margin-top: 30px; /* Optional, adjust margin if needed */
}

.info-container {
    background: rgba(255, 255, 255, 0.8); /* Add slight background for better readability */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 300px; /* You can adjust the width to fit your design */
}

h2 {
    margin-bottom: 10px;
}

p {
    font-size: 1rem;
    color: #333;
}


        .button-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            font-size: 1rem;
            color: white;
            background: #006b96;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #004c73;
            transform: scale(1.05);
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
    <link rel="icon" type="image/x-icon" href="images\hopitallogo.png">
</head>
<body>
    <!-- Navbar -->
 <header class="navbar">
        <a href="logout.php" class="logo">MOOrphine</a>
        <nav class="nav-links">
            <a href="doctor.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    

    <!-- Main Content -->

    <div class="profile-section">
    <div class="info-container">
        <h2>Welcome, <?= htmlspecialchars($doctorFirstname . ' ' . $doctorLastname) ?>!</h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($doctorEmail) ?></p>
    </div>
</div>

    <div class="container">
        <h1>Doctor Dashboard</h1>
        <div class="button-container">
            <a href="patient_list_doc.php" class="btn">View Patients</a>
            <a href="rendezvous_list_doc.php" class="btn">View Appointments</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 MOOrphine. All Rights Reserved.</p>
    </footer>
</body>
</html>
