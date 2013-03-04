<?php
	// Get the available admin modules...
	$Application = ADMIN_APPLICATION::getInstance();
	$arrAdminModules = $Application->getAdminModules();
	
	if (FALSE === empty($arrAdminModules)) 
	{
?>
    <div class="leftNav">
        <ul id="menu">
<?php   	
		reset($arrAdminModules);
		while (list($strModuleName, $arrModuleData) = each($arrAdminModules)) 
		{
			$objMenuUrl = new URL();
			$objMenuUrl->setPath('/admin/' . strtolower($arrModuleData['CONTROLLER_PATH']));
			$objMenuUrl->addSessionAttributes();
			
			// Here, the module's controller should already be included by admin_application because that is how
			// the names and url's were extracted. So we can go ahead and use the class
			$objClassName 	= $arrModuleData['CLASS_NAME']; 
			$arrSubMenus	= call_user_func_array(array($objClassName, 'getSubMenuActions'), array());
			
			?>
			<li class="<?php echo strtolower($arrModuleData['CLASS_NAME']); ?>">
				<a 	class="<?php echo  (TRUE === $arrModuleData['IS_CURRENT'] ? 'active' : ''); ?> rightDir" 
					original-title="<?php echo $arrModuleData['DISPLAY_NAME']; ?>"
					title="<?php echo $arrModuleData['DISPLAY_NAME']; ?>" 
					href="<?php echo (TRUE === $arrModuleData['IS_CURRENT'] ? '' : $objMenuUrl->build()); ?>">
					<span><?php echo $arrModuleData['DISPLAY_NAME']; ?></span>
					<?php if (FALSE === empty($arrSubMenus)) { ?>
						<span class="numberLeft"><?php echo count($arrSubMenus); ?></span>
					<?php } ?>	
				</a>
				<?php if (FALSE === empty($arrSubMenus)) { ?>
					<ul class="sub" id="<?php echo (TRUE === $arrModuleData['IS_CURRENT'] ? 'active_sub_menu' : '')?>">
						<?php 
							reset($arrSubMenus);
							$intCount = 1;
							$intTotal = count($arrSubMenus);
							while (list($intSubIndex, $arrSubMenuData) = each($arrSubMenus)) 
							{
								$strDisplayName	 = (true === isset($arrSubMenuData['name']) 	? $arrSubMenuData['name'] : '');
								$strSubMenuClass = (true === isset($arrSubMenuData['class']) 	? $arrSubMenuData['class'] : '');
								$strSubMenuUrl 	 = (true === isset($arrSubMenuData['url']) 		? $arrSubMenuData['url'] : '');
								
								// Clone the sub menu URL from the parent URL if the menu url is not defined 
								$objSubMenuUrl	 = new URL($strSubMenuUrl ? $strSubMenuUrl : $objMenuUrl->build());
								$strSubMenuUrl	 = $objSubMenuUrl->build();
								$strSubMenuClass .= ($intCount == $intTotal ? ' last': '');	
								$intCount++;
						?>
						<li class="<?php echo $strSubMenuClass; ?>">
							<a title="<?php echo $strDisplayName; ?>" href="<?php echo $strSubMenuUrl; ?>"><?php echo $strDisplayName; ?></a>
						</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</li>	
<?php 
		} 
?>
        </ul>
      </div>
<?php }  // End if ?>	

<?php /*
<div class="leftNav">
	<ul id="menu">
	 <li class="dash"><a class="active" title="" href="index.html"><span>Dashboard</span></a></li>
	  <li class="graphs"><a title="" href="charts.html"><span>Graphs and charts</span></a></li>
	  <li class="forms"><a title="" href="form_elements.html"><span>Form elements</span></a></li>
	  <li class="login"><a title="" href="ui_elements.html"><span>Interface elements</span></a></li>
	  <li class="typo"><a title="" href="typo.html"><span>Typography</span></a></li>
	  <li class="tables"><a title="" href="tables.html"><span>Tables</span></a></li>
	  <li class="cal"><a title="" href="calendar.html"><span>Calendar</span></a></li>
	  <li class="gallery"><a title="" href="gallery.html"><span>Gallery</span></a></li>
	  <li class="widgets"><a title="" href="widgets.html"><span>Widgets</span></a></li>
	  <li class="files"><a title="" href="file_manager.html"><span>File manager</span></a></li>
	  <li class="errors"><a class="exp" title="" href="#"><span>Error pages</span><span class="numberLeft">6</span></a>
		<ul class="sub">
		  <li><a title="" href="403.html">403 page</a></li>
		  <li><a title="" href="404.html">404 page</a></li>
		  <li><a title="" href="405.html">405 page</a></li>
		  <li><a title="" href="500.html">500 page</a></li>
		  <li><a title="" href="503.html">503 page</a></li>
		  <li class="last"><a title="" href="offline.html">Website is offline</a></li>
		</ul>
	  </li>
	  <li class="pic"><a title="" href="icons.html"><span>Buttons and icons</span></a></li>
	  <li class="contacts"><a title="" href="contacts.html"><span>Organized contact list</span></a></li>
  </ul>
</div>
*/ ?>