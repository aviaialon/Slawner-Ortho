<?php 
/**
 * Email Inbox Partial Class
 */	
class PARTIAL_EMAIL_INBOX extends PARTIAL_BASE
{
	protected  $strData = NULL;
	
	public function __construct()
	{
		
	}
	
	public function execute(array $arrparameters)
	{
		$this->Application = APPLICATION::getInstance();
		$this->strAdminProfileImage = 	'https://www.gravatar.com/avatar/' . md5(strtolower(trim(constant('__SMTP_USER__')))) . 
										'?d=' . urlencode($this->Application->getStaticResourcePath() . 'images/userLogin2.png') . '&s=72';
	}
	
	public function render()
	{
?>
<div class="fluid">
	<!-- Messages #1 -->
	<div class="widget grid6">
		<div class="whead">
			<h6>Message Inbox</h6>
			
			<ul class="titleToolbar">
				<li>
					<div class="headInput" style="width:90%;margin:6px 7px 5px 7px">
						<input type="text" name="emailSearch" id="emailSearch" placeholder="type to search" />
						<input type="submit" name="emailSubmit" class="srch" value="" />
					</div>
				</li>
				<li><a href="#" class="">Remove</a></li>
				<li style="height:37px"><a href="#" class="tipS" id="recentMessagesRefreshButton" title="Click here to refresh your inbox."><span class="icos-refresh"></span></a></li>
				<li class="drd"><a href="#" data-toggle="dropdown"><span class="iconb" data-icon=""></span></a>
					<ul class="dropdown-menu pull-right">
						<li><a href="#"><span class="icos-add"></span>New Message</a></li>
						<li><a href="#"><span class="icos-trash"></span>Remove</a></li>
						<li><a href="#" class=""><span class="icos-pencil"></span>Edit</a></li>
						<li><a href="#" class=""><span class="icos-heart"></span>Favorite</a></li>
					</ul>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
		
		<ul class="messagesOne" id="recentMessagesContainer">
			<li id="recentMessageLoading">
				<center>
					<img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/elements/loaders/10.gif" alt="">
					<div>Loading Inbox... Please wait</div>
				</center>
			</li>
		</ul>
		
		 <!-- Send message widget -->
		<div class="widget" style="border-left: none;border-right: none;">
			<div class="body">
				<span class="headLoad" id="messageSendLoad" style="display:none"><img src="/static/images/elements//loaders/4s.gif" alt=""></span>
				<div class="messageTo">
					<a href="mailto:<?php echo(constant('__SMTP_USER__')); ?>" title="" class="uName">
						<img src="<?php echo($this->strAdminProfileImage); ?>" alt="" width="37" height="37"></a><span> Send message to <strong><?php echo(constant('__SMTP_USER__')); ?></strong></span>
					<a href="mailto:<?php echo(constant('__SMTP_USER__')); ?>" title="" class="uEmail"><?php echo(constant('__SMTP_USER__')); ?></a>
				</div>
				<textarea rows="5" cols="" name="emailMessageBody" id="emailMessageBody" class="auto" placeholder="Write your message"></textarea>
				<div class="mesControls">
					<span><span class="iconb" data-icon="&#xe20d;"></span>Some basic <a href="#" >HTML</a> is OK</span>
					
					<div class="sendBtn sendwidget">
						<a href="#" title="" class="attachPhoto"></a>
						<a href="#" title="" class="attachLink"></a>
						<input type="submit" name="sendEmailMessage" id="sendEmailMessage" class="buttonM bLightBlue" value="Send message" />
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>  
		
	</div>
	
	<!-- Calendar -->
	<div class="widget grid6">
		<div class="whead"><h6>Calendar</h6><div class="clear"></div></div>
		<div id="calendar"></div>
	</div>
	<div class="clear"></div>
</div>
<?php
	}
	
	/**
	 * OVEVRRIDE: This method outputs the partial's signature as a comments
	 */
	protected static function getSignature()
	{
		echo ("\n <!-- partial: " . str_replace('PARTIAL_', '', __CLASS__) . " | cached: " . (MENU::isCached() ? 'true' : 'false') . " --> \n");
	}
}