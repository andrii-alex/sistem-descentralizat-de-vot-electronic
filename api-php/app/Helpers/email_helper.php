<?php

function send_email($to, $subject, $message)
{
    // Load the email library
    $email = \Config\Services::email();

    // $email->initialize([
    //     'protocol' => 'smtp',
    //     'SMTPHost' => 'evoting.andrii.ro',
    //     'SMTPUser' => 'no-reply@evoting.andrii.ro',
    //     'SMTPPass' => '$EaiZh2*fsHo',
    //     'SMTPPort' => 465,
    //     // 'SMTPCrypto' => 'tls',
    //     // 'mailType' => 'html',
    //     'charset' => 'utf-8',
    //     'validate' => true,
    //     'debug' => true, // enable debug output
    //     'log' => true,
    // ]);

    // Set the email content
    $email->setTo($to);
    $email->setFrom('no-reply@evoting.andrii.ro', 'EVOTING D-APP');
    $email->setSubject($subject);
    $email->setMessage($message);

    // Send the email
    if ($email->send()) {
        return true;
    } else {
        return false;
    }
}
