<?php
require_once 'app/helpers.php';
require_once 'app/cors_helper.php';

$error = '';
$request_body = json_decode(file_get_contents('php://input'));

 /* XSS attack (Cross-Site Scripting Attacks) - htmlspecialchars() | htmlentities + filter_input() */
$email = trim(filter_var($request_body->email, FILTER_VALIDATE_EMAIL));
$password = trim(filter_var($request_body->password, FILTER_SANITIZE_STRING));
  
if (!$email) {
    $error = '* A valid email is required';
    returnJsonHttpResponse(false,["data" =>$error]);
}
if (!$password) {
    $error = '* A valid password is required';
    returnJsonHttpResponse(false,["data" =>$error]);
}

$link = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PWD, MYSQL_DB);
mysqli_set_charset($link, 'utf8');

/* SQL injection - mysqli_real_escape_string($link,$dirty_data)*/
$email = mysqli_real_escape_string($link, $email);
$password = mysqli_real_escape_string($link, $password);
$sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($link, $sql);
        
if (empty($result) || mysqli_num_rows($result) !== 1) {
    $error = '*Wrong email and password';
    returnJsonHttpResponse(false,["data" =>$error]);
}
                
$user = mysqli_fetch_assoc($result);
                
if (password_verify($password, $user['password'])) {
    returnJsonHttpResponse(true,["data" => ['email'=>$user['email'],'XSRF-TOKEN'=>csrf_token()]]);
} else {
    $error = '*Wrong email and password';
    returnJsonHttpResponse(false,["data" =>$error]);
}

                  
    
?>
