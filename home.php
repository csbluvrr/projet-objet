<!DOCTYPE html>
<html lang="fr">
<head>
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
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
        width: 90%;
        margin: auto;
    }

    /* Hero Section */
    .hero {
        height: 80vh;
        background: url('images//lastone.png') no-repeat center center/cover;
        color: white;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-content {
        max-width: 600px;
    }

    .hero-content h1 {
        font-size: 3rem;
    }

    .hero-content p {
        margin: 20px 0;
        font-size: 1.2rem;
    }

    .hero-content .btn {
        padding: 10px 20px;
        background: #006b96;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    /* Cards Section */
    .cards {
        padding: 20px 0;
        text-align: center;
    }

    .cards h2 {
        margin-bottom: 30px;
    }

    .card-container {
        display: flex;
        gap: 20px;
        justify-content: center;
    }

    .card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        width: 300px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card a {
        text-decoration: none;
        color: inherit;
    }

    .card img {
        width: 50%;
        border-radius: 8px;
    }

    .card h3 {
        margin: 15px 0;
        color: #006b96;
    }

    .card p {
        color: #666;
        margin-bottom: 0;
    }

    /* Footer */
    footer {
        background: #333;
        color: white;
        text-align: center;
        padding: 20px 0;
    }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="icon" type="image/x-icon" href="images\hopitallogo.png">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <a href="home.php" class="logo">MOOrphine</a>
        <nav class="nav-links">
            <a href="login_admin.php">Admin</a>
            <a href="login_doctor.php">Doctor</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1></h1>
            <p></p>
        </div>
    </section>

    <!-- Cards Section -->
    <section id="services" class="cards">
        <h2>login</h2>
        <div class="card-container">
            <div class="card">
                <a href="login_admin.php">
                    <img src="images\\adminicon.png" alt="Service 1">
                    <h3>Administration</h3>
                    <p>Login to administration space</p>
                </a>
            </div>
            <div class="card">
                <a href="login_doctor.php">
                    <img src="images\\doctors.png" alt="Service 2">
                    <h3>Doctors</h3>
                    <p>login to Doctors space.</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 MOOrphine. Tous droits réservés.</p>
    </footer>
</body>
</html>
