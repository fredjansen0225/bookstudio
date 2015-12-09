<?php
require_once '_db.php';

require_once __DIR__ . '/Facebook/autoload.php';

# /js-login.php
$fb = new Facebook\Facebook([
    'app_id' => $fbAppId,
    'app_secret' => $fbAppSecret,
    'default_graph_version' => 'v2.2',
]);

$helper = $fb->getJavaScriptHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (! isset($accessToken)) {
    echo 'No cookie set or no OAuth data could be obtained from cookie.';
    exit;
}
$fb_token = (string) $accessToken;

$_SESSION['fb_access_token'] = $fb_token;

try {
    // Returns a `Facebook\FacebookResponse` object
    $response = $fb->get('/me?fields=id,name,email', (string) $accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

$user = $response->getGraphUser();

$fb_id = $user['id'];
$fb_name = $user['name'];
$fb_email = $user['email'];

$data = Array (
    "client_name" => $fb_name,
    "client_email" => $fb_email,
    "client_social_token" => $fb_token,
    "client_social_type" => "fb",
    "client_details" => "details",
);

$updateColumns = Array ("client_social_token");
$lastInsertId = "client_id";
$dbMysql->onDuplicate($updateColumns, $lastInsertId);
$id = $dbMysql->insert ('client', $data);

if($id)
    echo 'user was created. Id=' . $id;
else
    header('Location: login.php');

$_SESSION['client_id'] = $id;
$_SESSION['client_name'] = $fb_name;
$_SESSION['client_email'] = $fb_email;

// User is logged in!
// You can redirect them to a members-only page.
header('Location: edit.php');