<?php
header("Content-Type: text/plain; charset=utf-8");
error_reporting(E_ALL);
$test = 'multipart';

include 'config.php';
if (isset($_POST['email'])) {$to = $_POST['email'];}
//$to ='na.popova@binbank.ru, myworkakk@gmail.com';
$email = $to;
$ip_client = $_SERVER['REMOTE_ADDR'];
$pdo = new PDO(DSN, USER, PASSWORD);
$pdo->exec('set names utf8');

$stmt = $pdo->prepare(
    'INSERT INTO depo_tariff_emails'
    . '(email, utm_source, utm_medium, utm_campaign, utm_term, utm_content, ip_client) '
    . 'VALUES (?, ?, ?, ?, ?, ?, ?)'
//. 'VALUES (:name, :city, :email, :phone, :utm_source, :utm_medium, :utm_campaign, :utm_term, :utm_content, :date)'
);

$stmt->execute(
    array(
        $email,
        isset($_REQUEST['utm_source']) ? $_REQUEST['utm_source'] : null,
        isset($_REQUEST['utm_medium']) ? $_REQUEST['utm_medium'] : null,
        isset($_REQUEST['utm_campaign']) ? $_REQUEST['utm_campaign'] : null,
        isset($_REQUEST['utm_term']) ? $_REQUEST['utm_term'] : null,
        isset($_REQUEST['utm_content']) ? $_REQUEST['utm_content'] : null,
        $ip_client
    )
);


switch ($test) {
  case 'multipart':
    $boundary = md5(uniqid(time()));

    $my_subject = 'Тарифы по вкладам БИНБАНКа – с доставкой на дом';
    $subject = '=?utf-8?b?' . base64_encode($my_subject) . '?=';
    //$subject = iconv("windows-1251", "UTF-8//TRANSLIT", $my_subject);
    //$subject = iconv("UTF-8//TRANSLIT", "windows-1251", $my_subject);
    $headers .= "From: noreply@binbank.ru <mailto:noreply@binbank.ru>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";

    $message  = "Добрый день, <br><br><br>Вы заказывали на нашем сайте тарифы по вкладам – с радостью высылаем вам эту информацию. Если у вас останутся вопросы, получите более подробную консультацию у нашего сотрудника по телефону 8 800 555 5575 или в ближайшем отделении БИНБАНКа. <br><br>Ждем вас!";
    

    $multipart  = "--$boundary\r\n";
    $multipart .= "Content-Type: text/html; charset=utf-8\r\n";
    $multipart .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $multipart .= chunk_split(base64_encode($message));
    $multipart .= "\r\n\r\n";
    // $file1= iconv("UTF-8//TRANSLIT", "windows-1251", "Сборник Тарифов по Вкладам частных клиентов ПАО «БИНБАНК».pdf");
    // $file2= iconv("UTF-8//TRANSLIT", "windows-1251", "Сезонный вклад_Высокий сезон ПАО_БИНБАНК.pdf");
    $file1= "Sbornik tarifov po vkladam BINBANK.pdf";
    $file2= "Vklad high season BINBANK.pdf";
    $path='/opt/home/webmaster/www/anketa/landing/pdf/';
    echo $path.$file1;
    $files = array($path.$file1);
     function basename2($path){
        return substr(strrchr($path, "/"), 1);
    }
    foreach ($files as $item) {
      
      $filename = $item;
      $fp = fopen($filename, "r");
      if (!$fp) die('file not found');
      $file = fread($fp, filesize($filename));
      fclose($fp);

      //$filename = '=?utf-8?b?' . base64_encode($item) . '?=';
      //$filename= iconv("UTF-8//TRANSLIT", "windows-1251",$item);
      $message_part = "--$boundary\r\n";
      $message_part .= "Content-Type: application/octet-stream; name=\"".basename2($filename)."\"\r\n";
      $message_part .= "Content-Transfer-Encoding: base64\r\n";
      $message_part .= "Content-Disposition: attachment; filename=\"".basename2($filename)."\"\r\n\r\n";

      echo "\n$message_part";

      $message_part .= chunk_split(base64_encode($file));
      $multipart .= "$message_part\r\n\r\n";
    }  

    $multipart .= "--$boundary--";
    $res = mail($to, $subject, $multipart, $headers);
    
    echo "To: $to\nSubject: $subject\nHeaders:\n$headers";
    echo ($res == 1 ? 'Success!' : 'Failed!');
    
    break; 
   
  default:
    $subject = 'default (html)';

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "From: ${_SERVER['HTTP_HOST']} <${_SERVER['SERVER_ADMIN']}>\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";

    $message  = "<p>Testing <i>multipart</i> email <u>with</u> <b>attachment</b>...<p>";
    $message .= "<p>".date("Y-m-d H:i:s")."</p>";
    $message .= "<p>${_SERVER['SERVER_ADMIN']} // ${_SERVER['HTTP_HOST']}</p>";

    $res = mail($to, $subject, $message, $headers);
  
    echo "To: $to\nSubject: $subject\nHeaders:\n$headers";
    echo ($res == 1 ? 'Success!' : 'Failed!');

    break;
}

