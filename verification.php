    <?php
	session_start();
 	$otp_code = $_SESSION['otp_code'];
	//$re_otp_code = $_SESSION['otp_code'];
	$otp_email = $_SESSION['email'];
	$msg="";
	$err="";	
	$email="";


if(isset($_POST['otp_verify'])){
//Generate OTP USING bin2hex() function
if(empty($_POST['otp_code2'])) $err = "Enter your PassCode"; else $otp_code = $_POST['otp_code2'];
if(isset($otp_code) and strlen($otp_code) !== 6) $err = "PassCode not complete!"; else $re_otp_code = $_POST['otp_code2'];
if(empty($_SESSION['otp_email'])) $err = "Invalid Session"; else $email = $_SESSION['otp_email'];
$re_email = $otp_email;
if(!$err){

try {
     $dbLink = new PDO('mysql:host=localhost;dbname=oes', "root", "");
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
	$err = "Invalid PassCode";
	}
    $dbLink = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
}
}  

?>
<?php
function newurl() {
header ("Location: http://localhost/oes/");
}
?> 	
	

 
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<link rel="stylesheet" type="text/css" href="oes.css"/>
            <title>Online Examination System SMS Page</title>
        </head>
     
        <body>
      
      <div id="container">
            
                <div class="header">
                <img style="margin:10px 2px 2px 10px;float:left;" height="80" width="200" src="images/logo.gif" alt="OES"/><h3 class="headtext"> &nbsp;Secondary School Exam Management System </h3><h4 style="color:#ffffff;text-align:center;margin:0 0 5px 5px;"><i>...because Examination Matters</i></h4>
            </div>
		
		    <div class="menubar">
 
      </div>
		
			<div class="page">	
            <h2 style="text-align: center">&nbsp;</h2>
            <h2 style="text-align: center">Verify your  Passcode </h2>
			
            <div style="border: 1px solid #333; padding: 5px 10px; width: 40%; margin: 0 auto; background: url('images/page.gif'') repeat";>
			 <p align="center">
			 			 Please Enter PassCode, Check Your Mobile Phone for this.
						  <p>&nbsp;</p> <p>&nbsp;</p>
			 		<form class="otp_login_form" action="" method="post">
					
					<?php if($err):?>
					<p class="otp_app_err"><?php echo $err;?></p>
					<?php endif;?>
					<?php if($msg):?>
					<p class="otp_app_msg_success"><?php newurl()?></p>
					
					
					<a href="http://localhost/oes/"> <h3> Procced to Login </h3> </a>
					<?php else:?>
					<p align="center">
					<input type="text" name="otp_code2" class="otp_login_form_input" value="" placeholder="Enter OTP Code" required/>
					<input type="submit" name="otp_verify" class="otp_login_form_button" value="Confirm OTP"> </p>
					<?php endif ;?>

				 <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
            </form>
            </div>
			<p>&nbsp; </p>
			<p>&nbsp; </p>
			<p>&nbsp; </p>
			<p>&nbsp; </p>
			<p>&nbsp; </p>
			<p>&nbsp; </p>
			<p>&nbsp; </p>
			
			
			
			
			    <div id="footer">
          <p style="font-size:70%;color:#ffffff;"> Developed By-<b>Bulani Everestus O. 2014223004</b><br/> </p><p>Released under the GNU General Public License v.3</p>
      </div>
      </div>
        </body>
    </html>