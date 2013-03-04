<?php 
	$arrSiteUsers = $this->getViewData('User_List');
?>
<script src="<?php echo  $arrRequestParams['View_Data']['Resource_Path']; ?>/js/user-manager.js"></script>
<!-- User manager -->        
<div class="widget first">
	<div class="head">
		<h5 class="iUsers">User list</h5>
		<div class="num"><a class="blueNum" href="#">+<?php echo(count($arrSiteUsers)); ?></a></div>
	</div>
	<div id="myList-nav"></div>
	<ul id="myList">
		<?php while(list($intIndex, $arruserData) = each($arrSiteUsers)) { ?>
		<li>
			<a href="#"><?php echo($arruserData['username']); ?></a>&nbsp;
			<ul class="listData">
				<?php if ($arruserData['username'] !== $arruserData['email']) { ?>
					<a href="#"><?php echo($arruserData['email']); ?></a>
				<?php } ?>	
				<li><a href="#" title=""><?php echo($arruserData['first_name'] . ' ' . $arruserData['last_name']); ?></a></li>
				<li><span class="red"><?php echo $arruserData['activation_date']; ?></span></li>
				<?php if ($arruserData['oauth_provider']) { ?>
					<li><span class="cNote"><?php echo $arruserData['oauth_provider']; ?></span></li>
				<?php } ?>
			</ul>
		</li>
		<?php } ?>
	</ul>
	<div class="rowElem alignRight">
		<a href="#" title="" class="btnIconLeft mr10 mt5 topDir" rel="Add_Menu" original-title="Add a New Menu">
			<img src="/admin/static/images/icons/dark/users.png" alt="" class="icon"><span style="font-size:8pt">New User</span></a>
	</div>
</div>       