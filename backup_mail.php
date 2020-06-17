
<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

require_once('config/config.php');

$today = date("Ymd");

$file = "/home/turfgras/public_html/backup/db_backup_".$today.".sql";

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'backup.turfgrass@gmail.com';                 // SMTP username
    $mail->Password = 'n2z-e?O(KmQ]';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('backup@turfgrass.site', 'Backup');
    $mail->addAddress('tsite@protonmail.com');     // Add a recipient

    //Content
    $mail->Subject = 'MySQL backup';
	$mail->Body      = "Backup - ".$today;

	$mail->AddAttachment($file);

    $mail->send();

    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}

?>
 