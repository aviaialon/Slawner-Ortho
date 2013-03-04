<?php 
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGE_META");
	SHARED_OBJECT::getObjectFromPackage(__SITE_ROOT__ . "::ADMIN::MVC::APPLICATION::ADMIN_APPLICATION");	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::COMPONENTS::MESSAGEBOX::ALERT_NOTIFICATIONS_V2");
	
	$Application = ADMIN_APPLICATION::getInstance();
	$Application->webControllerInitiate()->dispatchRequest();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php include_once('includes/js_files.php'); ?>
</head>
<body>
<div class="pgLoader" id="pageLoader">
	<div class="pgLoaderDesc">Loading data. Please wait...</div>
	<span class="pgLoaderExtra1"><!-- --></span>
</div>
<div id="page" style="display:none">
	<!-- Top navigation bar -->
	<div id="topNav">
	  <div class="fixed">
	    <div class="wrapper">
	      <div class="welcome"><a title="" href="#"><img alt="" src="http://www.gravatar.com/avatar/<?php echo(md5(trim($Application->getUser()->getEmail()))); ?>?default=<?php echo(__ROOT_URL__); ?>/admin/static/images/userPic.png" width="22" height="20"></a><span>Howdy, <?php echo($Application->getUser()->getUsername()); ?>!</span></div>
	      <div class="userNav">
	        <ul>
	          <li><a title="" href="#"><img alt="" src="/admin/static/images/icons/topnav/profile.png"><span>Profile</span></a></li>
	          <li><a title="" href="#"><img alt="" src="/admin/static/images/icons/topnav/tasks.png"><span>Tasks</span></a></li>
	          <li class="dd"><img alt="" src="/admin/static/images/icons/topnav/messages.png"><span>Messages</span><span class="numberTop">8</span>
	            <ul class="menu_body">
	              <li><a title="" href="#">new message</a></li>
	              <li><a title="" href="#">inbox</a></li>
	              <li><a title="" href="#">outbox</a></li>
	              <li><a title="" href="#">trash</a></li>
	            </ul>
	          </li>
	          <li><a title="" href="#"><img alt="" src="/admin/static/images/icons/topnav/settings.png"><span>Settings</span></a></li>
	          <li><a title="" href="?logout"><img alt="" src="/admin/static/images/icons/topnav/logout.png"><span>Logout</span></a></li>
	        </ul>
	      </div>
	      <div class="fix"></div>
	    </div>
	  </div>
	</div>
	<div class="colorSwitch"> <a title="Switch to dark verion" href="#"></a> </div>
	<div class="layoutSwitch"> <a title="Switch to fixed version" href="#"></a> </div>
	<!-- Header -->
	<div class="wrapper" id="header">
	  <div class="logo">
      	<?php if (FALSE === $Application->getSite()) { ?>
        	<a title="Go to <?php echo(__SITE_NAME__); ?>" href="/"><img alt="" src="/static/images/logo.png" style="position:absolute;top:50px; width:250px; background:#FFF; border:solid 3px #333"></a>
        <?php } else if ($Application->getSite()->getId()) { ?>
        	<a 	title="View <?php echo($Application->getSite()->getName()); ?>"  
                    href="http://<?php echo($Application->getSite()->getUrl()); ?>">
                        <h1 style="color: #222;text-shadow: 0px 2px 3px #555;font-size:30px;font-weight:900">
                            <?php echo(strtoupper($Application->getSite()->getName())); ?></h1></a>  
        <?php } ?>    
      </div>
	  <div class="middleNav">
	    <ul>
	      <li class="iMes"><a title="" href="#"><span>Support tickets</span></a><span class="numberMiddle">9</span></li>
	      <li class="iStat"><a title="" href="#"><span>Statistics</span></a></li>
	      <li class="iUser"><a title="" href="#"><span>User list</span></a></li>
	      <li class="iOrders"><a title="" href="#"><span>Billing panel</span></a></li>
	    </ul>
	  </div>
	  <div class="fix"></div>
	</div>
	<!-- Content wrapper -->
	<div class="wrapper">
	  <!-- Left navigation -->
	  <?php require_once('includes/side_menu.php'); ?>
	  <!-- Content -->
	  <div class="content">
		<?php 
	      	// Alert notifications 		
			ALERT_NOTIFICATIONS_V2::getInstance();
			
			$objActiveModule = $Application->getCurrentActiveModule();
	        if (FALSE === empty($objActiveModule)) { 
	        	$strModuleName = call_user_func_array(array($objActiveModule, 'getDisplayName'), array());
	    ?>
	    	<div class="title">
	        	<h5><?php echo($strModuleName); ?></h5>
	        </div>
	        
	        <!-- Breadcrumbs -->
	         <?php require_once('includes/breadcrumb.php'); ?>  
	        
	    	<!-- Module Output -->
	      	<?php 
	      		// Render the module output
	      		call_user_func_array(array($objActiveModule, 'renderOutput'), array(array(
					'Request_Data' 	=> $Application->getRequestData(),
					'View_Data'		=>  $Application->getViewData()
				)));
			?>
	        <!-- /Module Output EOF -->
	    <?php } ?> 
	  </div>
	  <!-- /Content EOF  -->
	  <div class="fix"></div>
	</div>
	<!-- Footer -->
	<div id="footer">
	  <div class="wrapper"> <span>&copy; Copyright 2011. All rights reserved. It's Brain admin theme by <a title="" href="#">Avi / Myriam Aialon</a></span> </div>
	</div>
</div>
</body>
<script type="text/javascript">
	<!--
	 // Just in case error handler
	 if (typeof window.onerror !== "function")
	 {
		 window.onerror = function()
			{
				$('#page').fadeIn('slow', function(event) {
					$('#pageLoader').hide();
				});

				return false;
			};	
	 }
	-->
</script>
</html>
