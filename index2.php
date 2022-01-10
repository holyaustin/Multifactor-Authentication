<?php
session_start();
define('DEMO_HOME','localhost');
define('PROJECT_CATEGORY','otp');
//define('PROJECT_SLUG','otp-system-using-php');
$project_url = DEMO_HOME.'/'.PROJECT_CATEGORY;
if(isset($_POST['otp_submit'])){
//Generate OTP USING bin2hex() function
$msg="";
$err="";
if(empty($_POST['otp_email'])) $err = "Enter Your Phone number"; else $email = $_POST['otp_email'];
if($email and filter_var($email, FILTER_VALIDATE_EMAIL) === false) $err = "Enter Your Email Id"; else $re_email = $_POST['otp_email'];
if(!$err){
$otp_code = strtoupper(bin2hex(openssl_random_pseudo_bytes(3)));
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <demos@insbyrah.com>' . "\r\n";
$headers .= 'Cc: negi.webdeveloper1@gmail.com' . "\r\n";
$emailSubject = "DEMO OTP - HOW TO MAKE OTP CODE USING PHP";
//$customer_email= $_POST['otp_email'];;
$emailContent = "<div style='
    background: #f5f5f5;
    max-width: 600px;
    margin: 20px auto; 
    border: 1px solid #282887;
'>
	<div style='
    padding: 10px;
    background: #fdd55d;
    text-align: center;
    border-bottom: 1px solid #282887;
'><img src='http://www.insbyrah.com/wp-content/uploads/2016/08/cropped-INSBYRAH_LOGO_NEW.png' width='200'></div>
	<div style='
    padding: 20px;
    text-align: center;
    color: #282887;
'><h2 style='
    text-align: center;
    margin-bottom: 20px;
'>Thanks to request an OTP</h2> 
				<p style='
    margin-bottom: 10px;
'>Your OTP Code for this transaction is <strong>$otp_code</strong></p>
				<p style='
    text-align: center;
    margin-bottom: 15px;
'>Like us On Facebook</p>
				<p><a href='http://fb.me/Mr.Rahul.Negi/' style='
    display: block;
    width: 100%;
    text-align: center;
    font-size: 18px;
    border: 1px solid #3b5998;
    position: relative;
    font-weight: 400;
    outline:0;
    cursor:pointer;
    text-decoration: none;
    max-width:300px;
    margin:20px auto;
    padding: 10px 0;
    background: transparent;
    color: #3b5998;
    border-width: 2px;
    border-radius:25px;
'>Like and Stay Updated</a></p>
<p style='
    margin-bottom: 10px;
'>www.insbyrah.com<br>
Mail us at: demos@insbyrah.com</p>
  </div>
	</div>";
mail($email ,$emailSubject ,$emailContent); // Using mail() Function to send otp pin via email

try {
    $dbLink = new PDO('mysql:host=localhost;dbname=dbname', "root", "");
    $statement = $dbLink->prepare("INSERT INTO n_otp_table (n_otp_code,n_otp_email) VALUES(:otp_code, :otp_email) ON DUPLICATE KEY UPDATE    
		n_otp_code=:otp_code, n_otp_email=:otp_email");
	$statement->execute(array(
    "otp_code" => "$otp_code",
    "otp_email" => "$re_email"
	));
	$statement2 = $dbLink->prepare("INSERT INTO n_otp_table2 (n_otp_code,n_otp_email) VALUES(:otp_code, :otp_email) ON DUPLICATE KEY UPDATE    
		n_otp_code=:otp_code, n_otp_email=:otp_email");
	$statement2->execute(array(
    "otp_code" => "$otp_code",
    "otp_email" => "$re_email"
	));
    $dbLink = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
$msg ="An OTP Code was sent to your email id";
$_SESSION['email'] = $re_email;
}
}

if(isset($_POST['otp_verify'])){
//Generate OTP USING bin2hex() function
$msg="";
$err="";
if(empty($_POST['otp_code'])) $err = "Enter OTP Code"; else $otp_code = $_POST['otp_code'];
if($otp_code and strlen($otp_code) !== 6) $err = "Invalid OTP Code Entered By You!"; else $re_otp_code = $_POST['otp_code'];
if(empty($_POST['otp_email'])) $err = "Invalid Session"; else $email = $_POST['otp_email'];
if($email and filter_var($email, FILTER_VALIDATE_EMAIL) === false) $err = "Invalid Session mail"; else $re_email = $_POST['otp_email'];
if(!$err){

try {
     $dbLink = new PDO('mysql:host=localhost;dbname=dbname', "root", "");
    $statement = $dbLink->prepare("SELECT n_otp_email from n_otp_table where n_otp_email = :otp_email and n_otp_code = :otp_code");
	$statement->execute(array(
    "otp_email" => "$re_email",
    "otp_code" => "$re_otp_code"
	));
	$fetch = $statement->fetch();
	if($fetch[0]){
	$msg ="Thanks for Verifing Your OTP Code!";
	$preparedStatement = $dbLink->prepare("DELETE from n_otp_table where n_otp_email=:otp_email");
	$preparedStatement->execute(array(':otp_email' => $re_email));
	session_destroy();
	}else{
	$err = "Invalid OTP Code";
	}
    $dbLink = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
}
}
/*OTP SYSTEM CODE
 
function sendSMS($mobile=null, $subject=null)
{
$SMSapiKey = 'XYZ';
$url = 'http://example.com/api_2.0/SendSMS.php?APIKEY='.$SMSapiKey.'&amp;amp;amp;amp;amp;amp;amp;amp;MobileNo='.urlencode($mobile).'&amp;amp;amp;amp;amp;amp;amp;amp;SenderID=SAMPLE_MSG&amp;amp;amp;amp;amp;amp;amp;amp;Message='.urlencode($subject).'&amp;amp;amp;amp;amp;amp;amp;amp;ServiceName=TEMPLATE_BASED';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$returndata = curl_exec($ch);
curl_close($ch);
return "A SMS SENT SUCCESSFULLY TO $mobile";
}
$otp_code = strtoupper(bin2hex(openssl_random_pseudo_bytes(3)));  // A smart code to generate OTP PIN.
 
//update_user_meta($user_ID, 'otp_payment','$otp_code'); // For wordpress user's OTP management
$query = mysql_query("UPDATE otp_table SET otp_code='".$otp_code."' WHERE user_id= '$user_ID' ");// For PHP USER's OTP MANAGEMENT
 
$otp_query_fetch = mysql_query($query);
 
//Send OTP Via Email
 
$customer_email = "customer_name@example.com";
 
$emailSubject = "Hello, We received an Authentication Request."
 
$emailContent = "Thanks to request an OTP, Your OTP Code for this transaction is $otp_code";
mail($customer_email ,$emailSubject ,$emailContent); // Using mail() Function to send otp pin via&amp;amp;amp;amp;amp;amp;amp;nbsp;email
 
// Send OTP Via SMS
 
sendSMS(123456789, "Hello, Your OTP (One Time Password) for this transaction is $otp_code");
 
echo "An OTP has been sent to your mobile and email.";
*/
?>
<!DOCTYPE html>
<html>
<head>
<title>SMS Verification Online Examination System</title>
<meta name="description" content="SMS Verification Online Examination System "/>
<link href='https://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="http://demos.insbyrah.com/php/otp-system-using-php/assets/css/app.css" type="text/css"/>
<script src="http://demos.insbyrah.com/php/otp-system-using-php/assets/js/app.js" type="text/javscript"/></script>
</head>
<body>
	<div class="main_wrapper">
		<div class="inner_wrapper">
			<div class="otp_form">
				<h1 class="otp_login_form_heading">One time Password SMS</h1>
				<?php if($_GET['otp_varification'] !=="yes"){?>
				<form class="otp_login_form" action="" method="post">
					<?php if ($err):?>
					<p class="otp_app_err"><?php echo $err;?></p>
					<?php endif;?>
					<?php if ($msg):?>
					<p class="otp_app_msg"><?php echo $msg;?></p>
					<script type="text/javascript">
						setTimeout(function(){
						window.location.href="http://demos.insbyrah.com/php/otp-system-using-php/?otp_varification=yes&email=<?php echo $_POST['otp_email'];?>";
						},3000);
					</script>
					<?php endif;?>
					<input type="email" name="otp_email" class="otp_login_form_input" value="" placeholder="Enter Your Phone No" required/>
					<input type="submit" name="otp_submit" class="otp_login_form_button" value="Send OTP">
					<p class="otp_app_hint">Please Enter Your Email id, So we can send an <b>OTP PIN</b> code to your email id.</p>
				</form>
				
				
				<?php }else{?>
					<form class="otp_login_form" action="" method="post">
					<?php if($err):?>
					<p class="otp_app_err"><?php echo $err;?></p>
					<?php endif;?>
					<?php if($msg):?>
					<p class="otp_app_msg_success"><?php echo $msg;?></p>
					<?php else:?>
					<?php if(($_GET['email'] == $_SESSION['email']) and filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) !== false){?>
					<p>Your email id is</p>
					<input type="email" name="otp_email" class="otp_login_form_input" value="<?php echo $_GET['email'];?>" readonly style="border:1px solid #f9f9f9; background:#f9f9f9;" required/>
					<?php }else{?>
					<p style="color:red; margin-bottom:20px;">Invalid Activity Found In This Session</p>
					<?php } ?>
					<input type="text" name="otp_code" class="otp_login_form_input" value="" placeholder="Enter OTP Code" required/>
					<input type="submit" name="otp_verify" class="otp_login_form_button" value="Confirm OTP">
					<p class="otp_app_hint">Please Enter OTP Code, Check Your MailBox To Get This</p>
					<?php endif ;?>
				</form>
				<a href="<?php echo $project_url;?>" class="otp_login_form_button_green">Re-Send OTP</a>
				<?php } ?>
			</div>
		</div>
		
	</div>
		
</body>
</html>

