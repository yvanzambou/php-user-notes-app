<?php

declare(strict_types=1);

/** HTML-Escaping für Ausgaben in Templates. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Formatiert einen UTC-Zeitstempel aus der Datenbank in der Anwendungs-Zeitzone. */
function format_datetime(string $utc): string
{
    $date = new DateTimeImmutable($utc, new DateTimeZone('UTC'));

    return $date->setTimezone(new DateTimeZone(date_default_timezone_get()))->format('d.m.Y, H:i');
}
