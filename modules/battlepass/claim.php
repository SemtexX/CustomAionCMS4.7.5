<?php
/**
 * BattlePass claim endpoint.
 * Expects POST: tier_id, character, csrf_token
 * Responds with JSON.
 */

// Fehlerausgabe für AJAX aktivieren (damit wir im Fehlerfall sehen was los ist)
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

// AionCMS Session / System laden (Pfad ggf. anpassen, falls nötig)
// Wenn dein CMS eine zentrale init-Datei hat, wird diese hier meist schon geladen.
// Falls die Session fehlt, prüfen wir sie hier.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Prüfen ob eingeloggt
    if (!isLoggedIn()) {
        throw new Exception('Not logged in.');
    }

    // CSRF check
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Invalid CSRF token.');
    }

    if (!isset($_POST['tier_id']) || $_POST['tier_id'] === '') {
        throw new Exception('No tier specified.');
    }
    if (!isset($_POST['character']) || $_POST['character'] === '') {
        throw new Exception('No character specified.');
    }

    // BattlePass Bibliothek laden
    if (!class_exists('BattlePass')) {
        $bpLib = __PATH_LIBRARIES__ . 'lib.battlepass.php';
        if (!@include_once($bpLib)) {
            throw new Exception('Could not load BattlePass library.');
        }
    }

    $BP = new BattlePass();
    $BP->setUserId($_SESSION['userid']);
    $BP->setServer('siel');

    if (!$BP->hasActiveSeason()) {
        throw new Exception('No active season.');
    }

    // Belohnung abholen
    $msg = $BP->claimTier((int)$_POST['tier_id'], $_POST['character']);

    // Erfolgreiche Antwort
    echo json_encode(['ok' => true, 'message' => $msg]);
} catch (Exception $ex) {
    // Fehler-Antwort
    echo json_encode(['ok' => false, 'message' => $ex->getMessage()]);
} catch (Error $ex) {
    // Fängt schwere PHP-Fehler ab
    echo json_encode(['ok' => false, 'message' => 'PHP Error: ' . $ex->getMessage()]);
}