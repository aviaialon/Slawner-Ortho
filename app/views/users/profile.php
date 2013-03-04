<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $this->renderPartial('SCRIPTS::SCRIPT_INCLUDES', $this->getRequestData()); ?>
</head>

<body>
<!-- Top line begins -->
<?php $this->renderPartial('MENU::TOP_MENU', $this->getRequestData()); ?>
<!-- Top line ends -->


<!-- Sidebar begins -->
<div id="sidebar">
    <!-- Side Nav begins -->
	<?php $this->renderPartial('MENU::SIDE_MENU', $this->getRequestData()); ?>
	<!-- Side Nav ends -->

    
    <!-- Secondary nav -->
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                    <div class="balInfo">Balance:<span>Apr 21 2012</span></div>
                    <div class="balAmount"><span class="balBars"><!--5,10,15,20,18,16,14,20,15,16,12,10--></span><span>$58,990</span></div>
                </div>
                <a href="#" class="triangle-red"></a>
            </div>
            
            <!-- Tabs container -->
            <div id="tab-container" class="tab-container">
                <ul class="iconsLine ic3 etabs">
                    <li><a href="#general" title=""><span class="icos-fullscreen"></span></a></li>
                    <li><a href="#alt1" title=""><span class="icos-user"></span></a></li>
                    <li><a href="#alt2" title=""><span class="icos-archive"></span></a></li>
                </ul>
                
                <div class="divider"><span></span></div>
                
                <div id="general">
					<!-- Sub nav begin -->
                	<?php $this->renderPartial('MENU::SIDE_SUB_DASHBOARD_MENU', $this->getRequestData()); ?>
					<!-- Sub Nav ends -->
                </div>
            </div>
            
            
            <!-- Sidebar datepicker -->
            <div class="sideWidget">
                <div class="inlinedate"></div>
            </div>
            
            <div class="divider"><span></span></div>

            
            <!-- Sidebar tags line -->
            <div class="formRow">
                <input type="text" id="tags" name="tags" class="tags" value="these,are,sample,tags" />
            </div>
            
            <div class="divider"><span></span></div>
            
            <!-- Sidebar buttons -->
            <div class="fluid sideWidget">
                <div class="grid6"><input type="submit" class="buttonS bRed" value="Cancel" /></div>
                <div class="grid6"><input type="submit" class="buttonS bGreen" value="Submit" /></div>
            </div>
            
            <div class="divider"><span></span></div>
            
       </div> 
       <div class="clear"></div>
   </div>
</div>
<!-- Sidebar ends -->
    
    
<!-- Content begins -->
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-screen"></span><?php echo($this->getAction_Name()); ?></span>
        <ul class="quickStats">
            <li>
                <a href="" class="blueImg"><img src="<?php echo($this->getViewData('applicationStaticPath')); ?>images/icons/quickstats/plus.png" alt="" /></a>
                <div class="floatR"><strong class="blue">5489</strong><span>visits</span></div>
            </li>
            <li>
                <a href="" class="redImg"><img src="<?php echo($this->getViewData('applicationStaticPath')); ?>images/icons/quickstats/user.png" alt="" /></a>
                <div class="floatR"><strong class="blue">4658</strong><span>users</span></div>
            </li>
            <li>
                <a href="" class="greenImg"><img src="<?php echo($this->getViewData('applicationStaticPath')); ?>images/icons/quickstats/money.png" alt="" /></a>
                <div class="floatR"><strong class="blue">1289</strong><span>orders</span></div>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
    
    <!-- Breadcrumbs line -->
	<?php $this->renderPartial('MENU::BREADCRUMB', $this->getRequestData()); ?>    
    
    <!-- Main content -->
    <div class="wrapper">
      	<div class="fluid">
			<div class="grid4">
				<div class="widget">
					<div class="whead">
						<h6>Quick settings</h6>
						<a href="#" class="buttonH bBlue" title="">Save</a>
						<div class="clear"></div>
					</div>	
					<ul class="niceList params">
						<li>
							<div class="myPic"><img src="<?php echo($this->getViewData('profileImage')); ?>" alt="" width="75" height="75" /></div>
							<div class="myInfo">
								<h5><?php echo($this->getViewData('objUser')->getFullName()); ?> <a href="#" rel="btn_imageFileUploadForm" class="icona" data-icon="&#xe1ac;"></a></h5>
								<span class="myRole"><?php echo($this->getViewData('objUser')->getCompany()); ?> </span>
								<span class="followers">10 Projects</span>
							</div>
							<div class="clear"></div>
						</li>
						<li class="on_off">
							<label for="ch1"><span class="icon-key"></span>Keep me logged in:</label>
							<input type="checkbox" id="ch1" checked="checked" name="chbox" />
							<div class="clear"></div>
						</li>
						<li class="on_off">
							<label for="ch2"><span class="icon-reload-CW"></span>Enable quick changes:</label>
							<input type="checkbox" id="ch2" name="chbox" />
							<div class="clear"></div>
						</li>
						<li class="on_off">
							<label for="ch3"><span class="icon-fullscreen"></span>Allow quick view:</label>
							<input type="checkbox" id="ch3" name="chbox" />
							<div class="clear"></div>
						</li>
						<li class="on_off">
							<label for="ch4"><span class="icon-locked-2"></span>Auto log off:</label>
							<input type="checkbox" id="ch4" checked="checked" name="chbox" />
							<div class="clear"></div>
						</li>
					</ul>
				   </div>
			  </div>
			  <div class="grid8">
			  	<div class="widget">       
					<ul class="tabs">
						<li><a href="#tab1">General Information</a></li>
						<li><a href="#tab2">Account</a></li>
						<li><a href="#tab3">Settings</a></li>
					</ul>
					
					<div class="tab_container_with_overflow">
						<ul class="tToolbar">
							<li><a href="#" title=""><span class="icos-inbox"></span>Import profile content</a></li>
							<li><a href="#" title=""><span class="icos-outbox"></span>Export profile content</a></li>
							<li><a href="#" title=""><span class="icos-download"></span>Download profile</a></li>
						</ul>
						<div id="tab1" class="tab_content">
							<form action="<?php echo ($this->getViewData('postUrlGeneral')); ?>" method="post" class="ajax_submit">
							 <div class="formRow">
								<div class="grid3"><label>First Name:</label></div>
								<div class="grid9"><input type="text" name="first_name" id="first_name" placeholder="Enter Your First Name." value="<?php echo ($this->getViewData('objUser')->getFirst_Name()); ?>" /></div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>Middle Name:</label></div>
								<div class="grid9">
									<input type="text" name="middle_name" id="middle_name" placeholder="Enter Your Middle Name." value="<?php echo ($this->getViewData('objUser')->getMiddle_Name()); ?>" /></div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>Last Name:</label></div>
								<div class="grid9">
									<input type="text" name="last_name" id="last_name" placeholder="Enter Your Last Name." value="<?php echo ($this->getViewData('objUser')->getLast_Name()); ?>" /></div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>Address Line 1:</label></div>
								<div class="grid9">
									<input type="text" name="address1" id="address1" placeholder="Enter Your Address." value="<?php echo ($this->getViewData('objUser')->getAddress1()); ?>" /></div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>Address Line 2:</label></div>
								<div class="grid9">
									<input type="text" name="address2" id="address2" placeholder="Enter Your Address." value="<?php echo ($this->getViewData('objUser')->getAddress2()); ?>" /></div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>City:</label></div>
								<div class="grid9">
									<input type="text" name="city" id="city" placeholder="Enter Your City." value="<?php echo ($this->getViewData('objUser')->getCity()); ?>" /></div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>State / Province:</label></div>
								<div class="grid9 searchDrop">
									<select name="state" data-placeholder="Choose a State..." class="select" style="width:350px;" tabindex="7">
										<option value="">Please Select a State</option>
										<?php
											$arrStateRsData = $this->getViewData('arrState');
											if ((int) $this->getViewData('objUser')->getState()) {
												$objUserState = STATE::getInstance((int) $this->getViewData('objUser')->getState());
												echo ('<option selected="selected" value="' . $objUserState->getId() . '">' . $objUserState->getState_Name() . '</option> ');
											}
											while (list($intIndex, $arrStateData) = each($arrStateRsData)) {
												echo ('<option ' . $strSelected . ' value="' . $arrStateData['id'] . '">' . $arrStateData['state_name'] . '</option> ');
											}
										?>
									</select>
								</div>             
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>Country:</label></div>
								<div class="grid9 searchDrop">
									<select name="country" data-placeholder="Choose a Country..." class="select" style="width:350px;" tabindex="8">
										<option value="">Please Select a Country</option>
										<?php
											if ((int) $this->getViewData('objUser')->getCountry()) {
												$objUserCountry = COUNTRY::getInstance((int) $this->getViewData('objUser')->getCountry());
												echo ('<option selected="selected" value="' . $objUserCountry->getId() . '">' . $objUserCountry->getCountry_Name() . '</option> ');
											}
											$arrCountryRsData = $this->getViewData('arrCountry');
											while (list($intIndex, $arrCountryData) = each($arrCountryRsData)) {
												echo ('<option value="' . $arrCountryData['id'] . '">' . $arrCountryData['country_name'] . '</option> ');
											}
										?>
									</select>
								</div>             
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>Zip / Postal Code:</label></div>
								<div class="grid9">
									<input type="text" name="zip" id="zip" placeholder="Enter Your Zip / Postal Code." value="<?php echo ($this->getViewData('objUser')->getZip()); ?>" style="width:180px;"/></div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>Phone Number:</label></div>
								<div class="grid9">
									<input type="text" name="phone_number" class="maskPhoneExt" value="<?php echo ($this->getViewData('objUser')->getPhone_Number()); ?>" id="acpro_inp28"  style="width:180px;" />
									<span class="note">(999) 999-9999? x99999</span>
								</div>
								<div class="clear"></div>
							</div>
							<div class="formRow" align="right">
								<div class="btn-group rightdd" style="display: inline-block; margin-bottom: -4px;">
								  <button class="buttonM bDefault dropdown-toggle" data-toggle="dropdown" id="submit_button_general_toggle"><span class="caret"></span></button>
								  <ul class="dropdown-menu left">
										<li><a href="#"><span class="icos-add"></span>Save Draft</a></li>
										<li><a href="#"><span class="icos-trash"></span>Reset</a></li>
								  </ul>	
								  <button type="submit" class="buttonM bBlue floatL" id="submit_button_general"><span class="icon-download"></span><span>Save Profile & Continue</span></button>
								</div>
							</div>	
							</form>        
						</div>
						<div id="tab2" class="tab_content">
							<form action="<?php echo ($this->getViewData('postUrlAccount')); ?>" method="post" class="ajax_submit">
							 <div class="passwordMeter">
							 	<div id="solidBlue" class="roundSolid ml30" style="display: inline-block;"></div>
							 </div>
							 <div class="formRow">
								<div class="grid3"><label>Current Password:</label></div>
								<div class="grid9">
									<input type="password" name="current_password" id="current_password" placeholder="Enter Your Current Password." value="" style="width:50%" />
									<img src="<?php echo($this->getViewData('applicationStaticPath')); ?>images/icons/usual/icon-locked.png" alt="" class="fieldIcon">
								</div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>New password:</label></div>
								<div class="grid9">
									<input type="password" name="new_password1" id="new_password1" placeholder="Enter Your New Password." value="" style="width:50%" />
									<img src="<?php echo($this->getViewData('applicationStaticPath')); ?>images/icons/usual/icon-unlocked.png" alt="" class="fieldIcon">
								</div>
								<div class="clear"></div>
							</div>
							<div class="formRow">
								<div class="grid3"><label>New password Again:</label></div>
								<div class="grid9">
									<input type="password" name="new_password2" id="new_password2" placeholder="Enter Your New Password Again." value="" style="width:50%" />
									<img src="<?php echo($this->getViewData('applicationStaticPath')); ?>images/icons/usual/icon-unlocked.png" alt="" class="fieldIcon">
								</div>
								<div class="clear"></div>
							</div>
							<div class="formRow" align="right">
								<div class="btn-group rightdd" style="display: inline-block; margin-bottom: -4px;">
								  <button class="buttonM bDefault dropdown-toggle" data-toggle="dropdown" id="submit_button_general_toggle"><span class="caret"></span></button>
								  <ul class="dropdown-menu left">
										<li><a href="#"><span class="icos-add"></span>Save Draft</a></li>
										<li><a href="#"><span class="icos-trash"></span>Reset</a></li>
								  </ul>	
								  <button type="submit" class="buttonM bBlue floatL" id="submit_button_general"><span class="icon-download"></span><span>Save Profile & Continue</span></button>
								</div>
							</div>	
							</form>
						</div>
						
						<div id="tab3" class="tab_content">
						
						</div>
					</div>	
					<div class="clear"></div>		 
				</div>
			  </div>
		</div>
	   
        
    
        
        <div class="fluid">
        	
            <div class="grid6">
                <!-- Search widget -->
                <div class="searchLine">
                    <form action="">
                        <input type="text" name="search" class="ac" placeholder="Enter search text..." />
                       <button type="submit" name="find" value=""><span class="icos-search"></span></button>
                    </form>
                </div>
                
                <!-- Multiple files uploader -->
                <div class="widget">    
                    <div class="whead"><h6>WYSIWYG editor</h6><div class="clear"></div></div>
                    <textarea id="editor" name="editor" rows="" cols="16">Some cool stuff here</textarea>                    
                </div>
            </div>  
        </div>
    </div>
    <!-- Main content ends -->
</div>
<!-- Content ends -->
<form method="post" id="imageFileUploadForm" style="visibility:hidden" action="<?php echo($this->getViewData('changeProfileImageUrl')); ?>">
	<input type="file" name="file" id="imageFileUploadField" value="" />
</form>
<?php $this->renderPartial('NOTIFICATION::MESSAGES', $this->getRequestData()); ?>
</body>
</html>
