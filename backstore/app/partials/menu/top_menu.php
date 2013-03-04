<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_TOP_MENU extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		$this->Application 			 		= APPLICATION::getInstance();
		$this->canonicalurl 				= URL::getCanonicalUrl(NULL, true, false, true);
		$this->strApplicationStaticResPath 	= $this->Application->getBaseStaticResourcePath();
	}
	
	public function render()
	{
?>
	<div id="top">
		<div class="wrapper">
			<a href="index.html" title="" class="logo"><img src="/static/images/logo-small.png" alt="" /></a>
			<!-- Right top nav -->
			<div class="topNav">
				<ul class="userNav">
					<li><a title="" class="search"></a></li>
					<li><a href="#" title="" class="screen"></a></li>
					<li><a href="#" title="" class="settings"></a></li>
					<li><a href="<?php echo($this->Application->getUser()->getLogoutUrl()); ?>" title="Logout <?php echo($this->Application->getUser()->getUserName()); ?>" class="logout"></a></li>
					<li class="showTabletP"><a href="#" title="" class="sidebar"></a></li>
				</ul>
				<a title="" class="iButton"></a>
				<a title="" class="iTop"></a>
				<div class="topSearch">
					<div class="topDropArrow"></div>
					<form action="">
						<input type="text" placeholder="search..." name="topSearch" />
						<input type="submit" value="" />
					</form>
				</div>
			</div>
			
			<!-- Responsive nav -->
			<ul class="altMenu">
				<li><a href="index.html" title="">Dashboard</a></li>
				<li><a href="ui.html" title="" class="exp" id="current">UI elements</a>
					<ul>
						<li><a href="ui.html">General elements</a></li>
						<li><a href="ui_icons.html">Icons</a></li>
						<li><a href="ui_buttons.html">Button sets</a></li>
						<li><a href="ui_grid.html" class="active">Grid</a></li>
						<li><a href="ui_custom.html">Custom elements</a></li>
						<li><a href="ui_experimental.html">Experimental</a></li>
					</ul>
				</li>
				<li><a href="forms.html" title="" class="exp">Forms stuff</a>
					<ul>
						<li><a href="forms.html">Inputs &amp; elements</a></li>
						<li><a href="form_validation.html">Validation</a></li>
						<li><a href="form_editor.html">File uploads &amp; editor</a></li>
						<li><a href="form_wizards.html">Form wizards</a></li>
					</ul>
				</li>
				<li><a href="messages.html" title="">Messages</a></li>
				<li><a href="statistics.html" title="">Statistics</a></li>
				<li><a href="tables.html" title="" class="exp">Tables</a>
					<ul>
						<li><a href="tables.html">Standard tables</a></li>
						<li><a href="tables_dynamic.html">Dynamic tables</a></li>
						<li><a href="tables_control.html">Tables with control</a></li>
						<li><a href="tables_sortable.html">Sortable &amp; resizable</a></li>
					</ul>
				</li>
				<li><a href="other_calendar.html" title="" class="exp">Other pages</a>
					<ul>
						<li><a href="other_calendar.html">Calendar</a></li>
						<li><a href="other_gallery.html">Images gallery</a></li>
						<li><a href="other_file_manager.html">File manager</a></li>
						<li><a href="other_404.html">Sample error page</a></li>
						<li><a href="other_typography.html">Typography</a></li>
					</ul>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
	</div>
<?php	
	}
}