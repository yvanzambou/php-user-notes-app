<?php

declare(strict_types=1);

namespace App;

final class View
{
    /**
     * Rendert templates/<name>.php eingebettet in templates/layout.php.
     *
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = [], int $status = 200): void
    {
        http_response_code($status);

        $content = self::capture(__DIR__ . "/../templates/{$template}.php", $data);

        echo self::capture(__DIR__ . '/../templates/layout.php', [
            'title'   => $data['title'] ?? 'Notizen',
            'content' => $content,
            'user'    => Auth::user(),
            'flashes' => Flash::pull(),
        ]);
    }

    /** @param array<string, mixed> $data */
    private static function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
