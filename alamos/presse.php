<?php
require_once "funktionen.php";
require_once('config.php');


/*
Telegram Gruppe Presse
*/


//Wenn API Key per POST kommt
if (isset($_GET['apikey'])) {
    if ($_GET['apikey'] == $ValidApiKeyAlamosPresse) { // Wenn Alamos ApiKey

        //Array mit den GET Infos erstellen
        $EinsatzInfos = [
            "date" => "Datum: " . $_GET['date'],
            "einheit" => "Einheit: " . $_GET['einheit'],
            "ziel" => "Adresse: " . $_GET['ziel'],
            "keyword" => "Stichwort: #" . $_GET['keyword'],
            "keywordDescription" => "Beschreibung: " . $_GET['keywordDescription'],
        ];

        //Text imploden für Telegram
        $text = implode("\n", $EinsatzInfos);

        //An mich senden
        //telegramApiPresse('892584383', $PresseBotID, 0, $text);

        //An Alle senden
        telegramApiPresse($PresseChatID, $PresseBotID, $threadIDAlle, $text);


        //In die Threads versenden
        if (stristr($EinsatzInfos['einheit'], 'Geilenkausen')) {
            $threadID = $threadIDGeilenkausen;
        } elseif (stristr($EinsatzInfos['einheit'], 'Waldbröl')) {
            $threadID = $threadIDWaldbroel;
        } elseif (stristr($EinsatzInfos['einheit'], 'Thierseifen')) {
            $threadID = $threadIDThierseifen;
        } elseif (stristr($EinsatzInfos['einheit'], 'Heide')) {
            $threadID = $threadIDHeide;
        } elseif (stristr($EinsatzInfos['einheit'], 'Wehrführer')) {
            $threadID = $threadIDWehrfuehrer;
        } elseif (stristr($EinsatzInfos['einheit'], 'Hauptamtliche Gerätewarte')) {
            $threadID = $threadIDHauptamtliche;
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
