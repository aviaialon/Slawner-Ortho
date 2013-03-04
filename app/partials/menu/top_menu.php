<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_TOP_MENU extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		SHARED_OBJECT::loadSharedObject('MENU::MENU');
		
		$this->Application 			 		= APPLICATION::getInstance();
		$this->canonicalurl 				= URL::getCanonicalUrl(NULL, true, false, true);
		$this->objUser				 		= $this->Application->getUser();
		$this->strApplicationStaticResPath 	= $this->Application->getBaseStaticResourcePath();
		$this->strWelcomeText				= $this->Application->translate(
			'Welcome, ' . ($this->objUser->isLoggedIn() ? substr($this->objUser->getUserName(), 0, 4) . (strlen($this->objUser->getUserName()) > 4 ? '...' : '') : 'Guest'), 
			'Bienvenu ' . ($this->objUser->isLoggedIn() ? substr($this->objUser->getUserName(), 0, 4) . (strlen($this->objUser->getUserName()) > 4 ? '...' : '') : 'Visiteur') 
		);
		$this->strLongWelcomeText			= $this->Application->translate(
			'Welcome, ' . ($this->objUser->isLoggedIn() ? $this->objUser->getUserName() : 'Guest'), 
			'Bienvenu ' . ($this->objUser->isLoggedIn() ? $this->objUser->getUserName() : 'Visiteur') 
		);
		$this->strSiteMenuHtml				= MENU::getSiteMenuHtml(
			(int) $arrparameters['menuGroupId'],
			(isset($arrparameters['menuAttribute']) ? $arrparameters['menuAttribute'] : null),
			($this->canonicalurl)
		);
		$this->blnIsAdmin = (
			(bool) (($this->Application->getUser()->isLoggedIn()) &&
			($this->Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER)))
		);
		
		$this->extraLinks = (true === isset($arrparameters['extraLinks']) ? $arrparameters['extraLinks'] : array());
	}
	
	public function render()
	{
?>
	
	<div class="header-switch hborder" id="header-switch"> 
		<span class="trigger_switch" title="<?php echo($this->strLongWelcomeText); ?>">
			<b class="trigger" id="0" title="<?php echo($this->strLongWelcomeText); ?>">
				<i class="button-icon user icon">&nbsp;</i> 
				<?php echo($this->strWelcomeText); ?>
			</b> 
			<span class="current">&nbsp;</span> 
		</span>
	</div>
	<div class="header-switch hborder" id="header-switch-expanded"> 
		<span class="trigger_switch" title="<?php echo($this->strLongWelcomeText); ?>">
			<b class="trigger" id="0" title="<?php echo($this->strLongWelcomeText); ?>">
				<i class="button-icon user icon">&nbsp;</i> 
				<?php echo($this->strWelcomeText); ?>
			</b>
			<span class="current">&nbsp;</span>
		</span>
		<div class="header-dropdown"><ul>
			<?php
				$strExtraLinksHtml = (false === empty($this->extraLinks) ? '<li class="sep"></li>' : '');
				foreach($this->extraLinks as $intIndex => $arrLinkAttributes) {
					$strExtraLinksHtml .= ('<li>');	
						$strExtraLinksHtml .= ('<a');
							foreach($arrLinkAttributes as $strAttributeName => $strAttributeValue) {
								if ($strAttributeName !== 'text') {
									$strExtraLinksHtml .= (' ' . $strAttributeName . '="' . $strAttributeValue . '"');	
								}
							}
						$strExtraLinksHtml .= ('>' . $arrLinkAttributes['text'] . '</a>');	
					$strExtraLinksHtml .= ('</li>');	
				}
			?>
			<?php if ($this->objUser->isLoggedIn()) { ?>
				<li><a href="<?php echo($this->objUser->getProfileUrl()); ?>"><i class="icon icon-edit"></i> <?php echo($this->Application->translate('Account', 'Votre Compte')); ?></a></li>
				<li><a href="<?php echo($this->objUser->getHistoryUrl()); ?>"><i class="icon icon-pencil"></i> <?php echo($this->Application->translate('History', 'Historique')); ?></a></li>
				<?php if (true === $this->blnIsAdmin) { ?>
					<li class="sep"></li>
					<li><a href="#" rel="edit-sections"><i class="icon icon-edit"></i> <?php echo($this->Application->translate('Edit Sections', 'Modifier les Sections')); ?></a></li>
					<li><a href="#" rel="save-sections"><i class="icon icon-ok"></i> <?php echo($this->Application->translate('Save Sections', 'Saufgarder les Sections')); ?></a></li>
				<?php } ?>
				<?php echo($strExtraLinksHtml); ?>
				<li class="sep"></li>
				<li><a href="<?php echo($this->objUser->getLogoutUrl()); ?>"><i class="icon logoff"></i> <?php echo($this->Application->translate('Logout', 'Déconnexion')); ?></a></li>
			<?php } else { ?>
				<?php echo($strExtraLinksHtml); ?>
				<li><a href="<?php echo($this->objUser->getLoginUrl()); ?>"><i class="icon icon-edit"></i> <?php echo($this->Application->translate('Login', 'Connexion')); ?></a></li>
				<li><a href="<?php echo($this->objUser->getSignupUrl()); ?>"><i class="icon icon-pencil"></i> <?php echo($this->Application->translate('Register', 'S\'enregistrer')); ?></a></li>
			<?php } ?>
		</ul></div>
	</div>
	
	<!--<div class="lang">
		<a href="#">EN</a> / <a href="#">FR</a>
	</div>-->
    <!-- header start here -->
	<header>
    	<div id="top_wrapper">
            <div class="row">
                <div class="four column logo">
                    <a href="<?php echo(constant('__ROOT_URL__')); ?>" title="<?php echo(constant('__SITE_NAME__') . ' - ' . constant('__SITE_TITLE__')); ?>">
						<img src="<?php echo($this->strApplicationStaticResPath); ?>/images/logo-clear.png" alt="<?php echo(constant('__SITE_NAME__')); ?>"/></a>
                </div>
                <div class="eight column top_search">
                	<form id="search" action="#" method="get">
                        <fieldset class="search-fieldset2">
                        	<button type="submit" value="Submit" id="search-submit">Submit</button>
                            <input type="text" id="search-form"  ajax-url="/search" value="Search <?php echo(constant('__SITE_NAME__')); ?>" 
								placeholder="Search <?php echo(constant('__SITE_NAME__')); ?>" x-webkit-speech="x-webkit-speech" />
                        </fieldset>      						
                    </form>
                </div>
            </div>
        </div>
        
        <div id="mainmenu_wrapper">
        	<!-- mainmenu start here -->
            <div class="menu">
				<?php echo ($this->strSiteMenuHtml); ?>	
			</div>
            <!-- mainmenu end here -->
            
            <!-- top socials start here -->
            <div id="top-socials">
                <ul class="socials-list">
                    <li><a href="<?php echo(constant('__FACEBOOK_PAGE_URL__')); ?>" title="<?php echo($this->Application->translate('Follow us on Facebook', 'Suivez-nous sur Facebook')); ?>" class="tooltip" target="_blank">
						<img src="<?php echo($this->strApplicationStaticResPath); ?>/images/socials/facebook.gif" alt="" /></a></li>
                    <li><a href="#" title="twitter" class="tooltip"><img src="<?php echo($this->strApplicationStaticResPath); ?>/images/socials/twitter.gif" alt="" /></a></li>
                    <!--<li><a href="#" title="rss" class="tooltip"><img src="<?php echo($this->strApplicationStaticResPath); ?>/images/socials/rss.gif" alt="" /></a></li>-->
                    <li><a href="#" title="youtube" class="tooltip"><img src="<?php echo($this->strApplicationStaticResPath); ?>/images/socials/youtube.gif" alt="" /></a></li>
                </ul>
            </div>
			
			<div class="lang-selector-view top"> 
				<a class="select">
					<?php /* echo($this->Application->translate('us', 'ca')); */ ?>
					<span><img src="<?php echo($this->strApplicationStaticResPath); ?>images/selector/ca_small.png" width="18" height="14"  /> 
						<strong><?php echo($this->Application->translate('English', 'Français')); ?></strong></span>
				</a>
				<div class="popover">
					<div class="arrow"></div>
					<h4><?php echo($this->Application->translate('Select your Language', 'Choisissez votre language')); ?></h4>
					<ul>
						<li><a href="<?php echo($this->Application->getEnglishCanonicalUrl()); ?>"<?php echo($this->Application->translate(' class="selected"', '')); ?>>
							<img src="<?php echo($this->strApplicationStaticResPath); ?>images/selector/ca_big.png" width="34" height="25" /> <span>English</span></a></li>
						<li><a href="<?php echo($this->Application->getFrenchCanonicalUrl()); ?>"<?php echo($this->Application->translate('', ' class="selected"')); ?>>
							<img src="<?php echo($this->strApplicationStaticResPath); ?>images/selector/ca_big.png" width="34" height="25" /> <span>Français</span></a></li>
					</ul>
				</div>
				<div class="lang-selector-overlay"></div>
			</div>
			<!-- top socials end here 
			<a href="#" class="live-call"></a>-->
        </div>          
    </header>
    <!-- header end here -->
<?php	
	}
	
	/**
	 * OVEVRRIDE: This method outputs the partial's signature as a comments
	 */
	public static function signature()
	{
		echo ("\n <!-- partial: " . str_replace('PARTIAL_', '', __CLASS__) . " | cached: " . (MENU::isCached() ? 'true' : 'false') . " --> \n");
	}
}