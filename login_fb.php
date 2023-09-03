<?php
require_once './Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '1164449343724518', // Replace {app-id} with your app id
  'app_secret' => '5af7ed854964733bc5e9bf38351bc7c2',
  'default_graph_version' => 'v2.4',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email,user,photos']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://facebook.gsupertrack.com/fb-callback.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';

?>