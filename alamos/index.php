<?php
require_once 'funktionen.php';
require_once 'config.php';

/*
Automatische Toröffnung über Alamos.
*/


//Wenn API Key per POST kommt
if (isset($_POST['apikey']))
{
    //Wenn API Key der vom Tor ist
    if ($_POST['apikey'] == $ValidApiKeyTor)
    {
        //Pruefen ob ein Alarm vorliegt
        $alarmierung = CheckAlarmierung();

        if ($alarmierung)
        {
            //Abfrage in die Datenbank loggen
            abfrageLoggen("OpenGate");

            //Nachricht in die Presse Gruppe versenden.
            telegramApiPresse($PresseChatID, $PresseBotID, $threadIDGeilenkausen, "Das Garagentor in Geilenkausen wurde durch eine Alarmierung geöffnet.");
            //Mit "Alarm" wird das Tor geoeffnet! (Niemals aendern oder einfach echo'n)
            echo "Alarm";
        }
        else
        {
            //Abfrage in die Datenbank loggen
            abfrageLoggen("No Alarm");

            //Hier ist die Ausgage egal
            echo "Kein Einsatz";
            deleteOldEntrys();
        }
    }
}
elseif (isset($_GET['apikey']))
    if($_GET['apikey'] == $ValidApiKeyAlamos)
    { // Wenn Alamos ApiKey
        //Neuen Einsatz in die DB loggen
        neuenEinsatzEintragen($_GET['txt']);
    }else{

    }
else
{
    //Wenn letzte Abfrage vom Microcontroller lange her ist Telegram Nachricht
    $lastQuery = letzteAbfrage();

    //Wenn seit 35 Minuten keine Abfrage
    if (strtotime($lastQuery) < strtotime('-35 minutes'))
    {
        #echo "Seit einer Minute keine Abfrage!";
        echo "Letzte Abfrage: " . $lastQuery;
        telegramApi($AdminChatID, $TorBotID, "Microcontroller ausgefallen!\nLetzte Abfrage erfolgte: $lastQuery");
    }
    else
    {
        #echo "Letzte Abfrage: " . $lastQuery;
        exit;
    }
}