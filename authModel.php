<?php
  require_once 'app/helpers.php';

class AuthModel{

  /**
   * signup user into the system
   * receive params:
   * email - required
   * password - required
   * confirmPassword - required
   * first_name- required
   * last_name - required
   * address- required
   * city- required
   * phone- required
   * homeNumber - required
   * apartment - optional
   * entry_code - optional
   * mailAds - optional
   * return email and XSRF-TOKEN
   */
  public function register(){

  $request_body = json_decode(file_get_contents('php://input'));

  $form_valid = true;
  $errors='';

  if(empty($request_body->email)){        
      $errors = 'חובה להכניס מייל';
      $form_valid = false;
  }

  if(empty($request_body->passwordRegister)){        
      $errors = 'חובה להכניס סיסמה';
      $form_valid = false;
  }

  if(empty($request_body->passwordConfirmation)){        
      $errors = 'חובה למלא את שדה אימות הסיסמה';
      $form_valid = false;
  }

  if ($request_body->passwordRegister != $request_body->passwordConfirmation) {
      $errors = 'הסיסמה ואימות הסיסמה אינם תואמים';
      $form_valid = false; 
  }

  if(empty($request_body->firstName)){        
      $errors = 'חובה להכניס שם פרטי';
      $form_valid = false;
  }

  if(empty($request_body->lastName)){        
      $errors = 'חובה להכניס שם משפחה';
      $form_valid = false;
  }

  if(empty($request_body->address)){        
      $errors = 'חובה להכניס כתובת';
      $form_valid = false;
  }

  if(empty($request_body->city)){        
      $errors = 'חובה להכניס שם עיר';
      $form_valid = false;
  }

  if(empty($request_body->homeNumber)){        
      $errors = 'חובה להכניס מספר בית';
      $form_valid = false;
  }

  if(empty($request_body->phone)){        
      $errors = 'חובה להכניס מספר טלפון';
      $form_valid = false;
  }

  if(!$form_valid){
      returnJsonHttpResponse(false,["data" =>$errors]);
  }
          /* XSS attack (Cross-Site Scripting Attacks) - htmlspecialchars() | htmlentities + filter_input() */
          $email = trim(filter_var($request_body->email, FILTER_VALIDATE_EMAIL));
          $password = trim(filter_var($request_body->passwordRegister, FILTER_SANITIZE_STRING));
          $firstName = trim(filter_var($request_body->firstName, FILTER_SANITIZE_STRING));
          $lastName = trim(filter_var($request_body->lastName, FILTER_SANITIZE_STRING));
          $address = trim(filter_var($request_body->address, FILTER_SANITIZE_STRING));
          $apartment = trim(filter_var($request_body->apartment, FILTER_SANITIZE_STRING));
          $city = trim(filter_var($request_body->city, FILTER_SANITIZE_STRING));
          $homeNumber= trim(filter_var($request_body->homeNumber, FILTER_SANITIZE_NUMBER_INT));
          $entryCode = trim(filter_var($request_body->entryCode, FILTER_SANITIZE_NUMBER_INT));
          $mailAds = trim(filter_var($request_body->mailAdvertiseCheckbox, FILTER_SANITIZE_STRING));
          $phone = trim(filter_var($request_body->phone, FILTER_SANITIZE_STRING));

          $link = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PWD, MYSQL_DB);
          mysqli_set_charset($link, 'utf8');

          /* SQL injection - mysqli_real_escape_string($link,$dirty_data)*/
          $email = mysqli_real_escape_string($link, $email);
          $password = mysqli_real_escape_string($link, $password);
          $firstName = mysqli_real_escape_string($link, $firstName);
          $lastName = mysqli_real_escape_string($link, $lastName);
          $address = mysqli_real_escape_string($link, $address);
          $apartment = mysqli_real_escape_string($link, $apartment);
          $city = mysqli_real_escape_string($link, $city);
          $homeNumber = mysqli_real_escape_string($link, $homeNumber);
          $entryCode = mysqli_real_escape_string($link, $entryCode);
          $mailAds = mysqli_real_escape_string($link, $mailAds);
          $phone = mysqli_real_escape_string($link, $phone);
          
        
          $password= password_hash($password, PASSWORD_BCRYPT);
          $sql = "INSERT INTO users VALUES('','$email','$password','$firstName','$lastName',
              '$address','$homeNumber','$city','$apartment','$entryCode','$phone','$mailAds')";
          $result = mysqli_query($link, $sql);
              
          if($result && mysqli_affected_rows($link)>0){
            $to = $user['email'];
            $subject = "login with success";
            $txt = "thank for signup to the system with this mail:".$email;
            $headers = "From: support@quatro.com";
    
            mail($to,$subject,$txt,$headers);
              returnJsonHttpResponse(true,["data" => ['email'=>$email,'XSRF-TOKEN'=>csrf_token()]]); 
          }else{
              returnJsonHttpResponse(false,["data" =>'internal error']);
          }
    }

 /**
   * login user into the system
   * receive params:
   * email - required
   * password - required
   * return email and XSRF-TOKEN
   */
  public function login(){

    $error = '';
    $request_body = json_decode(file_get_contents('php://input'));

        
    if (empty($request_body->email)) {
        $error = 'שדה המייל הוא שדה חובה';
        returnJsonHttpResponse(false,["data" =>$error]);
    }

    if (empty($request_body->password)) {
        $error = 'שדה הסיסמה הוא שדה חובה';
        returnJsonHttpResponse(false,["data" =>$error]);
    }

    /* XSS attack (Cross-Site Scripting Attacks) - htmlspecialchars() | htmlentities + filter_input() */
    $email = trim(filter_var($request_body->email, FILTER_VALIDATE_EMAIL));
    $password = trim(filter_var($request_body->password, FILTER_SANITIZE_STRING));
  
    $link = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PWD, MYSQL_DB);
    mysqli_set_charset($link, 'utf8');

    /* SQL injection - mysqli_real_escape_string($link,$dirty_data)*/
    $email = mysqli_real_escape_string($link, $email);
    $password = mysqli_real_escape_string($link, $password);
    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($link, $sql);
            
    if (empty($result) || mysqli_num_rows($result) !== 1) {
        $error = 'מייל או סיסמה אינם תקינים';
        returnJsonHttpResponse(false,["data" =>$error]);
    }
                    
    $user = mysqli_fetch_assoc($result);
                    
    if (password_verify($password, $user['password'])) {
        $to = $user['email'];
        $subject = "login with success";
        $txt = "this your email: ".$email;
        $headers = "From: support@quatro.com";

        mail($to,$subject,$txt,$headers);
        returnJsonHttpResponse(true,["data" => ['email'=>$user['email'],'XSRF-TOKEN'=>csrf_token()]]);
    } else {
        $error = 'מייל או סיסמה אינם תקינים';
        returnJsonHttpResponse(false,["data" =>$error]);
    }

}
      
}
