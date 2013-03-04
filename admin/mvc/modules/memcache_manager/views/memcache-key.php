<?php 
	$Application = ADMIN_APPLICATION::getInstance();
?>
<form  class="mainForm" style="width:500px">
	<fieldset>
		<div class="widget gbox_margin_top0">
			<div class="head">
				<h5 class="iList">Key: <strong><?php echo (substr($this->getRequestedMemcacheKey(), 0, 50) . (strlen($this->getRequestedMemcacheKey()) > 50 ? '...' : '')); ?></strong></h5>
				<div class="num" id="close">
					<a class="blueNum" href="#" title="Click to Close" rel="cancel"><strong>x</strong></a>
				</div>
				<div class="loader" id="loader" style="display:none">
					<img alt="" src="/admin/static/images/loaders/loader.gif">
				</div>
			</div>
            <div class="rowElem">
            	<label class="topLabel"><strong>Key Data:</strong></label>
            	<div class="formBottom">
            		<textarea name="textarea" class="auto" cols="" rows="13" 
            			style="font-size:12px;height: auto; overflow: auto;"><?php print_r($Application->getMemcacheValue() ? $Application->getMemcacheValue() : 'No Data'); ?></textarea>
            	</div>
            	<div class="fix"></div>
            </div>
             <div class="rowElem">
            	<label class="topLabel"><strong>Key:</strong></label>
            	<div class="formBottom">
            		<textarea><?php echo $this->getRequestedMemcacheKey(); ?></textarea>
            	</div>
            	<div class="fix"></div>
            </div>
            <div class="rowElem">
	            <input type="button" rel="cancel" class="basicBtn submitForm" value="Done" /><br />
				<div class="fix"></div>
			</div>
		</div>
		<div class="fix"></div>
	</fieldset>  
</form>