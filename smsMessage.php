<?php

require_once("./include/messageBird/autoload.php");

$messageBird = new \MessageBird\Client('Y0MYKDUyHIaXkrxApI1TkcJag'); // Set your own API access key here.

$message             = new \MessageBird\Objects\Message();
$message->originator = 'nsit.lk';
$message->recipients = [447878253732];
$message->body       = 'This is a test message. Please let me know if you reciived it.';

try {
    $messageResult = $messageBird->messages->create($message);
    var_dump($messageResult);
} catch (\MessageBird\Exceptions\AuthenticateException $e) {
    // That means that your accessKey is unknown
    echo 'wrong login';
} catch (\MessageBird\Exceptions\BalanceException $e) {
    // That means that you are out of credits, so do something about it.
    echo 'no balance';
} catch (\Exception $e) {
    echo $e->getMessage();
}