<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

// Öffne die Log-Datei im Lesemodus
$logFilePath = "C:\\Users\\Administrator\\Desktop\\AionXcentra-4.7.5\\AC-Game\\log\\console.log";
$fp = fopen($logFilePath, "r");

// Überprüfe, ob die Datei erfolgreich geöffnet wurde
if ($fp) {
    // Beginne die Ausgabe der Tabelle
    echo '<div style="overflow-y: scroll; height: 500px;">'; // Feste Höhe und Scrollfunktion
    echo '<table border="1">';
    echo '<tr><th><center>Gameserver Logs</center></th></tr>';

    // Durchlaufe die Datei und gib jede Zeile aus
    while (!feof($fp)) {
        $line = fgets($fp);
        // Explodiere die Zeile in ihre Bestandteile
        $data = explode("----", $line);
        // Ausgabe jeder Zeile als Tabellenzeile
        echo '<tr>';
        foreach($data as $cell) {
            echo '<td>'.$cell.'</td>';
        }
        echo '</tr>';
    }

    // Schließe die Tabelle
    echo '</table>';
    echo '</div>'; // Ende des scrollbaren Bereichs

    // Schließe die Datei
    fclose($fp);
} else {
    // Fehlermeldung, wenn die Datei nicht geöffnet werden kann
    echo "Die Log-Datei konnte nicht geöffnet werden.";
}
?>
<br></br>
<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

// Öffne die Log-Datei im Lesemodus
$logFilePath = "C:\\Users\\Administrator\\Desktop\\AionXcentra-4.7.5\\AC-Login\\log\\console.log";
$fp = fopen($logFilePath, "r");

// Überprüfe, ob die Datei erfolgreich geöffnet wurde
if ($fp) {
    // Beginne die Ausgabe der Tabelle
    echo '<div style="overflow-y: scroll; height: 500px;">'; // Feste Höhe und Scrollfunktion
    echo '<table border="1">';
    echo '<tr><th><center>Loginserver Logs</center></th></tr>';

    // Durchlaufe die Datei und gib jede Zeile aus
    while (!feof($fp)) {
        $line = fgets($fp);
        // Explodiere die Zeile in ihre Bestandteile
        $data = explode("----", $line);
        // Ausgabe jeder Zeile als Tabellenzeile
        echo '<tr>';
        foreach($data as $cell) {
            echo '<td>'.$cell.'</td>';
        }
        echo '</tr>';
    }

    // Schließe die Tabelle
    echo '</table>';
    echo '</div>'; // Ende des scrollbaren Bereichs

    // Schließe die Datei
    fclose($fp);
} else {
    // Fehlermeldung, wenn die Datei nicht geöffnet werden kann
    echo "Die Log-Datei konnte nicht geöffnet werden.";
}
?>
<br></br>
<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

// Öffne die Log-Datei im Lesemodus
$logFilePath = "C:\\Users\\Administrator\\Desktop\\AionXcentra-4.7.5\\AC-Chat\\log\\console.log";
$fp = fopen($logFilePath, "r");

// Überprüfe, ob die Datei erfolgreich geöffnet wurde
if ($fp) {
    // Beginne die Ausgabe der Tabelle
    echo '<div style="overflow-y: scroll; height: 500px;">'; // Feste Höhe und Scrollfunktion
    echo '<table border="1">';
    echo '<tr><th><center>Chatserver Logs</center></th></tr>';

    // Durchlaufe die Datei und gib jede Zeile aus
    while (!feof($fp)) {
        $line = fgets($fp);
        // Explodiere die Zeile in ihre Bestandteile
        $data = explode("----", $line);
        // Ausgabe jeder Zeile als Tabellenzeile
        echo '<tr>';
        foreach($data as $cell) {
            echo '<td>'.$cell.'</td>';
        }
        echo '</tr>';
    }

    // Schließe die Tabelle
    echo '</table>';
    echo '</div>'; // Ende des scrollbaren Bereichs

    // Schließe die Datei
    fclose($fp);
} else {
    // Fehlermeldung, wenn die Datei nicht geöffnet werden kann
    echo "Die Log-Datei konnte nicht geöffnet werden.";
}
?>
