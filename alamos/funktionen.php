<?php
require_once 'config.php';

function neuenEinsatzEintragen($txt)
{
    global $servername;
    global $username;
    global $password;
    global $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "INSERT INTO `Feuerwehr`.`Alarmierungen` (`txt`) VALUES ('$txt');";

    if (!mysqli_query($conn, $query))
    {
        die('ES ist leider ein Fehler aufgetreten. Bitte versuch es nochmal');
    }
}

//Logt die Abfragen
function abfrageLoggen($txt)
{
    global $servername;
    global $username;
    global $password;
    global $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "INSERT INTO `Feuerwehr`.`Abfragen` (`result`) VALUES ('$txt');";

    if (!mysqli_query($conn, $query))
    {
        die('ES ist leider ein Fehler aufgetreten. Bitte versuch es nochmal');
    }
}

function CheckAlarmierung()
{
    global $servername;
    global $username;
    global $password;
    global $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query("select * from Alarmierungen where gelesen = 0 ORDER BY id DESC limit 1");

    if ($result->num_rows > 0)
    {
        while ($row = $result->fetch_assoc())
        {
            $rows[] = $row;
        }

        #Update DB Entry
        $conn->query("UPDATE `Feuerwehr`.`Alarmierungen` SET `gelesen`='1' WHERE `id`={$rows[0]['id']};");
        $conn->close();

        return $rows;
    }
    else
    {
        $conn->close();
        return false;
    }
}

function letzteAbfrage()
{
    global $servername;
    global $username;
    global $password;
    global $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query("select * from Abfragen ORDER BY id DESC limit 1");

    if ($result->num_rows > 0)
    {
        while ($row = $result->fetch_assoc())
        {
            $rows[] = $row;
        }
        return $rows[0]["timestamp"];
    }
    else
    {
        $conn->close();
        return false;
    }
}

function telegramApi($chatID, $botID, $text)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, ["chat_id" => $chatID, "text" => $text, ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/' . $botID . '/sendMessage');
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

#Sendet einen Standort
function telegramApiLocation($chatID, $botID, $threadID, $lat, $long, $MessageID)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, [ "chat_id" => $chatID, "message_thread_id" => $threadID, "latitude" => $lat, "longitude" => $long, "reply_to_message_id" => $MessageID, "disable_notification" => "true"]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/' . $botID . '/sendLocation');
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

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

function telegramApiPresse($chatID, $botID, $threadID, $text)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, ["chat_id" => $chatID, "text" => $text, "message_thread_id" => $threadID, "parse_mode" => "html", "disable_web_page_preview" => "false"]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/' . $botID . '/sendMessage');
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}


function neuenEinsatzEintragenPresse($EinsatzInfos)
{
    global $servername;
    global $username;
    global $password;
    global $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

//    $query = "INSERT INTO `Feuerwehr`.`AlarmierungenPresse` (`txt`) VALUES ('$txt');";

    
    $query = "INSERT INTO `Feuerwehr`.`AlarmierungenPresse` (`date`, `einheit`, `ziel`, `keyword`, `keywordDescription`) VALUES ('01.01.2001', 'Geilenk', 'strasse', 'b1', 'Tets');";
    if (!mysqli_query($conn, $query))
    {
        die('ES ist leider ein Fehler aufgetreten. Bitte versuch es nochmal');
    }
}
