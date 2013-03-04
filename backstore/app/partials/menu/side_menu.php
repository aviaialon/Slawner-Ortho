<?php 
/**
 * Side Menu Partial Class
 */	
class PARTIAL_SIDE_MENU extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		$this->Application 					= APPLICATION::getInstance();
		$this->objUser						= $this->Application->getUser();
		$this->strBaseStaticResourcePath 	= $this->Application->getBaseStaticResourcePath();
		$this->strStaticResourcePath 		= $this->Application->getStaticResourcePath();
	}
	
	public function render()
	{
?>
	<div class="mainNav">
        <div class="user">
             <a title="" class="leftUserDrop">
				<img src="<?php echo($this->objUser->getProfileImage(4, $this->strStaticResourcePath . 'images/userLogin2.png')); ?>" alt="" width="70" height="72" />
				<span><strong>3</strong></span>
			</a>
			<span><?php echo($this->objUser->getUserName()); ?></span>
            <ul class="leftUser">
                <li><a href="#" title="" class="sProfile">My profile</a></li>
                <li><a href="#" title="" class="sMessages">Messages</a></li>
                <li><a href="#" title="" class="sSettings">Settings</a></li>
                <li><a href="<?php echo($this->objUser->getLogoutUrl()); ?>" title="Logout" class="sLogout">Logout</a></li>
            </ul>
        </div>
        
        <!-- Responsive nav -->
        <div class="altNav">
            <div class="userSearch">
                <form action="">
                    <input type="text" placeholder="search..." name="userSearch" />
                    <input type="submit" value="" />
                </form>
            </div>
            
            <!-- User nav -->
            <ul class="userNav">
                <li><a href="#" title="" class="profile"></a></li>
                <li><a href="#" title="" class="messages"></a></li>
                <li><a href="#" title="" class="settings"></a></li>
                <li><a href="<?php echo($this->objUser->getLogoutUrl()); ?>" title="Logout" class="logout"></a></li>
            </ul>
        </div>
        
        <!-- Main nav -->
        <ul class="nav">
            <li><a href="index.html" title="" class="active"><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/icons/mainnav/dashboard.png" alt="" /><span>Dashboard</span></a></li>
            <li><a href="ui.html" title=""><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/icons/mainnav/ui.png" alt="" /><span>UI elements</span></a>
                <ul>
                    <li><a href="ui.html" title=""><span class="icol-fullscreen"></span>General elements</a></li>
                    <li><a href="ui_icons.html" title=""><span class="icol-images2"></span>Icons</a></li>
                    <li><a href="ui_buttons.html" title=""><span class="icol-coverflow"></span>Button sets</a></li>
                    <li><a href="ui_grid.html" title=""><span class="icol-view"></span>Grid</a></li>
                    <li><a href="ui_custom.html" title=""><span class="icol-cog2"></span>Custom elements</a></li>
                    <li><a href="ui_experimental.html" title=""><span class="icol-beta"></span>Experimental</a></li>
                </ul>
            </li>
            <li><a href="forms.html" title=""><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/icons/mainnav/forms.png" alt="" /><span>Projects</span></a>
                <ul>
                    <li><a href="forms.html" title=""><span class="icol-list"></span>Inputs &amp; elements</a></li>
                    <li><a href="form_validation.html" title=""><span class="icol-alert"></span>Validation</a></li>
                    <li><a href="form_editor.html" title=""><span class="icol-pencil"></span>File uploader &amp; WYSIWYG</a></li>
                    <li><a href="form_wizards.html" title=""><span class="icol-signpost"></span>Form wizards</a></li>
                </ul>
            </li>
            <li><a href="messages.html" title=""><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/icons/mainnav/messages.png" alt="" /><span>Messages</span></a></li>
            <li><a href="statistics.html" title=""><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/icons/mainnav/statistics.png" alt="" /><span>Statistics</span></a></li>
            <li><a href="tables.html" title=""><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/icons/mainnav/tables.png" alt="" /><span>Tables</span></a>
                <ul>
                    <li><a href="tables.html" title=""><span class="icol-frames"></span>Standard tables</a></li>
                    <li><a href="tables_dynamic.html" title=""><span class="icol-refresh"></span>Dynamic table</a></li>
                    <li><a href="tables_control.html" title=""><span class="icol-bullseye"></span>Tables with control</a></li>
                    <li><a href="tables_sortable.html" title=""><span class="icol-transfer"></span>Sortable and resizable</a></li>
                </ul>
            </li>
            <li><a href="other_calendar.html" title=""><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/icons/mainnav/other.png" alt="" /><span>Other pages</span></a>
                <ul>
                    <li><a href="other_calendar.html" title=""><span class="icol-dcalendar"></span>Calendar</a></li>
                    <li><a href="other_gallery.html" title=""><span class="icol-images2"></span>Images gallery</a></li>
                    <li><a href="other_file_manager.html" title=""><span class="icol-files"></span>File manager</a></li>
                    <li><a href="#" title="" class="exp"><span class="icol-alert"></span>Error pages <span class="dataNumRed">6</span></a>
                        <ul>
                            <li><a href="other_403.html" title="">403 error</a></li>
                            <li><a href="other_404.html" title="">404 error</a></li>
                            <li><a href="other_405.html" title="">405 error</a></li>
                            <li><a href="other_500.html" title="">500 error</a></li>
                            <li><a href="other_503.html" title="">503 error</a></li>
                            <li><a href="other_offline.html" title="">Website is offline error</a></li>
                        </ul>
                    </li>
                    <li><a href="other_typography.html" title=""><span class="icol-create"></span>Typography</a></li>
                    <li><a href="other_invoice.html" title=""><span class="icol-money2"></span>Invoice template</a></li>
                </ul>
            </li>
        </ul>
    </div>
	
	<!-- secondary nav -->
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
                    <ul class="subNav">
                        <li><a href="other_calendar.html" title="" class="this"><span class="icos-dcalendar"></span>Calendar</a></li>
                        <li><a href="other_gallery.html" title="" class=""><span class="icos-images2"></span>Images gallery</a></li>
                        <li><a href="other_file_manager.html" title=""><span class="icos-files"></span>File manager</a></li>
                        <li><a href="#" title="" class="exp"><span class="icos-alert"></span>Error pages <span class="dataNumRed">6</span></a>
                            <ul>
                                <li><a href="other_403.html" title="">403 error</a></li>
                                <li><a href="other_404.html" title="">404 error</a></li>
                                <li><a href="other_405.html" title="">405 error</a></li>
                                <li><a href="other_500.html" title="">500 error</a></li>
                                <li><a href="other_503.html" title="">503 error</a></li>
                                <li><a href="other_offline.html" title="">Website is offline error</a></li>
                            </ul>
                        </li>
                        <li><a href="other_typography.html" title=""><span class="icos-create"></span>Typography</a></li>
                        <li><a href="other_invoice.html" title=""><span class="icos-money2"></span>Invoice template</a></li>
                    </ul>
                </div>

                <div class="divider"><span></span></div>
                
                <div id="general">
                
                    <!-- Sidebar big buttons -->
                    <div class="sidePad">
                        <a href="#" title="" class="sideB bLightBlue">Add new session</a>
                        <a href="#" title="" class="sideB bGreen mt10">New orders</a>
                    </div>
                    
                    <div class="divider"><span></span></div>
                
                    <!-- Sidebar file uploads widget -->
                    <div class="sideUpload">
                        <div class="dropFiles"></div>
                        <ul class="filesDown">
                            <li class="currentFile">
                                <div class="fileProcess">
                                    <img src="<?php echo($this->strBaseStaticResourcePath); ?>images/elements/loaders/10s.gif" alt="" class="loader" />
                                    <strong>Homepage_widgets_102.psd</strong>
                                    <div class="fileProgress">
                                        <span>9.1 of 17MB</span> - <span>243KB/sec</span> - <span>1 min</span>
                                    </div>
                                    
                                    <div class="contentProgress"><div class="barG tipN" title="61%" id="bar10"></div></div>
                                </div>
                            </li>
                            <li><span class="fileSuccess"></span>About_Us_08956.psd<span class="remove"></span></li>
                            <li><span class="fileSuccess"></span>Our_services_02811.psd<span class="remove"></span></li>
                            <li><span class="fileError"></span>Homepage_Alt_032811.psd<span class="remove"></span></li>
                            <li><span class="fileQueue"></span>Homepage_Alt_032811.psd<span class="remove"></span></li>
                            <li><span class="fileQueue"></span>Homepage_Alt_032811.psd<span class="remove"></span></li>
                        </ul>
                    </div>
                    
                    <div class="divider"><span></span></div>
                    
                    <!-- Sidebar chart -->
                    <div class="sideChart">
                        <div class="barsS" id="placeholder1_hS"></div>
                    </div>
                </div>
                
                <div id="alt1">
                    
                    <!-- Sidebar chart -->
                    <div class="numStats">
                        <ul>
                            <li><a href="#" title="">4248</a><span>visitors</span></li>
                            <li><a href="#" title="">748</a><span>orders</span></li>
                            <li class="last"><a href="#" title="">357</a><span>reviews</span></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    
                    <div class="divider"><span></span></div>
                
                	<!-- Sidebar user list -->
                    <ul class="userList">
                        <li>
                            <a href="#" title="">
                                <img src="<?php echo($this->strBaseStaticResourcePath); ?>images/live/face1.png" alt="" />
                                <span class="contactName">
                                    <strong>Eugene Kopyov <span>(5)</span></strong>
                                    <i>web &amp; ui designer</i>
                                </span>
                                <span class="status_away"></span>
                                <span class="clear"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#" title="">
                                <img src="<?php echo($this->strBaseStaticResourcePath); ?>images/live/face2.png" alt="" />
                                <span class="contactName">
                                    <strong>Lucy Wilkinson <span>(12)</span></strong>
                                    <i>Team leader</i>
                                </span>
                                <span class="status_off"></span>
                                <span class="clear"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#" title="">
                                <img src="<?php echo($this->strBaseStaticResourcePath); ?>images/live/face3.png" alt="" />
                                <span class="contactName">
                                    <strong>John Dow</strong>
                                    <i>PHP developer</i>
                                </span>
                                <span class="status_available"></span>
                                <span class="clear"></span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="divider"><span></span></div>
                
                    <!-- Sidebar progress bars -->
                    <div class="sideWidget">
                        <div class="contentProgress"><div class="barGr tipS" id="bar1" title="15%"></div></div>   
                        <div class="contentProgress mt8"><div class="barB tipS" id="bar2" title="30%"></div></div>
                        <div class="contentProgress mt8"><div class="barO tipS" id="bar3" title="45%"></div></div>
                        <div class="contentProgress mt8"><div class="barBl tipS" id="bar4" title="60%"></div></div>
                        <div class="contentProgress mt8"><div class="barR tipS" id="bar5" title="75%"></div></div>  
                    </div>       
                    
                </div>
                
                
                <div id="alt2">
                
                	<!-- Sidebar forms -->
                    <div class="sideWidget">
                        <div class="formRow">
                            <label>Usual input field:</label>
                            <input type="text" name="regular" placeholder="Your name" />
                        </div>
                        <div class="formRow">
                           <label>Usual password field:</label>
                            <input type="password" name="regular" placeholder="Your password" /> 
                        </div>
                        <div class="formRow">
                            <label>Single file uploader:</label>
                            <input type="file" class="fileInput" id="fileInput" />
                        </div>
                        <div class="formRow">
                            <label>Dropdown menu:</label>
                            <select name="select2" >
                                <option value="opt1">Usual select box</option>
                                <option value="opt2">Option 2</option>
                                <option value="opt3">Option 3</option>
                                <option value="opt4">Option 4</option>
                                <option value="opt5">Option 5</option>
                                <option value="opt6">Option 6</option>
                                <option value="opt7">Option 7</option>
                                <option value="opt8">Option 8</option>
                            </select>
                        </div>
                        
                        <div class="formRow searchDrop">
                            <label>Dropdown with search:</label>
                            <select data-placeholder="Choose a Country..." class="select" tabindex="2">
                                <option value=""></option> 
                                <option value="Cambodia">Cambodia</option> 
                                <option value="Cameroon">Cameroon</option> 
                                <option value="Canada">Canada</option> 
                                <option value="Cape Verde">Cape Verde</option> 
                                <option value="Cayman Islands">Cayman Islands</option> 
                                <option value="Central African Republic">Central African Republic</option> 
                                <option value="Chad">Chad</option> 
                                <option value="Chile">Chile</option> 
                                <option value="China">China</option> 
                                <option value="Christmas Island">Christmas Island</option> 
                                <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
                                <option value="Colombia">Colombia</option> 
                                <option value="Comoros">Comoros</option> 
                                <option value="Congo">Congo</option> 
                                <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
                                <option value="Cook Islands">Cook Islands</option> 
                                <option value="Costa Rica">Costa Rica</option> 
                                <option value="Cote D'ivoire">Cote D'ivoire</option> 
                                <option value="Croatia">Croatia</option> 
                                <option value="Cuba">Cuba</option> 
                                <option value="Cyprus">Cyprus</option> 
                                <option value="Czech Republic">Czech Republic</option> 
                                <option value="Denmark">Denmark</option> 
                                <option value="Djibouti">Djibouti</option> 
                                <option value="Dominica">Dominica</option> 
                                <option value="Dominican Republic">Dominican Republic</option> 
                                <option value="Ecuador">Ecuador</option> 
                                <option value="Egypt">Egypt</option> 
                                <option value="El Salvador">El Salvador</option> 
                                <option value="Equatorial Guinea">Equatorial Guinea</option> 
                                <option value="Eritrea">Eritrea</option> 
                                <option value="Estonia">Estonia</option> 
                                <option value="Ethiopia">Ethiopia</option> 
                                <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
                                <option value="Faroe Islands">Faroe Islands</option> 
                                <option value="Fiji">Fiji</option> 
                                <option value="Finland">Finland</option> 
                                <option value="France">France</option> 
                                <option value="French Guiana">French Guiana</option> 
                                <option value="French Polynesia">French Polynesia</option> 
                                <option value="French Southern Territories">French Southern Territories</option> 
                                <option value="Gabon">Gabon</option> 
                                <option value="Gambia">Gambia</option> 
                                <option value="Georgia">Georgia</option> 
                                <option value="Germany">Germany</option> 
                                <option value="Ghana">Ghana</option> 
                                <option value="Gibraltar">Gibraltar</option> 
                                <option value="Greece">Greece</option> 
                            </select>
                        </div>
                    
                        <div class="formRow">
                            <input type="checkbox" id="check2" name="chbox1" checked="checked" class="check" />
                            <label for="check2"  class="nopadding">Checkbox checked</label>
                            <div class="clear"></div>
                        </div>
                        <div class="formRow">
                            <input type="radio" id="radio1" name="question1" checked="checked" />
                            <label for="radio1"  class="nopadding">Usual radio button</label>
                            <div class="clear"></div>
                        </div>
                        <div class="formRow">
                            <label>Usual textarea:</label>
                            <textarea rows="8" cols="" name="textarea" placeholder="Your message"></textarea>
                        </div>
                        <div class="formRow">
                            <input type="submit" class="buttonS bLightBlue" value="Submit button" />
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="divider"><span></span></div>
            
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
<?php
	}
}