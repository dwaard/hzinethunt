<?php
//mail tester
$to      = 'daan.de.waard@hz.nl';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: inethunt_noreply@hz.nl' . "\r\n" .
    'Reply-To: inethunt_noreply@hz.nl' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
echo 'mail sent';
?>