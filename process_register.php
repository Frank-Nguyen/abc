<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 11/16/2015
 * Time: 10:30 PM
 */
//echo "1"; exit;
include ('vendor/autoload.php');

try {

    $conn = new PDO(

        'mysql:host=localhost;dbname=tintuc2;charset=utf8',

        'root',

        ''

    );

} catch (PDOException $e) {

    throw new Exception('No connect to database!');

}

$email = isset($_POST['email']) ? $_POST['email'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

$active_key = substr(md5(time()).$email,0,20);

//setcookie("user_name", $user_name, time() + 3600);
//setcookie("email", $email, time() + 3600);
//setcookie("password", $password, time() + 3600);
//setcookie("phone", $phone, time() + 3600);
//setcookie("avatar", $avatar, time() + 3600);
//setcookie("active_key", $active_key, time() + 3600);
//setcookie("status", $status, time() + 3600);
//setcookie("created_at", $created_at, time() + 3600);
//setcookie("updated_at", $updated_at, time() + 3600);

$_SESSION['email']=$email;
$_SESSION['password']=$password;


if ($email == "") {
    $msg = "Email khong duoc de trong";
    $_SESSION['error'][] = $msg;
}

if ($password == "") {
    $msg = "Password khong duoc de trong";
    $_SESSION['error'][] = $msg;
}
//Kiem tra email da ton tai hay chua
checkEmailExist($email,$conn);
//var_dump($_SERVER); exit;
if (isset($_SESSION['error'])) {
    header("Location:http://localhost/tintuc/index.php?action=register");
} else {
    try {

        $conn = new PDO(

            'mysql:host=localhost;dbname=tintuc2;charset=utf8',

            'root',

            ''

        );

    } catch (PDOException $e) {

        throw new Exception('No connect to database!');

    }
    $sql = "insert into users(user_name, email, password, phone, avatar, active_key, status, created_at, updated_at) values
      (:user_name, :email, :password, :phone, :avatar, :active_key, :status, :created_at, :updated_at)
";
    $sth = $conn->prepare($sql);

    $result = $sth->execute(array(

        ':user_name' => $email,
        ':email' => $email,
        ':password' => $password,
        ':phone' => "",
        ':avatar' => "",
        ':active_key' => $active_key,
        ':status' => 0,
        ':created_at' => $created_at,
        ':updated_at' => $updated_at,

    ));

    if ($result == 1) {

//        include ('vendor/autoload.php');

        $mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'lophocphptest@gmail.com';                 // SMTP username
        $mail->Password = 'anhThanh12!@';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('from@example.com', 'PHP co ban');
        $mail->addAddress($email, $email);     // Add a recipient
//        $mail->addAddress('ellen@example.com');               // Name is optional
//        $mail->addReplyTo('info@example.com', 'Information');
//        $mail->addCC('cc@example.com');
//        $mail->addBCC('bcc@example.com');

//        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'Click vao link sau de <a href="http://localhost/tintuc/index.php?action=active_user&active_key='.$active_key.'">Active</a>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }

        $msg = "Ban da them moi thanh cong";
        $_SESSION['success'][] = $msg;
        header("Location:".$_SERVER['HTTP_REFERER']);
    } else {
        $msg = "Ban da them moi that bai";
        $_SESSION['error'][] = $msg;
        header("Location:".$_SERVER['HTTP_REFERER']);
    }
}

function checkEmailExist($email,$conn){
    $sql = "select * from users where email = :email";
    $sth = $conn->prepare($sql);

    $sth->execute(array(

        ':email' => $email,

    ));

    $result = $sth->fetchAll(PDO::FETCH_OBJ);

    if(count($result)>0){
        $msg = "email da ton tai";
        $_SESSION['error'][] = $msg;
    }
}
