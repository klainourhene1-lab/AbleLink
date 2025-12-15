<?php
session_start();

// ------------ CONFIG ------------
$client_id = "YOUR_CLIENT_ID";
$client_secret = "YOUR_CLIENT_SECRET";
$redirect_uri = "http://localhost/yerabby/view/general/google.php";
// --------------------------------

require_once __DIR__ . "/../../Control/UserController.php";
require_once __DIR__ . "/../../Model/User.php";

$controller = new UserController();

// --------- 1) إذا ما فمّاش code → نمشيو لجوجل ------------
if (!isset($_GET['code'])) {

    $google_url =
        "https://accounts.google.com/o/oauth2/auth?" .
        "response_type=code&" .
        "client_id=$client_id&" .
        "redirect_uri=$redirect_uri&" .
        "scope=email%20profile";

    header("Location: $google_url");
    exit;
}

// --------- 2) Exchange code → token ------------
$code = $_GET['code'];

$token = file_get_contents("https://oauth2.googleapis.com/token", false, stream_context_create([
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/x-www-form-urlencoded",
        "content" => http_build_query([
            "code" => $code,
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "redirect_uri" => $redirect_uri,
            "grant_type" => "authorization_code"
        ])
    ]
]));

$token = json_decode($token, true);
$access_token = $token['access_token'] ?? null;

// --------- 3) Get user info from Google ------------
$userInfo = file_get_contents("https://www.googleapis.com/oauth2/v2/userinfo?access_token=".$access_token);
$googleUser = json_decode($userInfo, true);

$email = $googleUser['email'];
$name = $googleUser['name'];
$picture = $googleUser['picture'];

// --------- 4) Check DB ------------
$user = $controller->getUserByEmail($email);

if ($user) {
    // يعمللو Login
    $_SESSION['user_id'] = $user->getId();

   header("Location: http://localhost/yerabby/view/general/profile.php");
    exit;
}

// --------- 5) إذا ما فماش → نعملو حساب جديد Google ------------
$newUser = new User(
    null,
    $googleUser['given_name'],   // prénom
    $googleUser['family_name'],  // nom
    $googleUser['email'],
    "",
    "",
    "Utilisateur",
    "actif",
    date("Y-m-d H:i:s"),
    date("Y-m-d H:i:s"),
    $googleUser['picture']
);



$controller->createUser($newUser);

// نرجعو نجيبو user من DB
$user = $controller->getUserByEmail($email);

// نحطّوه في الـ SESSION
$_SESSION['user_id'] = $user->getId();


// نمشيو للداشبورد
header("Location: http://localhost/yerabby/view/general/profile.php");
exit;



