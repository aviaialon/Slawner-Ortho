<!-- 
<h2>Edit Menu</h2>
<form method="post" action="<?php echo $arrRequestParams['View_Data']['Save_Menu_Url']; ?>">
	<p>
		<label for="edit-menu-title">Title</label>
		<input type="text" name="title" id="edit-menu-title" value="<?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['title']; ?>">
	</p>
	<p>
		<label for="edit-menu-url">URL</label>
		<input type="text" name="url" id="edit-menu-url" value="<?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['url']; ?>">
	</p>
	<p>
		<label for="edit-menu-class">Class</label>
		<input type="text" name="class" id="edit-menu-class" value="<?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['class']; ?>">
	</p>
	
</form>
-->
<form id="EditMenuForm" class="mainForm" method="post" action="<?php echo $arrRequestParams['View_Data']['Save_Menu_Url']; ?>">
	<input type="hidden" name="menu_id" value="<?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['id']; ?>">
	<fieldset>
		<div class="widget gbox_margin_top0">
			<div class="head">
				<h5 class="iList">Editing Menu Item: <strong><?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['title']; ?></strong></h5>
				<div class="num" id="close">
					<a class="blueNum" href="#" title="Click to Close" rel="cancel"><strong>x</strong></a>
				</div>
				<div class="loader" id="loader" style="display:none">
					<img alt="" src="/admin/static/images/loaders/loader.gif">
				</div>
			</div>
            <div class="rowElem nobg">
            	<label class="topLabel"><strong>Menu Title:</strong></label>
            	<div class="formBottom">
            		<input type="text" name="title" id="edit-menu-title" value="<?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['title']; ?>" original-title="Menu Title" />
            	</div>
            	<div class="fix"></div>
            </div>
            <div class="rowElem">
            	<label class="topLabel"><strong>Menu URL:</strong></label>
            	<div class="formBottom">
            		<input type="text" name="url" id="edit-menu-url" value="<?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['url']; ?>"  original-title="Menu URL">
            	</div>
            	<div class="fix"></div>
            </div>
            <div class="rowElem">
            	<label class="topLabel"><strong>Menu CSS Class:</strong></label>
            	<div class="formBottom">
            		<input type="text" name="class" id="edit-menu-class" value="<?php echo $arrRequestParams['View_Data']['Arr_Current_Menu']['class']; ?>" original-title="Menu CSS Class">
            	</div>
            	<div class="fix"></div>
            </div>
            <!--
            <div class="rowElem">
            	<label class="topLabel"><strong>Menu Description:</strong></label>
            	<div class="formBottom">
            		<textarea name="textarea" class="auto" cols="" rows="8" style="height: auto; overflow: hidden;" original-title="Menu Description"></textarea>
            	</div>
            	<div class="fix"></div>
            </div>
            -->
            <input type="submit" rel="submit" class="blueBtn submitForm mb22" value="Submit form" />
            <input type="button" rel="cancel" class="basicBtn submitForm" value="Cancel" />
            <div class="fix"></div>
		</div>
	</fieldset>  
</form>