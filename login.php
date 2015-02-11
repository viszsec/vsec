<?PHP 
include_once __DIR__ .'js/csrfprotector.php';

session_start(); 


 require 'include/connect.php'; 

 require 'include/header.php'; 


if (isset($_SESSION['userid']))
{
    if ($_SESSION['userid'] != md5($_SERVER['userid']))
    {
        /* Prompt for password */
        exit;
    }
}
else
{

}

if(isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']))
{
$_SERVER["REMOTE_ADDR"] = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
}

; 
 
 require 'templates/login.php'; 
 require 'include/footer.php'; 
 

?>
