<?php

use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

$mail = new PHPMailer;

$mail->isSMTP();

$mail->SMTPDebug = 2;

$mail->Host = 'smtp.gmail.com';

$subject = $_POST['subject'];
$type = $_POST['type'];
$crawl_link = $_POST['crawl_link'];
$post_link = $_POST['post_link'];
$title = $_POST['title'];
$content = $_POST['content'];
$iframe = $_POST['iframe'];
$app_link = $_POST['app_link'];
$notification = $_POST['notification'];
$featured_image = $_POST['featured_image'];
$detail_message = $_POST['detail_message'];

if (isset($subject)){

	$mail->Port = 587;

	$mail->SMTPSecure = 'tls';

	$mail->SMTPAuth = true;

	$mail->Username = "vicheanak@gmail.com";

	$mail->Password = "Helloworld123";

	$mail->setFrom('vicheanak@gmail.com.com', 'KhmerNewsLive24.com');

	$mail->addAddress('emailisden@gmail.com', 'Vannavy Vicheanak');

	$mail->Subject = $subject;


	$html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	  <title>'.$subject.'</title>
	</head>
	<body>
	<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
	  <h1>'.$subject.'</h1>
	  <p>Type: <strong>'.$type.'</strong></p>
	  <p>Crawl Link: <strong>'.$crawl_link.'</strong></p>
	  <p>Post Link: <strong>'.$post_link.'</strong></p>
	  <p>Title: <strong>'.$title.'</strong></p>
	  <p>Content: <strong>'.$content.'</strong></p>
	  <p>Content: <strong>'.$iframe.'</strong></p>
	  <p>App Link: <strong>'.$app_link.'</strong></p>
	  <p>Notification: <strong>'.$notification.'</strong></p>
	  <p>Featured Image: <strong>'.$featured_image.'</strong></p>
	  <p>Detail Message: <strong>'.$detail_message.'</strong></p>
	</div>
	</body>';

	$mail->msgHTML($html);

	if (!$mail->send()) {
	    // echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	    // echo "Message sent!";
	 
	}

}

