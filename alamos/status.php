<?php
require_once('config.php');

#region Funktionen
function deleteOldEntrys()
{
    global $servername;
    global $username;
    global $password;
    global $dbname;
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    // Überprüfen, ob Verbindung erfolgreich hergestellt wurde
    if (!$conn) {
        die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
    }
    
    // Datensätze löschen, die älter als 1 Monat sind
    $one_month_ago = date('Y-m-d', strtotime('-1 month'));
    
    $sql = "DELETE FROM Abfragen WHERE timestamp < '$one_month_ago'";
    
    if (mysqli_query($conn, $sql)) {
        echo "Datensätze erfolgreich gelöscht $one_month_ago";
    } else {
        echo "Fehler beim Löschen der Datensätze: " . mysqli_error($conn);
    }
    
    // Verbindung zur MySQL-Datenbank schließen
    mysqli_close($conn);
}
deleteOldEntrys();
?>
