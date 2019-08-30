<?php
/*function check_ean13($code){

	$error = '';
	if (strlen($code) != 13)
		$error.="Код не из 13 символов<br>";
	else
	{
		$c1=($code[1]+$code[3]+$code[5]+$code[7]+$code[9]+$code[11])*3;
		$c2=$code[0]+$code[2]+$code[4]+$code[6]+$code[8]+$code[10];
		$c3=($c1+$c2)%10;
		if (((10-$c3)%10) != $code[12]) $error.='Проверьте номер дисконтной карты, какая-то цифра неправильная.<br>';
	}
	if (empty($error)){
		return true;
	} else {
		return false;
	}
}

$code = "9917060013684";

	if ( check_ean13($code) === true ){
		echo $code."\n";
	} else {
		echo "bad";
	}

die;*/
// for ($i=0; $i < 999999999; $i++) { 
// 	$nine_digit = sprintf("%09d",$i++);

// 	$code = "9911".$nine_digit;

// 	if ( check_ean13($code) === true ){
// 		echo $code."\n";
// 	}

// }



// echo strtotime("12:00:00");

// echo date("Y-m-d H:i",3016151075);

// echo time();
// echo '1508136240';
// include('../admin/classes/DBclass.php');
// die("not now");
require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/fe_func.php';
// require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/phpmailer/PHPMailerAutoload.php';

$DB = new c_database();
$DB->iniSet();
$DB->connect();


$codes = array( 9917060012588,9917060012601,9917060030254,9917060030261,9917060030391,9917060030445,9917060030483,9917060030957,9917060030230,9917060030827,9917060030834,9917060030841,9917060030858 );


foreach ($codes as $code) {
	if ( check_ean13($code) ) {
		$DB->query_exec("INSERT INTO certs (`code`, `cert_type`) VALUES ('{$code}', 4)");
		// generate_barcode($code);
		// $cert_img = generate_cert_img($code);
	}
}

// $code = "9917060023171";
// generate_barcode($code);
// $cert_img = generate_cert_img($code);

echo "done\n";

/*$DB->query_exec("SELECT C.code, C.u_id, C.cert_type, U.name, U.email, U.social_page, U.birthday
					FROM certs C
					LEFT JOIN users U ON C.u_id = U.id
					WHERE u_id NOT IN (
					SELECT id
					FROM users
					WHERE id > 4500 AND user_level >= 8) AND cert_type = 2 AND u_id != 2666 AND u_id != 0 AND u_id != 310");*/
				
	// $DB->query_exec("SELECT C.code, C.u_id, C.cert_type, U.name, U.email, U.social_page, U.birthday
	// 				FROM certs C
	// 				LEFT JOIN users U ON C.u_id = U.id
	// 				WHERE u_id IN ( 3923, 1646, 3641, 1766, 1556, 1781, 759, 738, 2252, 2174, 396, 112, 127, 175, 1670 ) AND cert_type = 2");

	// $bedniy_user = $DB->fetch();

	// echo count($bedniy_user)."<br><br><br><br><br><br>";

	// foreach ($bedniy_user as $key => $value) {
	// 	/*echo $value['u_id'];
	// 	echo $value['cert_type'];
	// 	echo "<br><br>";*/

	// 	$DB->query_exec("UPDATE certs SET u_id={$value['u_id']} WHERE cert_type = 2 AND u_id = 0 limit 1");


	// }
	
// $mail = new PHPMailer;

// $mail->CharSet    = 'UTF-8';
// $mail->isSMTP();                             	// Set mailer to use SMTP
// $mail->Host       = 'smtp.timeweb.ru';         	// Specify main and backup SMTP servers
// $mail->SMTPSecure = 'ssl';
// $mail->SMTPAuth   = true;                      	// Enable SMTP authentication
// $mail->Port       = 465;                      	// TCP port to connect to	

// $mail->Username   = 'info@adventurefamil.ru';         	// SMTP username
// $mail->Password   = 'i2h9XAbk';                	// SMTP password

// $mail->setFrom('info@adventurefamil.ru', 'adventurefamil');	// от кого в заголовке письма
// $mail->isHTML(true);                         	// Set email format to HTML	
// $mail->Subject = 'Ваш сертификат на покупки в Familia!';


// $DB->query_exec("SELECT C.code, C.cert_type, U.name, U.email
// 				FROM certs C
// 				LEFT JOIN users U ON C.u_id = U.id
// 				WHERE C.cert_type in (2,3,4) AND C.u_date BETWEEN '2017-10-19 19:20:52' AND '2017-10-19 20:23:33' AND C.u_id != 0 AND U.email = ''");

// $bedniy_user = $DB->fetch();

// // $bedniy_user = array('reloved@gmail.com','streetadventuremsk@gmail.com');
// $i = 0;
// foreach ($bedniy_user as $user) {
// $i++;
// 	$mail->ClearAllRecipients( ); // clear all
// 	$mail->clearAttachments();

	// $cert_no = '9917060013684';

	// generate_barcode($cert_no);
	// $cert_img = generate_cert_img($cert_no);


	// $file = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/sertificate/new/'.$user['code'].'.jpg';

// 	$mail->addAddress($user['email']);	// КТО ПОЛУЧИТ ПИСЬМО
// 	$mail->AddAttachment($file);

// $mail->Body = <<<MEVINSIDE
// <p>{$user['name']}, здравствуйте!</p>
// <p>В связи с действиями злоумышленников мы были вынуждены заменить сертификат, полученный Вами в квесте «Поиск сокровищ» от сети магазинов Familia, на сертификат нового образца.
// Приносим Вам свои извинения за доставленные неудобства!</p>
// <p>Новый сертификат Вы найдёте в приложении. Распечатайте его и предъявите в любом магазине сети. Сертификат можно использовать до 1.01.2018.
// Желаем Вам хорошего дня!</p>
// MEVINSIDE;

// ### если письмо НЕ ОТПРАВЛЕНО:
// if(!$mail->send()) {
//     $data["mail_error"] = 'Message could not be sent.';
//     $data["mail_error"] .= 'Mailer Error: ' . $mail->ErrorInfo;
// ### если письмо ОТПРАВЛЕНО
// } else {
//     $data["mail_error"] = 'Message has been sent';

//     echo $user['email'].' отправлено - '.$cert_no.'<br>';
// }


// echo $i.' '.$user['name'].' '.$user['email'].' '.$user['code']."<br>";


// }











?>