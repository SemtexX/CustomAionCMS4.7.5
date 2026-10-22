<?php
// Am Anfang jeder Seite
include('countdown_timer.php');
?>

<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

// Log-Dateipfad
$logFilePath = "C:\Users\Administrator\Desktop\AionXcentra-4.7.5\AC-Game\log\uniques.log";

// Überprüfe, ob die Datei existiert und lesbar ist
if (!file_exists($logFilePath) || !is_readable($logFilePath)) {
    die("<p style='color: red;'>Fehler: Die Log-Datei konnte nicht geöffnet werden.</p>");
}

// Datei in ein Array einlesen
$lines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Überprüfen, ob die Dateiinhalt hat
if (!$lines) {
    die("<p style='color: red;'>Fehler: Die Log-Datei ist leer oder konnte nicht gelesen werden.</p>");
}

// HTML-Ausgabe mit optimierter Tabelle
echo '<div style="overflow-y: scroll; height: 400px; border: 1px solid #ccc; padding: 10px;">';
echo '<table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">';
echo '<tr style="background-color: #f2f2f2; font-weight: bold;"><th>Date & Time</th><th>Status</th></tr>';

// Durchlaufe jede Zeile und erstelle die Tabelle
foreach ($lines as $line) {
    $data = array_map('trim', explode("----", $line));
    
    // Sicherstellen, dass keine leeren Spalten angezeigt werden
    $date = isset($data[0]) && $data[0] !== '' ? htmlspecialchars($data[0]) : null;
    $monster = isset($data[1]) && $data[1] !== '' ? htmlspecialchars($data[1]) : null;
    $map = isset($data[2]) && $data[2] !== '' ? htmlspecialchars($data[2]) : null;
    
    // Statusprüfung: Lebendig oder tot
    $status = null;
    if (strpos($line, 'appeared') !== false) {
        $status = '<span style="color: green; font-weight: bold;">Alive</span>';
    } elseif (strpos($line, 'was killed by') !== false) {
        $status = '<span style="color: red; font-weight: bold;">Dead</span>';
    }
    
    // Nur Zeilen mit gültigen Werten anzeigen
    if ($date || $monster || $map || $status) {
        echo '<tr>';
        if ($date) echo '<td>' . $date . '</td>';
        if ($monster) echo '<td>' . $monster . '</td>';
        if ($map) echo '<td>' . $map . '</td>';
        if ($status) echo '<td>' . $status . '</td>';
        echo '</tr>';
    }
}

// Schließe die Tabelle
echo '</table>';
echo '</div>';
