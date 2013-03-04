<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_HOME_PAGE_PROFILES extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		SHARED_OBJECT::getObjectFromPackage(constant('__APPLICATION_ROOT__') . "::HYBERNATE::OBJECTS::PATIENT_PROFILES::PATIENT_PROFILES");
		
		$this->Application 		 			= APPLICATION::getInstance();
		$this->strApplicationStaticResPath 	= $this->Application->getBaseStaticResourcePath();
		$this->strCanonicalUrl 				= URL::getCanonicalUrl('patient-profiles', false, false, true);
		$this->arrPatientProfiles			= PATIENT_PROFILES::getItemObjectClassView(array(
			'imagePositionId' => array(14, 15),
			'orderBy'		  => 'RAND()',
			'limit'			  => 10	
		), false);

		$this->blnIsAdmin = (true === $this->Application->getUser()->fitsInRole(SITE_USERS_ROLE_ADMIN_USER));
	}
	
	public function render()
	{
		if (false === empty($this->arrPatientProfiles)) {
?>
<h5 class="title">
	<a href="<?php echo($this->strCanonicalUrl); ?>" style="color:#004E84">
		<?php echo($this->Application->translate('Patient Profiles', 'Profils de Patients')); ?></a>
</h5>
	<div class="carousel-content"><ul class="slides">
<?php
			reset ($this->arrPatientProfiles);
			while (list($intIndex, $arrProfileData) = each($this->arrPatientProfiles)) 
			{
				$strFrNameUrl	 = str_replace(' ', '_', ucwords($arrProfileData['name']));
				$strFrNameUrl	 = preg_replace('/[^A-Za-z0-9\s_]/', '', $strFrNameUrl);
				$strProfileLink  = $this->strCanonicalUrl . '/' . $strFrNameUrl;
				$strProfileLink .= ':' . (int) $arrProfileData['id'];
?>
    <li>
        <div class="team_wrap patient-profile homepage_patient_profiles">
        	<div class="profile_image_container">
            <a href="<?php echo($strProfileLink); ?>" class="img-container" title="<?php echo($this->Application->translate(
					'Click here to read ' . ucwords($arrProfileData['name']) . ' profile', 
					'Cliquez ici pour lire le profil a ' . ucwords($arrProfileData['name']))); ?>">
				<?php if (false === empty($arrProfileData['imagePosition15'])) { ?>
					<?php $arrPostImages = explode(',', $arrProfileData['imagePosition14']); ?>
					<?php $arrPostImages = array_splice($arrPostImages, 0, 1); // Show only the first image ?>
						<?php if(count($arrPostImages) > 1) { ?>
							<div class="post-slide">	
								<?php while (list($intIndex, $strImageUrl) = each($arrPostImages)) { ?>
									<?php if (false === empty($strImageUrl)) { ?>
										<div data-src="<?php echo($strImageUrl); ?>"></div>
									<?php } ?>
								<?php } ?>	
							</div>
						<?php } else { ?>		   
							<img src="<?php echo($arrPostImages[0]); ?>" alt="" />
						<?php } ?>
					</a>
				<?php } else { ?>
					<img src="<?php echo($this->strApplicationStaticResPath); ?>/images/sample_images/team<?php echo(number_format(mt_rand(1, 6), 0)); ?>.jpg" alt="" />
				<?php } ?>
			</a></div>
            <div class="box-blue">
                <h5><?php echo($arrProfileData['name']); ?></h5>
                <p class="patient-profile-desc">
                   	<?php echo(substr(strip_tags($arrProfileData['content']), 0, 70)); ?>
					<?php echo(strlen(strip_tags($arrProfileData['content'])) > 70 ? '...' : ''); ?>
                </p>
                <ul class="socials-list">
                    <li><a href="#" title="facebook" class="tooltip"><img src="/static/images/socials/facebook.gif" alt=""></a></li>
                    <li><a href="#" title="twitter" class="tooltip"><img src="/static/images/socials/twitter.gif" alt=""></a></li>
                </ul>
                <span class="float-left white"><?php echo($arrProfileData['comment_count']); ?> <?php echo($this->Application->translate('Comments', 'Commentaires')); ?></span>
				<?php if (true === $this->blnIsAdmin) { ?>
                    <a class="button small green"  style="left:0px;"  
                        href="<?php echo($this->strCanonicalUrl . '/manage/profile-id:' . ((int) $arrProfileData['id'])); ?>">
                        <?php echo($this->Application->translate('Edit', 'Modifier')); ?>
                    </a>
                    <a class="button small red float-left" id="delete_profile" href="#" style="margin-right:90px;">
                        <?php echo($this->Application->translate('Delete', 'Supprimer')); ?>
                    </a>
                <?php } ?>
                <a href="<?php echo($strProfileLink); ?>" class="dark_blue button small float-rightt"><?php echo($this->Application->translate('Read More', 'En Lire Plus')); ?></a>
            </div>
        </div>
    </li>
<?php
			}
?>
	</ul></div>
<?php			
		}
	}
}