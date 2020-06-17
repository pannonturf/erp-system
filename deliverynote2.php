<?php
////////////////////////////////////////////
// Create delivery note for normal orders //
////////////////////////////////////////////

include('tools/functions.php');

require_once('config/config.php');
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASS);
$db -> exec("set names utf8");
date_default_timezone_set('Europe/Budapest');

$days_short = array("V", "H", "K", "S", "C", "P", "S");

// Get modus
$query = $db->prepare("SELECT * FROM `system` WHERE `id` = 2");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$modus = $result->active;

// Get cutting modus
$query = $db->prepare("SELECT * FROM `system` WHERE `id` = 3");
$query->execute(); 
$result = $query->fetch(PDO::FETCH_OBJ);
$cutting_modus = $result->active;

////////////
// enter delivery note data into database
if (isset($_POST['deliveryNote'])) {

	$order_id = $_POST['order_id'];
	$deliverynote = $_POST['deliverynote'];
	$datum = $_POST['datum'];
	$amount = $_POST['amount'];
	$customer = $_POST['customer'];
	$customer_name = $_POST['customer_name'];
	$customer_street = $_POST['customer_street'];
	$customer_plz = $_POST['customer_plz'];
	$customer_city = $_POST['customer_city'];
	$customer_phone = $_POST['customer_phone'];
	$contactperson = $_POST['contactperson'];
	$country_disp = $_POST['country_disp'];
	$country_disp_en = $_POST['country_disp_en'];
	$country = $_POST['country1'];
	$note = $_POST['note'];

	$city_id = 0;
	$city_id = $_POST['city_id'];
	$plz = $_POST['plz'];
	$deliverytime = substr($_POST['deliverytime'], 0, 5);
	$type1 = $_POST['type1'];
	$type2 = $_POST['type2'];
	$type3 = $_POST['type3'];
	$pipes = $_POST['pipes'];

	$prefix = $_POST['prefix'];
	$id2 = $_POST['id2'];
	$id3 = $_POST['id3'];
	$showtime = $_POST['showtime'];
	$companyRadios = $_POST['companyRadios'];
	$langRadios = $_POST['langRadios'];

	$time = date("G:i");

	if (!empty($_POST['deliveryaddress'])) {
		$deliveryaddress = $_POST['deliveryaddress'];
	}
	else {
		$deliveryaddress = "";
  	}

  	if (!empty($_POST['licence'])) {
		$licence = $_POST['licence'];
	}
	else {
		$licence = "";
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

	if ($country == 0 AND $city_id > 0) {
		$query = $db->prepare("SELECT * FROM cities WHERE `id` = :id");
        $query->bindParam(":id", $city_id, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
		$city_disp = $result->name;
		$plz = $result->plz;

		$deliveryaddress_disp = $plz." ".$city_disp.", ".$deliveryaddress;
	}
	else {
		if ($deliveryaddress != "") {
			$deliveryaddress_disp = $deliveryaddress;
		}
		else {
		 	$deliveryaddress_disp = "-";
		} 
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

	// Check, if deliverynote already exists
	if ($deliverynote == 0) {
		// Insert delivernote and settings into database
		$query = $db->prepare("INSERT INTO `deliverynotes` (`id`, `ekaer`, `lang`, `company`, `zurrgurt`, `showtime`, `note`, `created`, `creator`) VALUES (NULL, :ekaer, :lang, :company, :zurrgurt, :showtime, :note, :created, :creator)");
		
		$query->bindParam(":ekaer", $ekaer, PDO::PARAM_STR);
		$query->bindParam(":lang", $langRadios, PDO::PARAM_STR);
		$query->bindParam(":company", $companyRadios, PDO::PARAM_STR);
		$query->bindParam(":zurrgurt", $zurrgurt, PDO::PARAM_STR);
		$query->bindParam(":showtime", $showtime, PDO::PARAM_STR);
		$query->bindParam(":note", $note, PDO::PARAM_STR);
		$query->bindParam(":created", $created, PDO::PARAM_STR);
		$query->bindParam(":creator", $creator, PDO::PARAM_STR);
		$query->execute(); 

		
		// Get id of deliverynote
		$query = $db->prepare("SELECT * FROM `deliverynotes` ORDER BY `id` DESC LIMIT 1");
		$query->execute(); 
		$result = $query->fetch(PDO::FETCH_OBJ);
		$deliverynote = $result->id;
	}
	else {
		// Update deliverynote and settings into database
		$query = $db->prepare("UPDATE `deliverynotes` SET `ekaer` = :ekaer, `lang` = :lang, `company` = :company, `zurrgurt` = :zurrgurt, `showtime` = :showtime, `note` = :note WHERE `id` = :id");
		
		$query->bindParam(":ekaer", $ekaer, PDO::PARAM_STR);
		$query->bindParam(":lang", $langRadios, PDO::PARAM_STR);
		$query->bindParam(":company", $companyRadios, PDO::PARAM_STR);
		$query->bindParam(":zurrgurt", $zurrgurt, PDO::PARAM_STR);
		$query->bindParam(":showtime", $showtime, PDO::PARAM_STR);
		$query->bindParam(":note", $note, PDO::PARAM_STR);
		$query->bindParam(":id", $deliverynote, PDO::PARAM_STR);
		$query->execute(); 
	}


	// check, if sorszam is already assigned
	if (($id2 == 0 AND $id3 == 0) AND $type1 < 4) {
		if ($cutting_modus == 1) {
			//get last id2 of the day
			$query = $db->prepare("SELECT * FROM `order` WHERE `date` = :datum AND `id2` > 0 AND `type1` < 4 ORDER BY `id2` DESC LIMIT 1");
			$query->bindParam(":datum", $datum, PDO::PARAM_STR);
			$query->execute(); 
			
			if ($query->rowCount() > 0) {
				$result = $query->fetch(PDO::FETCH_OBJ);
				$current_id2 = $result->id2;
				$id2 = $current_id2 + 1;
			}
			else {
				$id2 = 1;
			}

			$day1 = date('w', strtotime($datum));
			$prefix = $days_short[$day1];
			$id3 = 0;
		}
		elseif ($cutting_modus == 2) {
			// get last assigned id3
			$query = $db->prepare("SELECT * FROM `order` WHERE `id3` > 0 AND `type1` < 4 ORDER BY `id3` DESC LIMIT 1");
			$query->execute(); 

			$result = $query->fetch(PDO::FETCH_OBJ);
			$current_id3 = $result->id3;
			$id3 = $current_id3 + 1;
			$id2 = 0;
			$prefix = "";
		}
	}

	$id3_display = substr($id3, -2);
	if ($id3_display == "00") {
		$id3_display = 100;
	}

	// Update order data
	$sql = "UPDATE `order` SET `pipes` = :pipes, `deliveryaddress` = :deliveryaddress, `city` = :city, `country` = :country, `deliverynote` = :deliverynote, 
		`licence` = :licence, `id2` = :id2, `id3` = :id3, `prefix` = :prefix WHERE `id` = :id";
	$query = $db->prepare($sql);

	$query->bindParam(":pipes", $pipes, PDO::PARAM_STR);
	$query->bindParam(":deliveryaddress", $deliveryaddress, PDO::PARAM_STR);
	$query->bindParam(":city", $city_id, PDO::PARAM_STR);
	$query->bindParam(":country", $country, PDO::PARAM_STR);
	$query->bindParam(":deliverynote", $deliverynote, PDO::PARAM_STR);
	$query->bindParam(":licence", $licence, PDO::PARAM_STR);
	$query->bindParam(":id2", $id2, PDO::PARAM_STR); 
	$query->bindParam(":id3", $id3, PDO::PARAM_STR); 
	$query->bindParam(":prefix", $prefix, PDO::PARAM_STR);
	$query->bindParam(":id", $order_id, PDO::PARAM_STR);

	$query->execute();


	// Check if customer data was edited -> checkdata = 0

	// Customer data
	$query = $db->prepare("SELECT * FROM customers WHERE `id` = :id");
	$query->bindParam(":id", $customer, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$customer_name_old = $result->name;
	$contactperson_old = $result->contactperson;
	$customer_street_old = $result->street;
	$customer_plz_old = $result->plz;
	$customer_city_old = $result->city;
	$telephone_old = $result->phone;
	$checkdata_old = $result->checkdata;

	if ($customer_name_old != $customer_name OR $contactperson_old != $contactperson OR $customer_street_old != $customer_street OR $customer_plz_old != $customer_plz OR $customer_city_old != $customer_city OR $telephone_old != $customer_phone) {
		$checkdata = 0;
	}
	else {
		$checkdata = $checkdata_old;
	}

	// Update customer data
	$query = $db->prepare("UPDATE `customers` SET `name` = :name, `contactperson` = :contactperson, `street` = :street, `plz` = :plz, `city` = :city, `phone` = :phone, `checkdata` = :checkdata WHERE `id` = :id");
		
	$query->bindParam(":name", $customer_name, PDO::PARAM_STR);
	$query->bindParam(":contactperson", $contactperson, PDO::PARAM_STR);
	$query->bindParam(":street", $customer_street, PDO::PARAM_STR);
	$query->bindParam(":plz", $customer_plz, PDO::PARAM_STR);
	$query->bindParam(":city", $customer_city, PDO::PARAM_STR);
	$query->bindParam(":phone", $customer_phone, PDO::PARAM_STR);
	$query->bindParam(":checkdata", $checkdata, PDO::PARAM_STR);
	$query->bindParam(":id", $customer, PDO::PARAM_STR);
	$query->execute();


	if ($type1 == 1 OR $type1 == 2) {
		if ($langRadios == 1) {
			$type1_display = "Készgyep kistekercs";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Fertigrasen Kleinrollen";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Turfgrass small rolls";
		}
		
	}
	elseif ($type1 == 3) {
		if ($langRadios == 1) {
			$type1_display = "Készgyep kistekercs - vastag";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Fertigrasen Kleinrollen - Dicksode";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Turfgrass small rolls - thick sod";
		}
	} 
	elseif ($type1 == 4) {
		if ($langRadios == 1) {
			$type1_display = "Készgyep nagytekercs";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Großrollen Sportrasen";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Big rolls turfgrass";
		}
	} 
	elseif ($type1 == 5 OR $type1 == 6) {
	 	if ($langRadios == 1) {
			$type1_display = "Készgyep nagytekercs - vastag";
		}
		elseif ($langRadios == 2) {
			$type1_display = "Großrollen Sportrasen - Dicksode";
		}
		elseif ($langRadios == 3) {
			$type1_display = "Turfgrass big rolls - thick sod";
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

	$darab = $pipes." ".$unit;
	$pallets_darab = $pallets." ".$unit;

	$amount = getAmount2($amount, $type3, $modus);

	if ($type1 < 4) {
		$description1 = $type1_display;
		$amount_disp = $amount." m&sup2;";
	}
	else {
		$description1 = $type1_display;
		$amount_disp = $darab;
	}

	$today = date("Y-m-d");

	
	$rechnungs_datum = $datum;
	$pdfAuthor = "Pannon Turfgrass";
	 
	$rechnungs_header = '';
	//$rechnungs_header = '
	//<img src="img/pannon.png" style="width:250px">';

	
	if ($langRadios == 1) {
		$rechnungs_empfaenger = "<b>".$customer_name."</b><br>".
		$customer_city."<br>".
		$customer_street."<br>".
		$customer_plz."<br><br>".
		$contactperson."<br>".
		$customer_phone."<br>";
	}
	else {
		$rechnungs_empfaenger = "<b>".$customer_name."</b><br>".
		$customer_street."<br>".
		$customer_plz." ".$customer_city."<br>".
		$country_disp_en."<br><br>".
		$contactperson."<br>".
		$customer_phone."<br>";
	}
	
	 
	$rechnungs_footer = '<img src="img/footer_hu.png" style="width:300px">';
	 
	//Auflistung eurer verschiedenen Posten im Format [Produktbezeichnung, Menge]
	$rechnungs_posten = array(
	 array($description1, $amount_disp)
	 );

	if ($type1 > 3) {
		array_push($rechnungs_posten, array($desc_display, $darab));
	}

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
	 <td style="text-align: right"><br><br><br><br><br><br>
	   '.$datum.'
	 </td>
	 </tr>
	 
	 <tr>
	 <td style="font-size:1.3em; font-weight: bold;">
	<br><br><br><br><br><br>';

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
	 	$desc6 = "Rendelési időpont";
	 	$desc7 = "Az áru nem köthető le.";
	 	$desc8 = "Köszönjük a megrendelést!";
	 	$desc9 = "Időpont";
	 	$desc10 = "Rendszám";
	 	$desc11 = "Aláírás";
	 	$desc12 = "Sorszám";
	}
	elseif ($langRadios == 2) {
		$html .= '<td style="padding:5px;"><b>Produkt</b></td>
	 		<td style="text-align: center;"><b>Menge</b></td>';

	 	$desc3 = "Abholort";
	 	$desc4 = "Lieferadresse";
	 	$desc5 = "EKAÉR-Nummer";
	 	$desc6 = "Bestellzeitpunkt";
	 	$desc7 = "Die Ware (Fertigrasen) darf nicht mit Spanngurten festgezurrt werden.";
	 	$desc8 = "Wir danken für den Auftrag!";
	 	$desc9 = "Ladezeitpunkt";
	 	$desc10 = "LKW - Kennzeichen";
	 	$desc11 = "Unterschrift";
	 	$desc12 = "Abhol-Nummer";
	}
	elseif ($langRadios == 3) {
		$html .= '<td style="padding:5px;"><b>Product</b></td>
	 		<td style="text-align: center;"><b>Quantity</b></td>';

	 	$desc3 = "Loading place";
	 	$desc4 = "Delivery address";
	 	$desc5 = "EKAÉR number";
	 	$desc6 = "Time ordered";
	 	$desc7 = "The goods (turfgrass) must not be lashed down with lashing straps.";
	 	$desc8 = "Thanks for your business!";
	 	$desc9 = "Pick-up time";
	 	$desc10 = "Truck - Licence plate";
	 	$desc11 = "Signature";
	 	$desc12 = "Pick-up number";
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
	$html .='</table><br><br><br><br><br>';
	 
	$html .= $desc3.": &nbsp; &nbsp; &nbsp;H-2363 Felsőpakony, Hétsor út 5.<br><br>";

	$html .= $desc4.": &nbsp; ".$deliveryaddress_disp."<br><br><br>";

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

	if ($type1 < 4) {
		if ($cutting_modus == 1) {
			$html .= $desc12.': <b>'.$prefix."-".$id2."</b><br><br>";
		}
		elseif ($cutting_modus == 2) {
			$html .= $desc12.': <b>'.$id3_display."</b><br><br>";
		}
	}
	else {
		$html .= '<br><br>';
	}

	if ($licence != "") {
		$html .= $desc10.': '.$licence."<br><br>";
	}
	else {
		$html .= '<br><br>';
	}

	$html .= $note."<br><br>";

	$html .= $desc8."<br><br>";

	if ($type1 < 3) {
		$html .= "<br><br>";
	}

	$html .= $desc11.':<br><br><br>';

	if (strlen($customer_street) < 46) {
		$html .= "<br>";
	}

	if ($pallets == 0) {
		$html .= "<br><br>";
	}

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