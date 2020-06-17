<?php
///////////////////////////////////////
// Create delivery note for projects //
///////////////////////////////////////

include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

////////////
// enter delivery note data into database
if (isset($_POST['deliveryNote'])) {

	$truck_id = $_POST['truck_id'];
	$datum = $_POST['datum'];
	$customer_name = $_POST['customer_name'];
	$customer_street = $_POST['customer_street'];
	$customer_plz = $_POST['customer_plz'];
	$customer_city = $_POST['customer_city'];
	$country_disp = $_POST['country_disp'];
	$country_disp_en = $_POST['country_disp_en'];
	$telephone = $_POST['telephone'];
	$deliveryaddress = $_POST['deliveryaddress'];
	$deliverytime = $_POST['deliverytime'];
	$sort = $_POST['sort'];
	$licence1 = $_POST['licence1'];
	$licence2 = $_POST['licence2'];
	$amount = $_POST['amount'];
	$pipes = $_POST['pipes'];
	$length = $_POST['length'];
	$cooling = $_POST['cooling'];
	$showtime = $_POST['showtime'];
	$companyRadios = $_POST['companyRadios'];
	$langRadios = $_POST['langRadios'];

	$time = date("G:i");

	if (isset($_POST['pallets'])) {
		$pallets = $_POST['pallets'];
	}
	else {
		$pallets = 0;
	}

	if ($_POST['zurrgurt'] == 1) {
		$zurrgurt = 1;
	} else {
		$zurrgurt = 0;
	}

	if ($_POST['showtime'] == 1) {
		$showtime = 1;
	} else {
		$showtime = 0;
	}

	if (empty($_POST['ekaer'])) {
		$ekaer = 1111;
		$ekaer_check = 0;
	} else {
		$ekaer = $_POST['ekaer'];
		$ekaer_check = 1;
	}



	if (isset($_SESSION['userid'])) {
		$creator = $_SESSION['userid'];
	}
	elseif (isset($_COOKIE['userid'])) {
		$creator = $_COOKIE["userid"];
	}
	else {
		$creator = 0;
	}

	$created = date("Y-m-d H:i:s");

	// Insert delivernote and settings into database
	$query = $db->prepare("INSERT INTO `deliverynotes` (`id`, `ekaer`, `lang`, `company`, `zurrgurt`, `showtime`, `created`, `creator`) VALUES (NULL, :ekaer, :lang, :company, :zurrgurt, :showtime, :created, :creator)");
	
	$query->bindParam(":ekaer", $ekaer, PDO::PARAM_STR);
	$query->bindParam(":lang", $langRadios, PDO::PARAM_STR);
	$query->bindParam(":company", $companyRadios, PDO::PARAM_STR);
	$query->bindParam(":zurrgurt", $zurrgurt, PDO::PARAM_STR);
	$query->bindParam(":showtime", $showtime, PDO::PARAM_STR);
	$query->bindParam(":created", $created, PDO::PARAM_STR);
	$query->bindParam(":creator", $creator, PDO::PARAM_STR);
	$query->execute(); 

	
	// Get id of deliverynote
	$query = $db->prepare("SELECT * FROM `deliverynotes` ORDER BY `id` DESC LIMIT 1");
	$query->execute(); 
	$result = $query->fetch(PDO::FETCH_OBJ);
	$deliverynote = $result->id;

	// Update truck data
	$sql = "UPDATE `trucks` SET `licence1` = :licence1, `licence2` = :licence2, `deliverynote` = :deliverynote, `amount` = :amount, `pipes` = :pipes, 
			`pallets` = :pallets, `deliverytime` = :deliverytime WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":licence1", $licence1, PDO::PARAM_STR);
	$query->bindParam(":licence2", $licence2, PDO::PARAM_STR);
	$query->bindParam(":deliverynote", $deliverynote, PDO::PARAM_STR);
	$query->bindParam(":amount", $amount, PDO::PARAM_STR);
	$query->bindParam(":pipes", $pipes, PDO::PARAM_STR);
	$query->bindParam(":pallets", $pallets, PDO::PARAM_STR);
	$query->bindParam(":deliverytime", $deliverytime, PDO::PARAM_STR);
	$query->bindParam(":id", $truck_id, PDO::PARAM_STR);

	$query->execute();


	$type1 = $_POST['type1'];
	if ($type1 == 1) {
		if ($langRadios == 1) {
			$type1_display = "Kistekercs Készgyep (";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Fertigrasen Kleinrollen (";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Turfgrass small rolls (";
		}
		
	}
	elseif ($type1 == 2) {
	 	if ($langRadios == 1) {
			$type1_display = "Kistekercs Készgyep (";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Fertigrasen Kleinrollen (";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Turfgrass small rolls (";
		}
	}
	elseif ($type1 == 3) {
		if ($langRadios == 1) {
			$type1_display = "Kistekercs Készgyep (Dick. ";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Fertigrasen Kleinrollen (Dick. ";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Turfgrass small rolls (thick ";
		}
	} 
	elseif ($type1 == 4) {
		if ($langRadios == 1) {
			$type1_display = "Nagytekercs Készgyep (";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Großrollen Sportrasen (";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Big rolls turfgrass (";
		}
	} 
	elseif ($type1 == 5) {
	 	if ($langRadios == 1) {
			$type1_display = "Nagytekercs Készgyep (Dick. ";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Großrollen Sportrasen (Dick. ";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Big rolls turfgrass (thick ";
		}
	} 
	elseif ($type1 == 6) {
	 	if ($langRadios == 1) {
			$type1_display = "Nagytekercs Készgyep (Dick. ";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Großrollen Sportrasen (Dick. ";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Big rolls turfgrass (thick ";
		}
	} 

	if ($langRadios == 1) {
		$desc_display = "Feketecső";
		$desc2_display = "Raklap";
		$unit = "db.";
	}
	elseif ($langRadios == 2) {
		$desc_display = "Kernrohre";
		$desc2_display = "Palette";
		$unit = "Stk.";
	}
	elseif ($langRadios == 3) {
		$desc_display = "Pipes ";
		$desc2_display = "Pallets";
		$unit = "pcs.";
	}

	$description1 = $type1_display."".$length." m)";
	$darab = $pipes." ".$unit;
	$pallets_darab = $pallets." ".$unit;

	$today = date("Y-m-d");

	
	$rechnungs_datum = $datum;
	$pdfAuthor = "Pannon Turfgrass";
	 
	$rechnungs_header = '';
	//$rechnungs_header = '
	//<img src="img/pannon.png" style="width:250px">';

	if ($langRadios == 1) {
		$country = $country_disp;
	}
	else {
		$country = $country_disp_en;
	}
	 
	$rechnungs_empfaenger = $customer_name."<br><br>".
	$customer_street."<br>".
	$customer_plz." ".$customer_city."<br>".
	$country."<br><br>".
	$telephone."<br>";
	 
	$rechnungs_footer = '<img src="img/footer_hu.png" style="width:300px">';
	 
	//Auflistung eurer verschiedenen Posten im Format [Produktbezeichnung, Menge]
	$rechnungs_posten = array(
	 array($description1, $darab),
	 array($desc_display, $darab)
	 );

	if ($pallets > 0) {
		array_push($rechnungs_posten, array($desc2_display, $pallets_darab));
	}
	 
	 
	$pdfName = "Szallitolevel.pdf";
	 
	 
	//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	 
	 
	// Erstellung des HTML-Codes. Dieser HTML-Code definiert das Aussehen eures PDFs.
	// tcpdf unterstützt recht viele HTML-Befehle. Die Nutzung von CSS ist allerdings
	// stark eingeschränkt.
	 
	$html = '
	<table cellpadding="5" cellspacing="0" style="width: 100%; ">
	 <tr>
	 <td>'.nl2br(trim($rechnungs_header)).'</td>
	    <td style="text-align: right">
	    <img src="img/logo.png" style="width:150px">
	 </td>
	 </tr>

	 <tr>
	 <td>'.nl2br(trim($rechnungs_empfaenger)).'</td>
	 <td style="text-align: right"><br><br><br>
	   '.$datum.'
	 </td>
	 </tr>
	 
	 <tr>
	 <td style="font-size:1.3em; font-weight: bold;">
	<br><br><br><br>';

	if ($langRadios == 1) {
		$html .= "Szállítólevél";
	}
	elseif ($langRadios == 2) {
		$html .= "Lieferschein";
	}
	elseif ($langRadios == 3) {
		$html .= "Delivery Note";
	}
	
	
	$html .= '<br>
	 </td>
	 </tr>
	 
	 
	 
	</table>
	<br><br>
	 
	<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
	 <tr style="background-color: #cccccc; padding:5px;">';

	if ($langRadios == 1) {
		$html .= '<td style="padding:5px;"><b>Termék</b></td>
	 		<td style="text-align: center;"><b>Mennyiség</b></td>';

	 	$desc3 = "Felrakodás";
	 	$desc4 = "Szállítási cím";
	 	$desc5 = "EKAÉR szám";
	 	$desc6 = "Szállítási időpont";
	 	$desc7 = "Az áru nem köthető le.";
	 	$desc8 = "Köszönjük a megrendelést!";
	 	$desc9 = "Időpont";
	 	$desc10 = "Rendszám";
	 	$desc11 = "Aláírás";
	}
	elseif ($langRadios == 2) {
		$html .= '<td style="padding:5px;"><b>Produkt</b></td>
	 		<td style="text-align: center;"><b>Menge</b></td>';

	 	$desc3 = "Abholort";
	 	$desc4 = "Lieferadresse";
	 	$desc5 = "EKAÉR-Nummer";
	 	$desc6 = "Lieferzeitpunkt";
	 	$desc7 = "Die Ware (Fertigrasen) darf nicht mit Spanngurten festgezurrt werden.";
	 	$desc8 = "Wir danken für den Auftrag!";
	 	$desc9 = "Ladezeitpunkt";
	 	$desc10 = "LKW - Kennzeichen";
	 	$desc11 = "Unterschrift";
	}
	elseif ($langRadios == 3) {
		$html .= '<td style="padding:5px;"><b>Product</b></td>
	 		<td style="text-align: center;"><b>Quantity</b></td>';

	 	$desc3 = "Loading place";
	 	$desc4 = "Delivery address";
	 	$desc5 = "EKAÉR number";
	 	$desc6 = "Delivery time";
	 	$desc7 = "The goods (turfgrass) must not be lashed down with lashing straps.";
	 	$desc8 = "Thanks for your business!";
	 	$desc9 = "Pick-up time";
	 	$desc10 = "truck - Licence plate";
	 	$desc11 = "Signature";
	}
	
	 
	 
	 $html .= '</tr>';
	 
	 
	$gesamtpreis = 0;
	 
	foreach($rechnungs_posten as $posten) {
	 $menge = $posten[1];

	 $html .= '<tr>
	                <td>'.$posten[0].'</td>
	 <td style="text-align: center;">'.$posten[1].'</td> 
	              </tr>';
	}
	$html .='</table><br><br><br><br>';
	 
	$html .= $desc3.": &nbsp; &nbsp; &nbsp;H-2363 Felsőpakony, Hétsor út 5.<br><br>";

	$html .= $desc4.": &nbsp; ".$deliveryaddress."<br><br><br>";

	$html .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
	
	if ($ekaer_check == 1) {
		$html .= "<b>".$desc5.": &nbsp; ".$ekaer."</b><br><br>";
	}
	else {
		$html .= "<br><br>";
	}

	


	if ($deliverytime == "00:00") {
		$html .= "<br><br><br>";
	}
	else {
		$html .= '<br>'.$desc6.': '.$deliverytime."<br><br>";
	}

	if ($zurrgurt == 1) {
		$html .= $desc7."<br>";
	}
	else {
		$html .= "<br>";
	}

	$html .= $desc8."<br><br>";

	if ($cooling == 1) {
		$html .= "<br><b>Temp. + 2&deg;C";

		if ($showtime == 1) {
			$html .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ".$desc9.": &nbsp; ".$time."</b>";
		}
		$html .= "<br><br>";
	}
	elseif ($showtime == 1) {
		$html .= "<br><b>".$desc9.": ".$time."</b><br><br>";
	}
	else {
		$html .= "<br><br><br>";
	}


	$html .= '<br><b>'.$sort.'.</b> '.$desc10.': '.$licence1." // ".$licence2."<br><br><br><br>";

	$html .= $desc11.':<br><br><br><br><br><br><br>';

	if ($companyRadios == 1) {
		$html .= '<img src="img/footer_hu.jpg">';
	}
	elseif ($companyRadios == 2) {
		$html .= '<img src="img/footer_at.jpg">';
	}

	
	 
	//$html .= nl2br($rechnungs_footer);
	 
	 
	 
	//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	 
	// TCPDF Library laden
	require_once('tcpdf/tcpdf.php');
	 
	// Erstellung des PDF Dokuments
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	 
	// Dokumenteninformationen
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($pdfAuthor);
	$pdf->SetTitle('Szallitolevel ');
	$pdf->SetSubject('Szallitolevel');
	
	// remove default header/footer
	//$pdf->setPrintHeader(false);
	//$pdf->setPrintFooter(false);
	 
	// Header und Footer Informationen
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	 
	// Auswahl des Font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	 
	// Auswahl der MArgins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	 
	// Automatisches Autobreak der Seiten
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	 
	// Image Scale 
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	 
	// Schriftart
	$pdf->SetFont('dejavusans', '', 10);
	 
	// Neue Seite
	$pdf->AddPage();
	 
	// Fügt den HTML Code in das PDF Dokument ein
	$pdf->writeHTML($html, true, false, true, false, '');
	 
	//Ausgabe der PDF
	 
	//Variante 1: PDF direkt an den Benutzer senden:
	$pdf->Output($pdfName, 'I');
	 
	//Variante 2: PDF im Verzeichnis abspeichern:
	//$pdf->Output(dirname(__FILE__).'/'.$pdfName, 'F');
	//echo 'PDF herunterladen: <a href="'.$pdfName.'">'.$pdfName.'</a>';
	
}
else {
	echo "Nem sikerült.";
}
?>