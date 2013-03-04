<?php 
	$Application = ADMIN_APPLICATION::getInstance();
?>
<div class="breadCrumbHolder module">
	<div class="breadCrumb module">
		<ul>
			<li class="firstB"><a href="/admin">Home</a> </li>
			<?php 
				if (
					($Application->getRequest_Controller()) &&
					($Application->getRequest_Controller() !== ADMIN_APPLICATION::ADMIN_APPLICATION_DEFAULT_CONTROLLER)
				) {
					$objCrumbUrl 	= $Application->newActionUrl(array()); 
					$strCrumbName	= ucwords(
						str_replace('_', ' ', $Application->getRequestDispatcher()->neutralise($Application->getRequest_Controller()))
					);
			?>
				<li><a href="<?php echo ($objCrumbUrl->build()); ?>"><?php echo $strCrumbName; ?></a></li>
			<?php 
				} 

				if (
					($Application->getRequest_Action()) &&
					($Application->getRequest_Action() !== ADMIN_APPLICATION::ADMIN_APPLICATION_DEFAULT_ACTION)
				) {
					$objCrumbUrl 	= $Application->newActionUrl(array($Application->getRequest_Action())); 
					$strCrumbName	= ucwords(
						str_replace('_', ' ', $Application->getRequestDispatcher()->neutralise($Application->getRequest_Action()))
					);
			?>
				<li><a href="<?php echo ($objCrumbUrl->build()); ?>"><?php echo $strCrumbName; ?></a></li>
			<?php 
				} 

				if (true == ((bool) count($Application->getRequest_Params()))) 
				{
					$arrParams 		= (array) $Application->getRequest_Params();
					$strCrumbName	= ucwords(
						str_replace('_', ' ', $Application->getRequestDispatcher()->neutralise($arrParams[0]))
					);
					
			?>
				<li><?php echo $strCrumbName; ?></li>
			<?php } ?>
		</ul>
	</div>
</div>