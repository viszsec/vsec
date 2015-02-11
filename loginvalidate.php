<?PHP    

include_once __DIR__ .'js/csrfprotector.php';

session_start();
        
$string = $_SERVER['userid'];
$string .= 'SHIFLETT';

/* Add any other data that is consistent */

$fingerprint = md5($string);
        require 'include/connect.php';

?>

<?PHP        

if(isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']))
{
$_SERVER["REMOTE_ADDR"] = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
}

        $username=$_POST['txtUsername']; 
        $password=$_POST['txtPassword'];
        $turningcode = $_POST['turningcode'];
        $security_code = $_SESSION['security_code'];

        
        if($username=='')
        {
                $userflag=1;
                $_SESSION['empty_user']="<span class='formerror'>Please Enter the Username</span>";
        }
        if($password=='')
        {
            $passflag=1;
            $_SESSION['empty_pass']="<span class='formerror'>Please Enter the Password</span>";
        }

        if($turningcode=='')
        {
            $turningcodeflag=1;
            $_SESSION['empty_turningcode']="<span class='formerror'>Please Enter the Captcha Code</span>";
        }
        if($turningcode != $security_code)
        {
            $turningcodeflag=1;
            $_SESSION['empty_turningcode']="<span class='formerror'>Invalid Captcha Code</span>";
        }

        if($userflag!=1 && $passflag!=1 && $turningcodeflag!=1)
        {
            $current_time = date('Y-m-d H:i:s');
            $validuser_check_query="select * from members where username='".$username."' and password='".base64_encode(base64_encode($password))."' and status='active'";

            $validuser_check_result=mysql_query($validuser_check_query);
            if(mysql_num_rows($validuser_check_result)>0) 
            {
                $validuser_check_row=mysql_fetch_array($validuser_check_result);
                if($validuser_check_row['suspend_time'] == '0000-00-00 00:00:00' || $validuser_check_row['suspend_time'] < $current_time)
                {
                    $_SESSION['userid']=$validuser_check_row['member_id'];
                    $_SESSION['username']=$validuser_check_row['username'];
                    mysql_query("UPDATE members SET last_login_time='".$validuser_check_row['current_login_time']."',current_login_time='".date('Y-m-d H:i:s')."',last_asscess_ip='".$validuser_check_row['current_asscess_ip']."',current_asscess_ip='".$_SERVER['REMOTE_ADDR']."' WHERE member_id='".$_SESSION['userid']."'");
                echo '<meta http-equiv="refresh" content="0;url=memberhome.php">';
                    exit();
                }
                else
                {
                    $_SESSION['invalid']="<font color='#FF0000'>Your Account has suspended for next 24 hours</font>";
                    echo '<meta http-equiv="refresh" content="0;url=login.php">';
                    exit();
                }
            }
            else
            {
                if($_SESSION['lcnt'])
                {
                    $lcnt=$_SESSION['lcnt']+1;
                }
                else
                {
                    $lcnt=1;
                }
                $_SESSION['lcnt']=$lcnt;
                
                if($lcnt >= 6)
                {
                    $dattime = date('Y-m-d');
                    $asl = mysql_fetch_array(mysql_query("SELECT DATE_ADD('".$dattime."', INTERVAL 1 DAY) as cnt"));
                    $exp  = $asl['cnt']." ".date('H:i:s');
                    
                    $update = mysql_query("update members set suspend_time= '$exp' where username='$username'");
                    $_SESSION['invalid']="<font color='#FF0000'>Your Account has suspended for next 24 hours due to invalid username or password</font>";
                    echo '<meta http-equiv="refresh" content="0;url=login.php">';
                    exit();
                    
                }
            
                $_SESSION['invalid']="<font color='#FF0000'>Invalid username or password</font>";
                echo '<meta http-equiv="refresh" content="0;url=login.php">';
                exit();
            }
        }
        else
        {
            
            echo '<meta http-equiv="refresh" content="0;url=login.php">';
            exit();
        }
?> 
