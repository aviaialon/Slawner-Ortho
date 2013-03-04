<?php
	$Application 			= $this->getApplication();
	$strStaticResourcePath 	= $Application->getStaticResourcePath();
	
	
	$this->renderPartial('SCRIPTS::SCRIPT_INCLUDES', $this->getRequestData());
	$this->renderPartial('IO::PAGE_PRELOADER', $this->getRequestData());	
?>

<div class="loginContainer">
	<div class="header">
		<a href="<?php echo(constant('__ROOT_URL__')); ?>" title="<?php echo(constant('__SITE_NAME__') . ' - ' . constant('__SITE_TITLE__')); ?>" class="login_logo"></a>
		<h2><?php echo($Application->translate('Login to your account', 'Connection à votre compte')); ?></h2>
		<a href="#" class="settings"></a>
		<a href="#" class="home"></a>
	</div>
	<form id="loginForm" name="loginForm" action="<?php echo($this->getViewData('userLoginUrl')); ?>" method="post">
		<input type="hidden" name="__LOGIN__" value="<?php echo($this->getViewData('userLoginToken')); ?>" />
		<input type="hidden" name="ref" value="<?php echo($this->getRequestParam('ref')); ?>" />
		<div class="login_row">
			<img src="/static/images/person.png" class="person" />
			<input type="text" name="user_login" id="username" value="<?php echo($Application->getUser()->getCookieUserName()); ?>" placeholder="<?php echo($Application->translate('Username', 'Nom d\'utilisateur')); ?>" />
		</div>
		<div class="login_row">
			<img src="/static/images/key.png" class="key" />
			<input type="password" autocomplete="off" name="user_password" id="password" placeholder="<?php echo($Application->translate('Password', 'Mot de passe')); ?>" value="" />
		</div>
		<div class="submit_row">
			<div class="remember_me">
				<input type="checkbox" name="remember" id="remember" checked="checked" />
				<label for="remember"><?php echo($Application->translate('Remember me on this computer.', 'Souvient toi de moi.')); ?></label>
			</div>
			<div class="login_button">
				<input type="submit" value="login"  class="button large blue buttonShadow" />
			</div>
		</div>	
	</form>	
	<!--<div class="login_footer_alt">
		<div class="login_footer_container">
			<p>No worries, <a href="#">click here</a> to reset your password</p>
		</div>
	</div>-->
	<div class="social_login">
		<div class="social_login_inner">
			<a href="<?php echo($Application->getUser()->getFacebookLoginUrl()); ?>" tabindex="2" title="Login Via Facebook" class="tooltip">
				<img src="<?php echo($strStaticResourcePath); ?>images/facebook-icon.gif" border="0">
			</a>
			
			<a href="<?php echo($Application->getUser()->getGoogleLoginUrl()); ?>" tabindex="2" title="Login Via Google" class="tooltip">
				<img src="<?php echo($strStaticResourcePath); ?>images/google-plus-icon.png" border="0">
			</a>
			
			<?php echo($this->getViewData('payPalOAuthData')); ?>
			
			<a href="<?php echo($Application->getUser()->getTwitterLoginUrl()); ?>" tabindex="2" title="Login Via Twitter" class="tooltip">
				<img src="<?php echo($strStaticResourcePath); ?>images/twitter_icon.png" border="0">
			</a>
			
			<a href="<?php echo($Application->getUser()->getLinkedInLoginUrl()); ?>" tabindex="2" title="Login Via LinkedIn" class="tooltip">
				<img src="<?php echo($strStaticResourcePath); ?>images/linkedin-icon.png" border="0">
			</a>
		</div>
	</div>
	<div class="login_footer">
		<div class="login_footer_container">
			<h3><?php echo($Application->translate('Forgot your password?', 'Oublié ton mot de passe?')); ?></h3>
			<p><?php echo($Application->translate('No worries, ', 'Pas de soucis, ')); ?>
			<a href="<?php echo($Application->getUser()->getPassReminderUrl()); ?>"><?php echo($Application->translate('click here', 'cliquez ici')); ?></a> 
				<?php echo($Application->translate('to reset your password', 'pour réinitialiser ton mot de passe')); ?></p>
			<a href="#" rel="live-call"><img src="<?php echo($strStaticResourcePath); ?>images/live_support.png" /></a>
			<div class="lang-selector-view"> 
				<a class="select">
					<span><img src="<?php echo($strStaticResourcePath); ?>images/selector/<?php echo($Application->translate('us', 'ca')); ?>_small.png" width="18" height="14"  /> 
						<strong><?php echo($Application->translate('English', 'Français')); ?></strong></span>
				</a>
				<div class="popover">
					<div class="arrow"></div>
					<h4><?php echo($Application->translate('Select your Language', 'Choisissez votre language')); ?></h4>
					<ul>
						<li><a href="<?php echo($Application->getEnglishCanonicalUrl()); ?>"<?php echo($Application->translate(' class="selected"', '')); ?>>
							<img src="<?php echo($strStaticResourcePath); ?>images/selector/us_big.png" width="34" height="25" /> <span>English</span></a></li>
						<li><a href="<?php echo($Application->getFrenchCanonicalUrl()); ?>"<?php echo($Application->translate('', ' class="selected"')); ?>>
							<img src="<?php echo($strStaticResourcePath); ?>images/selector/ca_big.png" width="34" height="25" /> <span>Français</span></a></li>
					</ul>
				</div>
				<div class="lang-selector-overlay"></div>
			</div>
		</div>
	</div>
</div>
<?php $this->renderPartial('NOTIFICATION::MESSAGES', $this->getRequestData()); ?>
</body>
</html>

