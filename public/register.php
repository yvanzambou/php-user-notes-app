<?php
    require "../app/db/dbConnect.php";

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        $stmt = $con->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // Prüfen, was genau doppelt ist
            if ($existingUser["username"] === $username && $existingUser["email"] === $email) {
                $error = "Benutzername und E-Mail bereits vergeben.";
            } elseif ($existingUser["username"] === $username) {
                $error = "Benutzername bereits vergeben.";
            } elseif ($existingUser["email"] === $email) {
                $error = "E-Mail bereits registriert.";
            }
        } else {
            // registrieren
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            registerUser($username, $email, $hashedPassword);
        }
    }

    function registerUser($username, $email, $password) {
            global $con;
            
            $stmt = $con->prepare("INSERT INTO users(username, email, password) VALUES(:username, :email, :password)");
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->execute();

            session_start();
            $_SESSION["flash_message"] = "Registrierung erfolgreich! Sie können sich jetzt anmelden.";
            header("Location: index.php");
            exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/style.css">
        <title>Registrierung</title>
    </head>
    <body>
        <?php if (!empty($error)): ?>
            <div class="warn-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST"  class="card">
            <h2>Registrierung</h2>
            <input type="text" name="username" placeholder="Name" required value="<?= htmlspecialchars($username ?? '') ?>" autocomplete="off">
            <input type="text" name="email" placeholder="e-Mail" required value="<?= htmlspecialchars($email ?? '') ?>" autocomplete="off">
            <input type="password" name="password" placeholder="Kennwort" required>
            <button type="submit">Registrieren</button>
            <p>schon registriert? <a href="index.php">einloggen</a> </p>
        </form>
    </body>
</html>