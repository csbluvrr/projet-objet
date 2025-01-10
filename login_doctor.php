<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'conndatabase.php';
$connection = new Connection();

// Error variables and input defaults
$email_userError = '';
$password_userError = '';
$loginError = '';
$email_user = '';
$password_user = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and trim input
    $email_user = htmlspecialchars(trim($_POST['email']));
    $password_user = trim($_POST['password']);

    // Validate inputs
    if (empty($email_user)) {
        $email_userError = 'Veuillez entrer votre adresse email.';
    }

    if (empty($password_user)) {
        $password_userError = 'Veuillez entrer votre mot de passe.';
    }

    // Proceed if no input errors
    if (empty($email_userError) && empty($password_userError)) {
        // Test database connection
        $conn = $connection->dbconnect();
        if (!$conn) {
            die('Échec de connexion à la base de données : ' . mysqli_connect_error());
        }

        // Sélectionner explicitement la base de données
        if (!mysqli_select_db($conn, "hopitaldb")) {
            die('Échec de sélection de la base de données : ' . mysqli_error($conn));
        }

        // Prepare SQL statement for doctors table
        $stmt = $conn->prepare("SELECT cin, firstname, lastname, specialite, num, email, password FROM doctors WHERE email = ?");
        if (!$stmt) {
            die("Erreur de préparation de requête : " . $conn->error);
        }

        // Bind and execute statement
        $stmt->bind_param("s", $email_user);
        if (!$stmt->execute()) {
            die("Échec d'exécution de la requête : " . $stmt->error);
        }

        // Store the result to verify the doctor exists
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Bind the results
            $stmt->bind_result($doctorCin, $doctorFirstname, $doctorLastname, $doctorSpecialite, $doctorNum, $dbEmail, $dbPassword);
            $stmt->fetch();

            // Verify hashed password
            if (password_verify($password_user, $dbPassword)) {
                // Set session variables
                $_SESSION['doctor_cin'] = $doctorCin;
                $_SESSION['doctor_firstname'] = $doctorFirstname;
                $_SESSION['doctor_lastname'] = $doctorLastname;
                $_SESSION['doctor_specialite'] = $doctorSpecialite;
                $_SESSION['doctor_num'] = $doctorNum;
                $_SESSION['doctor_email'] = $dbEmail;

                // Redirect to doctor home
                header("Location: doctor.php");
                exit();
            } else {
                $loginError = 'Identifiants invalides (mot de passe incorrect).';
            }
        } else {
            $loginError = 'Identifiants invalides (utilisateur introuvable).';
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Docteur</title>
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

       
        /* Content */
        h2 {
            text-align: center;
            color: #333;
            margin: 30px 0;
        }

        form {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #006b96;
            outline: none;
            box-shadow: 0 0 4px rgba(0, 107, 150, 0.3);
        }

        button {
            width: 100%;
            background-color: #006b96;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #005577;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }
    </style>
    <link rel="icon" type="image/x-icon" href="images\hopitallogo.png">
</head>
<body>
   

    <h2>Connexion Docteur</h2>
    <form method="POST" action="">
        <label for="email">Adresse Email :</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email_user); ?>" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <?php if (!empty($loginError)): ?>
            <div class="error-message"><?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>