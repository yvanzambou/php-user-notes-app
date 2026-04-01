<?php
    require __DIR__ . "/../app/db/dbConnect.php";
    session_start();


    // Wenn Benutzer nicht eingeloggt ist → zurück zum Login
    if (!isset($_SESSION["username"])) {
        header("Location: index.php");
        exit;
    }

    $username = $_SESSION["username"];

    // Wenn Formular abgeschickt wurde → neuen Text speichern
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["note"])) {
        $note = $_POST["note"];
        if (!empty($note)) {
            $stmt = $con->prepare("INSERT INTO notes (username, content) VALUES (:username, :content)");
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":content", $note);
            $stmt->execute();
        }
    }

    // Alle bisherigen Notizen laden
    $stmt = $con->prepare("SELECT content, created_at FROM notes WHERE username = :username ORDER BY created_at DESC");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/accountStyle.css">
        <title>Account</title>
    </head>
    <body>
        <div class="account-header">
            <h2>Hallo <?= htmlspecialchars($username) ?>,</h2>
            <form action="logout.php" method="post" style="margin:0;">
                <button class="logout-btn" type="submit">Abmelden</button>
            </form>
        </div>

        <div class="note-section">
            <h3>Deine Notizen</h3>

            <form class="add-form" action="userAccount.php" method="POST">
                <textarea name="note" placeholder="Neuen Text eingeben..." required></textarea>
                <button type="submit" class="add-btn">speichern</button>
            </form>

            <div class="note-list">
                <?php if (count($notes) > 0): ?>
                    <?php foreach ($notes as $note): ?>
                        <div class="note-item">
                            <p><?= nl2br(htmlspecialchars($note["content"])) ?></p>
                            <small><?= date("d.m.Y H:i", strtotime($note["created_at"])) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Noch keine Notizen vorhanden.</p>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>