<?php

require_once 'database_config.php';


if (!function_exists('csrf_token')) {

    /**
     *CSRF (Cross-site request forgery)
     * Generate and storehash random string.
     * @return string -The random string.
     */
    function csrf_token() {
        $token=sha1(rand(1,1000).'$$'.date('H.i.s').'donuts');
        $_SESSION['csrf_token']=$token;
        return $token;
    }

}

if (!function_exists('user_verify')) {
     /**
     * Session hijacking 
     * Check if match to the login.
     * @return bool.
     */
    
    function user_verify(){

        $verify = false;
        
        if(isset($_SESSION['user_id'])){
            
            if(isset($_SESSION['user_ip'])&& $_SERVER['REMOTE_ADDR']== $_SESSION['user_ip']){
                
                if(isset($_SESSION['user_agent'])&& $_SERVER['HTTP_USER_AGENT']== $_SESSION['user_agent']){
                    
                    $verify = true;
                }
            }
        }
        return $verify;
    }    
}

if (!function_exists('email_exist')) {
     /**
      * Check if is taken.
      * @param $link mysqli object.
      * @param $email string The value to check.
      * @return bool.
     */

    function email_exist($link,$email){
        
        $exist = false;
        
        $sql = "SELECT email FROM users WHERE email = '$email'";
        $result = mysqli_query($link,$sql);
        
        if($result && mysqli_num_rows($result)==1){
            $exist = true;
        }
        
        return $exist;
        
    }
}

/*
 * returnJsonHttpResponse
 * @param $success: Boolean
 * @param $data: Object or Array
 */
function returnJsonHttpResponse($success, $data)
{
    // remove any string that could create an invalid JSON 
    // such as PHP Notice, Warning, logs...
    ob_clean();

    // Set your HTTP response code, 2xx = SUCCESS, 
    // anything else will be error, refer to HTTP documentation
    if ($success) {
        http_response_code(200);
    } else {
        http_response_code(403);
    }
    
    // encode your PHP Object or Array into a JSON string.
    // stdClass or array
    echo json_encode($data);

    // making sure nothing is added
    exit();
}







