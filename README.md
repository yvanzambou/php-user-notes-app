# 🧩 PHP User Notes App

Eine einfache Webanwendung in **PHP** mit **Benutzerregistrierung**, **Login** und **persönlicher Notizverwaltung**.  
Sie wurde mit **PDO** und **MySQL (MariaDB)** entwickelt und eignet sich ideal als Beispielprojekt für moderne PHP-Webentwicklung.

---

## 🚀 Funktionen

- 🔐 Benutzerregistrierung mit Passwort-Hashing (`password_hash()`)
- 🔑 Benutzer-Login mit sicherer Authentifizierung (`password_verify()`)
- 💾 Speicherung der Daten in MySQL über PDO (prepared statements)
- 🧠 Persönlicher Account-Bereich (geschützt durch Sessions)
- 📝 Benutzer kann eigene Texte / Notizen hinzufügen
- 🗑️ Automatische Löschung der Notizen beim Löschen des Benutzers (FOREIGN KEY)
- ⚡ Automatisches Erstellen der Datenbank und Tabellen beim Start

---

## 🧰 Verwendete Technologien

| Bereich | Technologie |
|----------|--------------|
| Backend | PHP 8.x |
| Datenbank | MySQL / MariaDB |
| Server | Apache (z. B. über XAMPP oder Laragon) |
| Styling | HTML5, CSS3 |
| Sicherheit | Passwort-Hashing, Prepared Statements, Sessions |

---

## ⚙️ Installation & Einrichtung

### 🔸 Voraussetzungen
- XAMPP installieren

### 🔸 Schritte

1. **Repository klonen oder herunterladen**
   ```bash
   git clone https://github.com/yvanzambou/php-user-notes-app.git

2. **Projekt in deinen lokalen Serverordner legen**:
      C:\xampp\htdocs\php-user-notes-app

3. **Server starten**
      - Apache & MySQL in XAMPP aktivieren
      - Im Browser aufrufen: http://localhost/php-user-notes-app/public/

4. **Automatische Datenbankerstellung**
      Beim ersten Start erstellt das Skript automatisch:
      - die Datenbank **userdb**
      - die Tabellen **users** und **notes**

---

## 🧑‍💻 Verwendung

      **Registrieren:**
      Erstelle ein neues Benutzerkonto unter /register.php

      **Anmelden:**
      Logge dich über /index.php ein

      **Notizen hinzufügen:**
      Auf der Benutzerseite (userAccount.php) kannst du Texte über den „speichern“-Button hinzufügen.
      Diese werden automatisch in der Datenbank gespeichert.

      **Abmelden:**
      Über den Button „Abmelden“ wird die aktuelle Session beendet.