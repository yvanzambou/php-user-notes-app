<?php

declare(strict_types=1);

namespace App;

use App\Http\Http;
use App\Repository\UserRepository;

final class Auth
{
    /** @var array<string, mixed>|null */
    private static ?array $user = null;
    private static bool $loaded = false;
    private static ?string $dummyHash = null;

    /**
     * Der aktuell angemeldete Benutzer oder null.
     *
     * @return array<string, mixed>|null
     */
    public static function user(): ?array
    {
        if (self::$loaded) {
            return self::$user;
        }

        Session::start();
        self::$loaded = true;

        $id = $_SESSION['user_id'] ?? null;
        if (is_int($id)) {
            self::$user = (new UserRepository(Database::connection()))->findById($id);
            if (self::$user === null) {
                unset($_SESSION['user_id']); // Konto existiert nicht mehr
            }
        }

        return self::$user;
    }

    /** @return array<string, mixed> */
    public static function requireUser(): array
    {
        return self::user() ?? Http::redirect('/login.php');
    }

    public static function requireGuest(): void
    {
        if (self::user() !== null) {
            Http::redirect('/notes.php');
        }
    }

    /** @param array<string, mixed> $user */
    public static function login(array $user): void
    {
        Session::regenerate(); // Schutz vor Session Fixation
        Csrf::rotate();
        $_SESSION['user_id'] = (int) $user['id'];
        self::$user = $user;
        self::$loaded = true;
    }

    public static function logout(): void
    {
        Session::destroy();
        self::$user = null;
        self::$loaded = true;

        Session::start();       // frische Session für die Abmelde-Meldung
        session_regenerate_id(true);
    }

    /**
     * Prüft ein Passwort in gleichbleibender Zeit – auch wenn der Benutzer nicht existiert.
     * Verhindert, dass man über die Antwortzeit gültige Benutzernamen erraten kann.
     *
     * @param array<string, mixed>|null $user
     */
    public static function verifyPassword(?array $user, string $password): bool
    {
        self::$dummyHash ??= password_hash('dummy-password', PASSWORD_DEFAULT);
        $hash = $user['password_hash'] ?? self::$dummyHash;

        return password_verify($password, $hash) && $user !== null;
    }
}
