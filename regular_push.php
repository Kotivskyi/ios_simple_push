<?php

// device token for regular push
$deviceToken = '5079790ffeb8ac9f48e3a209968040e6b82f9bd9a03ddca11cbfaac93ec6d06f';

//alert message
$message = $argv[1];

if (!$message)
    exit('Example Usage: $php regular_push.php \'Hello!\' ' . "\n");

// Put you certificate here:  
$certfile = 'apns_regular.pem';

// Put your private key's passphrase here:
$passphrase = '';

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', $certfile);
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server


$fp = stream_socket_client(
//  'ssl://gateway.push.apple.com:2195', $err,
    'ssl://gateway.sandbox.push.apple.com:2195', $err,
    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
    exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body

$body['aps'] = array(
    'alert' => $message,
    'sound' => 'default',
    );


// Encode the payload as JSON

$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
    echo 'Message not delivered' . PHP_EOL;
else
    echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);