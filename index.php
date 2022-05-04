
<?php

require_once 'app/cors_helper.php';
require_once 'authModel.php';

$request_body = json_decode(file_get_contents('php://input'));

if(!empty($request_body->method)){
    $model = new AuthModel();
    switch($request_body->method){
        case 'register':
            return $model->register();
          break;
        case 'login':
            return $model->login();
          break;
      }
}else{
    returnJsonHttpResponse(false,["data" =>'internal error']);
}
    
?>
