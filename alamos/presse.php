<?php
require_once 'funktionen.php';
require_once 'config.php';


/*
Telegram Gruppe Presse
TEST
*/


// Log-Datei definieren
$log_file = 'get_log.txt';

// Aktuelles Datum und Uhrzeit erfassen
$timestamp = date("Y-m-d H:i:s");

// Alle GET-Parameter erfassen
$get_params = print_r($_GET, true);

// Log-Nachricht erstellen
$log_message = "[$timestamp] GET-Parameters: " . $get_params . PHP_EOL;





if ($_GET['demo'] == "true") {
    $PresseChatID = "892584383";
}



echo "TestErkannt: $PresseChatID";






//Wenn API Key per POST kommt
if (isset($_GET['apikey'])) {
    if ($_GET['apikey'] == $ValidApiKeyAlamosPresse) { // Wenn Alamos ApiKey

        // In die Log-Datei schreiben
        if (!(empty($_GET))) {
            file_put_contents($log_file, $log_message, FILE_APPEND);
        }

        //GoolgeMaps
        $gMapsUrl = 'https://maps.google.com/?q=' . $_GET['lat'] .',' . $_GET['lon'];
        $gMapsTxt = '<a href="' . $gMapsUrl . '">Adresse:</a> ';

        //Array mit den GET Infos erstellen
        $EinsatzInfos = [
            "Datum: "       . $_GET['date'],
            "Einheit: "     . $_GET['einheit'],
            $gMapsTxt       . $_GET['ziel'],
            "Stichwort: "   . $_GET['keyword'],
            "",
            "--"
        ];

        //Text imploden für Telegram
        $text = implode("\n", $EinsatzInfos);

        //An mich senden
        telegramApiPresse('892584383', $PresseBotID, 0, $text);

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
            if (stristr($EinsatzInfos['Einheit'], $keyword)) {
                $threadID = $id;
                break;
            }
        }


        //Nachricht Senden
        $result = telegramApiPresse($PresseChatID, $PresseBotID, $threadID, $text);

        //Location senden
        //$messageID = json_decode($result, true)['result']['message_id'];
        //if (isset($_GET['lat']) && isset($_GET['lon'])) {
        //telegramApiLocation($PresseChatID, $PresseBotID, $threadID, $_GET['lat'], $_GET['lon'], $messageID);
        //}


        // Test Rückmeldungen per URL an mich.
        $answersUrl= "https://availability-27b55.firebaseapp.com/feedback?dbId=" . $_GET['dbId'] . "&sharedSecret=" . $_GET['dbId_shared_secret'];
        telegramApi($AdminChatID, $PresseBotID, $answersUrl);


        
    } else {
        echo "Unbekannter API Key";
    }
}
