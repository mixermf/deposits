<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	require 'class.phpmailer.php';

	if (isset($_POST['email'])) {$user_email = $_POST['email'];}
	//$user_email = 'm.nikitin@binbank.ru';
 
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->IsMail();
	$mail->AddAddress($user_email);
	$mail->SetFrom('please_reply@binbank.ru', 'БИНБАНК');
	$mail->Subject = 'Тарифы по вкладам БИНБАНКа – с доставкой на дом';
	$mail->isHTML(true);
	$mail->Body    = 'Добрый день, <br><br><br>Вы заказывали на нашем сайте тарифы по вкладам – с радостью высылаем вам эту информацию. Если у вас останутся вопросы, получите более подробную консультацию у нашего сотрудника по телефону 8 800 555 5575 или в ближайшем отделении БИНБАНКа. <br><br>Ждем вас!';

	//$mail->AddAttachment( 'https://www.binbank.ru/landing/deposits/docs/deposit_all.pdf' , 'Сборник Тарифов по Вкладам частных клиентов ПАО «БИНБАНК».pdf' );
	//$mail->AddAttachment( 'https://www.binbank.ru/landing/deposits/docs/deposit_season.pdf' , 'Сезонный вклад Высокий сезон  ПАО «БИНБАНК».pdf' );

	return $mail->Send();
?>