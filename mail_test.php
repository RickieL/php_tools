<?php
$mail_to='abcd@example.com';
$mail_title="test mail";
$mail_body = "mail test body";

mail($mail_to, $mail_title, $mail_body);
