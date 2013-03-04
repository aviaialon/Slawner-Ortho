<?php
	$Application 	= $this->getApplication();
	$strApplicationStaticResPath = $Application->getBaseStaticResourcePath();
	$objPageBlocks 	= ($Application->getPageBlocks());	
	$intLangId 		= ($Application->translate(1, 2));
	$arrAttachments	= $this->getViewData('uploaded-attachments');
	
	$this->renderPartial('SCRIPTS::SCRIPT_INCLUDES', $this->getRequestData());
	$this->renderPartial('IO::PAGE_PRELOADER', $this->getRequestData());
	$this->renderPartial('MENU::FONT_RESIZER', $this->getRequestData());
?>
<div id="main-wrapper">
	<?php 
		$this->renderPartial('MENU::TOP_MENU', array(
			'menuGroupId'	=> 	(int) $intLangId,
			'menuAttribute'	=>	'id="menu"'
		)); 
	?>
	
	<section id="pagetitle-wrapper">
    	<div class="row">
        	<div class="twelve columns content" data-content-id="9">
            	<?php echo($objPageBlocks->getBlockGroup(17)->getExecutedBlockContent()); ?>
            </div>
            <div class="twelve columns">
            	<div id="breadcrumb">
                	<ul>
                        <li><a href="/">
                        	<img src="<?php echo($strApplicationStaticResPath); ?>images/breadcrumb_home.png" alt="" /></a></li>
                        <li class="current-page"><a href="#"><?php echo($Application->translate('Our History', 'Notre Histoire')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>    	       
    </section>
    
		
	<section id="content-wrapper">
        <div class="row">
            <div class="twelve columns">
                <div id="timeline"></div>
            </div>
        </div>
    </section>
    
    <div class="row">
		<div class="twelve columns">
			<div class="featured-box content"  data-content-id="6">
				 <?php echo($objPageBlocks->getBlockGroup(6)->getExecutedBlockContent()); ?>
			</div>
		</div>
	</div>
	
	
    <?php $this->renderPartial('MENU::FOOTER', $this->getRequestData()); ?>
</div>
<?php $this->renderPartial('NOTIFICATION::MESSAGES', $this->getRequestData()); ?>
<?php $this->renderPartial('MODULES::CALL::CALL_MODULE', $this->getRequestData()); ?>
</body>
</html>