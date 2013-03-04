<?php
	$Application 			= $this->getApplication();
	$strStaticResourcePath 	= $Application->getStaticResourcePath();
	
	
	$this->renderPartial('SCRIPTS::SCRIPT_INCLUDES', $this->getRequestData());
	$this->renderPartial('IO::PAGE_PRELOADER', $this->getRequestData());	
?>

<div class="loginContainer reset">
	<div class="header">
		<a href="<?php echo(constant('__ROOT_URL__')); ?>" title="<?php echo(constant('__SITE_NAME__') . ' - ' . constant('__SITE_TITLE__')); ?>" class="login_logo"></a>
		<h2><?php echo($Application->translate('Reset your Password', 'Réinitialiser mot de passe')); ?></h2>
		<a href="#" class="settings"></a>
		<a href="#" class="home"></a>
	</div>
	<form id="loginForm" name="loginForm" action="<?php echo($this->getViewData('postDataUrl')); ?>" method="post">
		<div class="login_row">
			<p><?php echo($Application->translate(
				'Enter your new password in the 2 fields below.',
				'Entrez votre nouveau mot de passe ci-dessous'
			)); ?></p>
		</div>	
		<div class="login_row">
			<img src="/static/images/key.png" class="person" />
			<input type="password" name="user_password" id="user_password"  
					value="<?php echo($this->getRequestParam('user_password')); ?>"  
					placeholder="<?php echo($Application->translate('Your Password.', 'Votre mot de passe.')); ?>" />
		</div>
		<div class="login_row">
			<img src="/static/images/key.png" class="person" />
			<input type="password" name="user_password2" id="user_password2" 
					value="<?php echo($this->getRequestParam('user_password2')); ?>" 
					placeholder="<?php echo($Application->translate('Your Password again.', 'Votre mot de passe à nouveau.')); ?>" />
		</div>
		<div class="submit_row">
			<div class="login_button">
				<input type="submit" value="<?php echo($Application->translate('Send', 'Envoyer')); ?>"  class="button large blue buttonShadow" />
			</div>
		</div>	
	</form>	
	
	
	<div class="login_footer">
		<div class="login_footer_container">
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

