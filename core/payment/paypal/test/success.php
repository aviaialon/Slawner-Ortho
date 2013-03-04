<?php
require_once(__APPLICATION_ROOT__ . '/payment/paypal/paypal_gateway.php');
$payPalGateway 	= &new PAYPAL_GATEWAY(PAYPAL_HTTPS_SANDBOX_GATEWAY);
$payPalGateway->processNotifySyncRequest();

new dump($payPalGateway->getIpnInfo('payment_status'));
print "<br />";
new dump($payPalGateway->getIpnInfo());
print "<br />";
new dump($payPalGateway->getSerializedIpnInfo());
print "<br />";
new dump($payPalGateway->getConfigData());
print "<br />";
new dump($payPalGateway->getPaymentStatus());
print "<br />";
new dump($payPalGateway->isError());
print "<br />";
new dump($payPalGateway->isSuccess());
print "<br />";
new dump($payPalGateway->getErrors());

/*
// read the post from PayPal system and add 'cmd'
// read the post from PayPal system and add 'cmd'
$req 			= 'cmd=_notify-synch';
$tx_token 		= $_GET['tx'];
$auth_token 	= "M9X9sCIwGQetDfOLfAYZ2Gq_pdN_yCe3u7QQQoXSwlhqm4cziZqgJfLBHXK"; // LIVE : uS_bwm6ti5jxm4It5YpNQXosDItAqNS6DiCV7f8oak_S71X2ljaYU73hNe8
$req 		   	.= "&tx={$tx_token}&at={$auth_token}";

// post back to PayPal system to validate
$header 		.= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header 		.= "Content-Type: application/x-www-form-urlencoded\r\n";
$header 		.= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp 			 = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
// If possible, securely post back to paypal using HTTPS
// Your PHP server will need to be SSL enabled
// $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

if (!$fp) {
    // HTTP ERROR
    print "HTTP ERROR: " . $errstr;
} else {
    fputs ($fp, $header . $req);
	
    // read the body data
    $res 		= '';
    $headerdone = false;
	
	// Parse the file....
    while (! ((bool) feof($fp))) {
        $line = fgets ($fp, 1024);
        if (strcmp($line, "\r\n") == 0) {
            // read the header
            $headerdone = true;
        }
        else if ($headerdone) {
            // header has been read. now read the contents
            $res .= $line;
        }
    }
    
    // parse the data
    $lines 		= explode("\n", $res);
    $arrKeyArray 	= array();
    if (strcmp ($lines[0], "SUCCESS") == 0) {
       for ($i=1; $i< count($lines);$i++){
            list($key,$val) = explode("=", $lines[$i]);
            $arrKeyArray[urldecode($key)] = urldecode($val);
        }
		
		if (
			(strcmp(strtolower($arrKeyArray['payment_status']), 'completed') == 0) &&
			(strcmp(strtolower($arrKeyArray['receiver_email']), 'sales_1272505245_biz@regentvanlines.com') == 0) && // receiver_email is same as your account email
			($arrKeyArray['payment_gross'] == $arrKeyArray['mc_gross']) //check they payed what they should have
		) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			$firstname 	= $arrKeyArray['first_name'];
			$lastname 	= $arrKeyArray['last_name'];
			$itemname 	= $arrKeyArray['item_name'];
			$amount 	= $arrKeyArray['payment_gross'];
			
			echo ("<p><h3>Thank you for your purchase!</h3></p>");
			echo ("<b>Payment Details</b><br>\n");
			echo ("<li>Name: $firstname $lastname</li>\n");
			echo ("<li>Item: $itemname</li>\n");
			echo ("<li>Amount: $amount</li>\n");
			echo ("");
		} else {
			// PENDING OR DECLINED....
		}
		
		new dump($keyarray);
    }
    else if (strcmp ($lines[0], "FAIL") == 0) {
        // log for manual investigation
        var_dump($lines);
    }
    
}

fclose ($fp);*/
?>

Your transaction has been completed, and a receipt for your purchase has been emailed to you.<br> You may log into your account at <a href='https://www.paypal.com'>www.paypal.com</a> to view details of this transaction.<br>























<?php
die;
/*
 * success.php
 *
 * PHP Toolkit for PayPal v0.51
 * http://www.paypal.com/pdn
 *
 * Copyright (c) 2004 PayPal Inc
 *
 * Released under Common Public License 1.0
 * http://opensource.org/licenses/cpl.php
 *
 */
?>

<html>
<head><title>::Thank You::</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body bgcolor="ffffff">
<br>
<br>
<table width="500" border="0" align="center" cellpadding="1" cellspacing="0">
   <tr> 
      <td align="left" valign="top" bgcolor="#333333"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr> 
               <td align="center" bgcolor="#EEEEEE"> <p>&nbsp;</p>
                  <p>Thank you! Your order has been successfully processed.</p>
                  <p>&nbsp;</p></td>
            </tr>
         </table></td>
   </tr>
</table>
<br>
<table width="500" border="0" align="center" cellpadding="1" cellspacing="0">
   <tr> 
      <td align="left" valign="top" bgcolor="#333333"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr align="left" valign="top"> 
               <td width="20%" bgcolor="#EEEEEE"><table width="100%" border="0" cellspacing="0" cellpadding="3">
                     <tr align="left" valign="top"> 
                        <td bgcolor="#EEEEEE">Order Number:</td>
                        <td bgcolor="#EEEEEE"> 
                           <?=$_POST[txn_id]?>
                        </td>
                     </tr>
                     <tr align="left" valign="top"> 
                        <td bgcolor="#EEEEEE">Date:</td>
                        <td bgcolor="#EEEEEE"> 
                           <?=$_POST[payment_date]?>
                        </td>
                     </tr>
                     <tr align="left" valign="top"> 
                        <td width="20%" bgcolor="#EEEEEE"> First Name: </td>
                        <td width="80%" bgcolor="#EEEEEE"> 
                           <?=$_POST[first_name]?>
                        </td>
                     </tr>
                     <tr align="left" valign="top"> 
                        <td bgcolor="#EEEEEE">Last Name:</td>
                        <td bgcolor="#EEEEEE"> 
                           <?=$_POST[last_name]?>
                        </td>
                     </tr>
                     <tr align="left" valign="top"> 
                        <td bgcolor="#EEEEEE">Email:</td>
                        <td bgcolor="#EEEEEE"> 
                           <?=$_POST[payer_email]?>
                        </td>
                     </tr>
                  </table></td>
            </tr>
         </table></td>
   </tr>
</table>
<br>
</body>
</html>
