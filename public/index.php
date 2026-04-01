<?php
session_start();
require __DIR__ . "/../app/db/dbConnect.php";

$error = "";

// Flash-Message abholen (falls vorhanden)
$flashMessage = $_SESSION["flash_message"] ?? null;
unset($_SESSION["flash_message"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $con->prepare("SELECT * FROM users WHERE username=:username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $hashedPassword = $user["password"];

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["username"] = $user["username"];
            header("Location: userAccount.php");
            exit;
        } else {
            $error = "Benutzername oder Kennwort inkorrekt.";
        }
    } else {
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

    <?php if ($flashMessage): ?>
        <div id="flash-message" class="success-message">
            <?= htmlspecialchars($flashMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="index.php" method="POST" class="card">
        <h2>Anmeldung</h2>
        <input type="text" name="username" placeholder="Benutzername" required value="<?= htmlspecialchars($username ?? '') ?>" autocomplete="off">
        <input type="password" name="password" placeholder="Kennwort" required autocomplete="off">
        <button type="submit">Anmelden</button>
        <p>noch nicht registriert? <a href="register.php">registrieren</a></p>
    </form>

    <script src="assets/js/jscript.js"></script>
</body>
</html>