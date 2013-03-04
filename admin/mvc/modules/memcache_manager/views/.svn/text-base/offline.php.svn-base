<?php 
	$Application = ADMIN_APPLICATION::getInstance();
	
	$objDashBoardUrl = new URL();
	$objDashBoardUrl->setPath('/admin');
	$objDashBoardUrl->clearAttribute();
	$objDashBoardUrl->setAttribute('authToken', $Application->getForm()->getUrlParam('authToken'));
?>
<div class="errorPage" style="margin-top:50px">
	<h2 class="red aligncenter weAreOff"><span>Sorry, but the server is currently </span></h2>
	<h1>offline</h1>
	<p>Seems like we're off. We'll get back to you very soon. Please, visit us later.</p>
	<br />
	<div class="backToDash">
		<!--<a href="<?php echo $objDashBoardUrl->build(); ?>" title="" class="seaBtn button">Back to Dashboard</a>-->
		<a href="<?php echo $objDashBoardUrl->build(); ?>" title="" class="btnIconLeft mr10">
			<img src="/admin/static/images/icons/dark/laptop.png" alt="" class="icon"><span>Back to Dashboard</span>
		</a>
		<a href="<?php echo $Application->getCurrentUrl(); ?>" title="" class="btnIconLeft mr10">
			<img src="/admin/static/images/icons/dark/refresh.png" alt="" class="icon"><span>Try Again</span>
		</a>
	</div>
</div>