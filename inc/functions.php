<?php
    function getEmails($secretSantas) {
        $emails = [];

        foreach ($secretSantas as $secretSanta) {
            array_push($emails, $secretSanta->email);
        }

        return $emails;
    }

	function unset_array($array, $index){
		$array = $array;
        $outputArray = array();
		$index = $index;

		//Schleife bis zur Anzahl -1 der beinhaltenden Elemente durchgehen, da ein Element rausgelöscht wird.
		for($i=0; $i<count($array)-1; $i++){
			// wenn der Index < $i ist speichere an die derzeitige Stelle das darauffolgende Element.
			// Damit wird der Wert, der gelöscht werden soll einfach übergangen bzw. überspeichert.
			if($index <= $i){
				$outputArray[$i] = $array[$i+1];
			}
			//Solange jedoch der Index nicht 0 und noch nicht <= $i ist, soll der Wert der i-ten
			//Stelle an die i-te Stelle des neuen Arrays gespeichert werden.
			else if($index != 0){
				$outputArray[$i] = $array[$i];
			}
			//Ist Index = 0 speichere den Wert der 0-ten Stelle an die -$i-te Stelle
			//else $outputArray[$i-1] = $array[$i];
		} return($outputArray);
	}

	function sicherungskopie($array){
		$text = "";
		$dateiname = uniqid().".txt"; // Name der Datei
		$backupsrc = 'backupfiles/';
		// Datei öffnen,
		// wenn nicht vorhanden dann wird die Datei erstellt.
		$handler = fOpen($backupsrc.$dateiname , "a+");
		// Dateiinhalt in die Datei schreiben
		$text .= "---------------Start of Wichtels------------------ \n";
		for($i=0;$i<count($array);$i++){
			$text .= 'Wichtel: ' . $array[$i]['name'] . ' ---- eMail: ' . utf8_decode($array[$i]['email']) . "\n";
		}
		$text .= "----------------End of Wichtels------------------";
		fWrite($handler , $text);
		fClose($handler); // Datei schließen

		return $backupsrc.$dateiname;
	}
?>