   <?php
	 session_start();
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "oes";
	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}

	 $sql="select contactno from student where stdid=(SELECT MAX(stdid) From student)";
	$result = mysqli_query($conn, $sql);
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
		$recipients = $row["contactno"];
}
	$message="";
	$msg="Your PassCode was sent to your phone";
    $json_url = "http://api.ebulksms.com:8080/sendsms.json";
    $xml_url = "http://api.ebulksms.com:8080/sendsms.xml";
    $http_get_url = "http://api.ebulksms.com:8080/sendsms";
    $username = 'holyaustin@yahoo.com';
    $apikey = 'b02cfcdd2bb0e8a73be8ead04a1c9649a09642f4';
    //$recipients = '';
	$otp_code = strtoupper(bin2hex(openssl_random_pseudo_bytes(3))); // A smart code to generate OTP PIN.
	$otp_email = $recipients;
	$re_email = $recipients;
	$_SESSION['otp_code'] = $otp_code;
	$_SESSION['email'] = $otp_email;
	$_SESSION['otp_email'] = $otp_email;
	
    if (isset($_POST['button'])) {
        $sendername = $_POST['sender_name'];
        $flash = 0;

        $message = "This is your One Time Passcode " . $otp_code;
    #Use the next line for HTTP POST with JSON
        $result = useJSON($json_url, $username, $apikey, $flash, $sendername, $message, $recipients);
        
    #Uncomment the next line and comment the one above if you want to use HTTP POST with XML
        //$result = useXML($xml_url, $username, $apikey, $flash, $sendername, $message, $recipients);
        
    #Uncomment the next line and comment the ones above if you want to use simple HTTP GET
        //$result = useHTTPGet($http_get_url, $username, $apikey, $flash, $sendername, $message, $recipients);
    }
     
    function useJSON($url, $username, $apikey, $flash, $sendername, $messagetext, $recipients) {
        $gsm = array();
        $country_code = '234';
        $arr_recipient = explode(',', $recipients);
        foreach ($arr_recipient as $recipient) {
            $mobilenumber = trim($recipient);
            if (substr($mobilenumber, 0, 1) == '0'){
                $mobilenumber = $country_code . substr($mobilenumber, 1);
            }
            elseif (substr($mobilenumber, 0, 1) == '+'){
                $mobilenumber = substr($mobilenumber, 1);
            }
            $generated_id = uniqid('int_', false);
            $gsm['gsm'][] = array('msidn' => $mobilenumber, 'msgid' => $generated_id);
        }
        $message = array(
            'sender' => $sendername,
            'messagetext' => $messagetext,
            'flash' => "{$flash}",
        );
     
        $request = array('SMS' => array(
                'auth' => array(
                    'username' => $username,
                    'apikey' => $apikey
                ),
                'message' => $message,
                'recipients' => $gsm
        ));
        $json_data = json_encode($request);
        if ($json_data) {
            $response = doPostRequest($url, $json_data, array('Content-Type: application/json'));
            $result = json_decode($response);
            return $result->response->status;
        } else {
            return false;
        }
    }
     
     
    //Function to connect to SMS sending server using HTTP POST
    function doPostRequest($url, $data, $headers = array('Content-Type: application/x-www-form-urlencoded')) {
        $php_errormsg = '';
        if (is_array($data)) {
            $data = http_build_query($data, '', '&');
        }
        $params = array('http' => array(
                'method' => 'POST',
                'content' => $data)
        );
        if ($headers !== null) {
            $params['http']['header'] = $headers;
        }
        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            return "Error: gateway is inaccessible";
        }
        //stream_set_timeout($fp, 0, 250);
        try {
            $response = stream_get_contents($fp);
            if ($response === false) {
                throw new Exception("Problem reading data from $url, $php_errormsg");
            }
            return $response;
        } catch (Exception $e) {
            $response = $e->getMessage();
            return $response;
        }
    }
     
	 
	 try {
    $dbLink = new PDO('mysql:host=localhost;dbname=oes', "root", "");
    $statement = $dbLink->prepare("INSERT INTO n_otp_table (n_otp_code,n_otp_email) VALUES(:otp_code, :otp_email) ON DUPLICATE KEY UPDATE    
		n_otp_code=:otp_code, n_otp_email=:otp_email");
	$statement->execute(array(
    "otp_code" => "$otp_code",
    "otp_email" => "$re_email"
	));

    $dbLink = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
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
            <h2 style="text-align: center">One Time Passcode Page </h2>
			
            <div style="border: 1px solid #333; padding: 5px 10px; width: 40%; margin: 0 auto; background: url('images/page.gif'') repeat";>
            <form id="form1" name="form1" method="post" action="">
                
                    <?php
                    if (!empty($_POST)) {
                        if ($result == 'SUCCESS') {?>
                        <p style="border: 1px dotted #333; background: #33ff33; padding: 5px;">One Time Passcode Message sent to your Phone. </p>
						&nbsp; &nbsp; &nbsp;
						<p style="border: 1px dotted #333; background: #FFDACC; padding: 5px;"><a HREF="verification.php" ><h3>CLICK HERE TO VERFIY PASSCODE </h3></a></p>  &nbsp; &nbsp; &nbsp;
						<script type="text/javascript">
						setTimeout(function(){
						window.location.href="http://demos.insbyrah.com/php/otp-system-using-php/?otp_varification=yes&email=<?php echo $_POST['otp_email'];?>";
						},3000);
					</script>
                        <?php
                         }
                        else {?>
                        <p style="border: 1px dotted #333; background: #FFDACC; padding: 5px;">Message not sent</p>
                        <?php
                        }
                    }
                    ?>
                
				 <p align="center">
                    <label>Sender name:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="sender_name" type="text" id="name" value="SMS Verify" readonly/>
</label>
                </p>
				 <p>&nbsp;</p>
				 <!--<p>
                    <label>Username:
                        <input name="username" type="text" id="username"/>
                    </label>
                </p>
                <p>
                    <label>API Key:
                        <input name="apikey" type="password" id="passwd" />
                    </label>
                </p>
               
                <p>
                    <label>Recipients
                        <textarea name="telephone" id="telephone" cols="45" rows="2"></textarea>
                    </label>
                </p>
                <p>
                    <label>Message
                        <textarea name="message" id="message" cols="45" rows="5"></textarea>
                    </label>
                </p> -->
                <p align="center">
                    <label>
                        <input type="submit" name="button" id="button" value="Submit" />
                    </label> 
                    <label>
                        <input type="reset" name="button2" id="button2" value="Reset" />
                    </label>
                </p>
				
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