<?php
    require "../app/db/dbConnect.php";

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $con->prepare("SELECT * FROM users WHERE username=:username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC); // nur eine Zeile holen

        if ($user) {
            // Benutzername existiert, dann Kennwort prüfen
            $hashedPassword = $user["password"];
            $passwordIsCorrect = password_verify($password, $hashedPassword);

            if ($passwordIsCorrect) {
                session_start();
                $_SESSION["username"] = $user["username"];
                header("Location: userAccount.php");
                exit;
            } else {
                $error = "Benutzername oder Kennwort inkorrekt.";
            }
        } else {
            // Kein Benutzer mit diesem Namen gefunden
            $error = "Benutzername oder Kennwort inkorrekt.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/style.css">
        <title>Anmeldung</title>
    </head>
    <body>
        <?php
            session_start();

            if (isset($_SESSION["flash_message"])) {
                echo '<div id="flash-message" class="success-message">' . htmlspecialchars($_SESSION["flash_message"]) . '</div>';
                unset($_SESSION["flash_message"]);
            }

            if (!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif;
        ?>

        <form action="index.php" method="POST"  class="card">
            <h2>Anmeldung</h2>
            <input type="text" name="username" placeholder="Benutzername" required value="<?= htmlspecialchars($username ?? '') ?>" autocomplete="off">
            <input type="password" name="password" placeholder="Kennwort" required value="<?= htmlspecialchars($password ?? '') ?>" autocomplete="off">
            <button type="submit">Anmelden</button>
            <p>noch nicht registriert? <a href="register.php">registrieren</a></p>
        </form>

        <script src="assets/js/jscript.js"></script>
    </body>
</html>