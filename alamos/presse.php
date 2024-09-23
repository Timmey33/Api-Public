<?php
require_once 'funktionen.php';
require_once 'config.php';


/*
Telegram Gruppe Presse
*/
// Log-Datei definieren
$log_file = 'get_log.txt';

// Aktuelles Datum und Uhrzeit erfassen
$timestamp = date("Y-m-d H:i:s");

// Alle GET-Parameter erfassen
$get_params = print_r($_GET, true);

// Log-Nachricht erstellen
$log_message = "[$timestamp] GET-Parameters: " . $get_params . PHP_EOL;

// In die Log-Datei schreiben
if (!(empty($get_params))) {
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

//Wenn API Key per POST kommt
if (isset($_GET['apikey'])) {
    if ($_GET['apikey'] == $ValidApiKeyAlamosPresse) { // Wenn Alamos ApiKey

        //Wenn Vorhanden die tac Information
        if (isset($_GET['tac'])) {
            $stichwort = $_GET['tac'];
        } else {
            $stichwort = $_GET['keywordDescription'];
        }

        //Array mit den GET Infos erstellen
        $EinsatzInfos = [
            "date"      => "Datum: " . $_GET['date'],
            "einheit"   => "Einheit: " . $_GET['einheit'],
            "ziel"      => "Adresse: " . $_GET['ziel'],
            "keyword"   => "Stichwort: " . $stichwort,
            #"message"   => "Beschreibung: " . $_GET['message'],
        ];

        //Text imploden für Telegram
        $text = implode("\n", $EinsatzInfos);

        //An mich senden
        //telegramApiPresse('892584383', $PresseBotID, 0, $text);

        //An Alle senden
        telegramApiPresse($PresseChatID, $PresseBotID, $threadIDAlle, $text);

        // Mapping der Schlüsselwörter zu den entsprechenden Thread-IDs
        $mapping = [
            'Geilenkausen'      => $threadIDGeilenkausen,
            'Waldbröl'          => $threadIDWaldbroel,
            'Thierseifen'       => $threadIDThierseifen,
            'Heide'             => $threadIDHeide,
            'Wehrführer'        => $threadIDWehrfuehrer,
            '11 F Gerätewarte'  => $threadIDHauptamtliche,
            '11 F B-Dienst'     => $threadIDHauptamtliche,
            '11 F C-Dienst'     => $threadIDHauptamtliche,
            '11 F D-Dienst'     => $threadIDHauptamtliche,
            '11 F FEL Waldbröl' => $threadIDHauptamtliche,
            '11 F Waldbröl ELW' => $threadIDHauptamtliche
        ];

        // Standardmäßig Thread-ID auf 1 setzen (General Topic)
        $threadID = 1;

        // Über das Mapping iterieren und die passende Thread-ID setzen
        foreach ($mapping as $keyword => $id) {
            if (stristr($EinsatzInfos['einheit'], $keyword)) {
                $threadID = $id;
                break;
            }
        }


        //Nachricht Senden
        $result = telegramApiPresse($PresseChatID, $PresseBotID, $threadID, $text);

        //Location senden
        $messageID = json_decode($result, true)['result']['message_id'];




        if (isset($_GET['lat']) && isset($_GET['lon'])) {
            telegramApiLocation($PresseChatID, $PresseBotID, $threadID, $_GET['lat'], $_GET['lon'], $messageID);
        }
    } else {
        echo "Unbekannter API Key";
    }
}
