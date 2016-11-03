<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

include 'config.php';

$list = file(dirname(__FILE__) . '/data/data.csv');
$emails = array();
foreach ($list as $line) {
    $l = explode(';', $line);

    $city = trim($l[2]);
    $office = $l[0];
    $office = preg_replace("/^\"+/", '', $office);
    $office = preg_replace("/\"+$/", '', $office);
    $office = str_replace('""', '"', $office);

    if ($city==$_REQUEST['city'] && $office==$_REQUEST['office']) {
        $emails[] = $l[1];
    }
}

$pdo = new \PDO(DSN, USER, PASSWORD);
$pdo->exec('set names utf8');

$stmt = $pdo->prepare(
    'INSERT INTO premium_land '
    . '(name, city, office, phone, email, utm_source, utm_medium, utm_campaign, utm_term, utm_content) '
    . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
//. 'VALUES (:name, :city, :email, :phone, :utm_source, :utm_medium, :utm_campaign, :utm_term, :utm_content, :date)'
);

$stmt->execute(
    array(
        $_REQUEST['name'],
        $_REQUEST['city'],
        $_REQUEST['office'],
        $_REQUEST['phone'],
        $emails[0],
        isset($_REQUEST['utm_source']) ? $_REQUEST['utm_source'] : null,
        isset($_REQUEST['utm_medium']) ? $_REQUEST['utm_medium'] : null,
        isset($_REQUEST['utm_campaign']) ? $_REQUEST['utm_campaign'] : null,
        isset($_REQUEST['utm_term']) ? $_REQUEST['utm_term'] : null,
        isset($_REQUEST['utm_content']) ? $_REQUEST['utm_content'] : null
    )
);

$subject = 'Zayavka na premium';

$message = <<<EOF
<p>Уважаемый сотрудник!</p>

<p>Клиент оставил заявку на получение ПУ Премиум через сайт Банка.</p>

<p>Просьба связаться с клиентом в самое ближайшее время</p>

<table>
<tr>
    <td>Имя:</td>
    <td>{$_REQUEST['name']}</td>
</tr>
<tr>
    <td>Город:</td>
    <td>{$_REQUEST['city']}</td>
</tr>
<tr>
    <td>Телефон:</td>
    <td>{$_REQUEST['phone']}</td>
</tr>
<tr>
    <td>E-mail:</td>
    <td>{$_REQUEST['email']}</td>
</tr>
</table>
EOF;

foreach ($emails as $email) {
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    $headers .= 'To: ' . $email . "\r\n";
    $headers .= 'From: Binbank <premium@binbank.ru>' . "\r\n";

    mail($to, $subject, $message, $headers);
}

header('Location: ../thankyou.php?name=' . $_REQUEST['name']);
