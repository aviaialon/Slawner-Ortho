<?php
include 'EpiCurl.php';
include 'EpiOAuth.php';
include 'EpiTwitter.php';
$consumer_key = 'LKNXbJSZI9q5vabn4gbhOw';
$consumer_secret = 'cPgQ2vvZ8VY95c5uCsNCgQksmZyXNA6kc87NqOf77M';
$objTwitterOAuth = new EpiTwitter($consumer_key, $consumer_secret);  	

if (! isset($_GET['oauth_token'])) {
	$objTwitterAuthURL = new URL($objTwitterOAuth->getAuthenticateUrl());  
	URL::redirect($objTwitterAuthURL->build());
} else {
	try {
		$objTwitterOAuth->setToken($_GET['oauth_token']);  
		$objAuthToken = $objTwitterOAuth->getAccessToken();  
		$objTwitterOAuth->setToken(
			$objAuthToken->oauth_token, 
			$objAuthToken->oauth_token_secret
		);  
		// Get the user info
		$objUserInfo = $objTwitterOAuth->get_accountVerify_credentials();  
		
		// Error Here....
		
		if (
			(isset($objUserInfo->error)) &&
			(! is_null($objUserInfo->error))
		) {
			print "<h1>OAUTH ERROR</h1>";
			new dump($objUserInfo->error);	
		}
		
		new dump($objUserInfo);
		setcookie('oauth_token', $objAuthToken->oauth_token);  
		setcookie('oauth_token_secret', $objAuthToken->oauth_token_secret);  
	} 
	//
	// Catch EpiTwitter Exception, possilbly due to session conflicts.
	//
	catch(EpiOAuthUnauthorizedException  $EpiUnauthorizedExcpetion) {
		$intExceptionsNum = ($objForm->getOrPost('exp') ? (int) $objForm->getOrPost('exp') : 1);
		// Give it 3 tries and go to hell
		if ($intExceptionsNum <= 3) {
			// Clear the session
			SESSION::destroySession();
			$objRedirectUrl = new URL(
				$objUser->getTwitterLoginUrl()
			);
			$objRedirectUrl->clearAttribute();
			$objRedirectUrl->addSessionAttributes();
			$objRedirectUrl->setAttribute('exp', ++$intExceptionsNum);
			URL::redirect(
				$objRedirectUrl->build()
			);
		} else {
			$objRedirectUrl = new URL(__SITE_ROOT__);
			$objRedirectUrl->setAttribute('err', 'Sorry, twitter login seemed to get stuck. Please try again later or use a different login method.');
		}
	} 
	//
	// Catch EpiTwitter Exception, possilbly due to session conflicts.
	//
	catch(EpiTwitterException $EpiSessionExcpetion) {
		if (! class_exists('SITE_EXCEPTION')) {
			SHARED_OBJECT::getObjectFromPackage("CLASSES::EXCEPTION::SITE_EXCEPTION");	
		}
		SITE_EXCEPTION::throwNewException($e);
	} 
	//
	// Catch All Hell Breaks Loose Exceptions, due my my fuck up! ;)
	//
	catch (Exception $e) {
		if (! class_exists('SITE_EXCEPTION')) {
			SHARED_OBJECT::getObjectFromPackage("CLASSES::EXCEPTION::SITE_EXCEPTION");	
		}
		SITE_EXCEPTION::throwNewException($e);
	}
}
die;
?>










<?php
include 'EpiCurl.php';
include 'EpiOAuth.php';
include 'EpiTwitter.php';
$consumer_key = 'LKNXbJSZI9q5vabn4gbhOw';
$consumer_secret = 'cPgQ2vvZ8VY95c5uCsNCgQksmZyXNA6kc87NqOf77M';


/*

request token
http://twitter.com/oauth/request_token
Access token URL
http://twitter.com/oauth/access_token
Authorize URL
http://twitter.com/oauth/authorize


$token = '25451974-uakRmTZxrSFQbkDjZnTAsxDO5o9kacz2LT6kqEHA';
$secret= 'CuQPQ1WqIdSJDTIkDUlXjHpbcRao9lcKhQHflqGE8';
*/
$twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $token, $secret);
$twitterObjUnAuth = new EpiTwitter($consumer_key, $consumer_secret);
?>

<h1>Single test to verify everything works ok</h1>

<h2><a href="javascript:void(0);" onclick="viewSource();">View the source of this file</a></h2>
<div id="source" style="display:none; padding:5px; border: dotted 1px #bbb; background-color:#ddd;">
<?php highlight_file(__FILE__); ?>
</div>

<hr>

<h2>Generate the authorization link</h2>
<?php echo $twitterObjUnAuth->getAuthenticateUrl(); ?>

<hr>

<h2>Verify credentials</h2>
<?php
  $creds = $twitterObj->get('/account/verify_credentials.json');
?>
<pre>
<?php print_r($creds->response); ?>
</pre>

<hr>

<h2>Post status</h2>
<?php
  $status = $twitterObj->post('/statuses/update.json', array('status' => 'This a simple test from twitter-async at ' . date('m-d-Y h:i:s')));
?>
<pre>
<?php print_r($status->response); ?>
</pre>

<script> function viewSource() { document.getElementById('source').style.display=document.getElementById('source').style.display=='block'?'none':'block'; } </script>
