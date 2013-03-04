<?php 
/**
 * Top Menu Partial Class
 */	
class PARTIAL_BREADCRUMB extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		$this->Application 		 = APPLICATION::getInstance();
		$this->requestDispatcher = $this->Application->getRequestDispatcher();
		$this->objUser			 = $this->Application->getUser();
	}
	
	public function render()
	{
		if ($this->objUser->isLoggedIn()) {
?>
<div class="breadLine">
	<div class="bc">
		<ul id="breadcrumbs" class="breadcrumbs">
			<li><a href="#"><?php echo($this->objUser->getUserName()); ?></a>
				<ul>
					<li><a href="#" title="">Profile</a></li>
					<li><a href="#" title="">Messages</a></li>
					 <li><a href="#" title="">Invoices</a></li>
					<li><a href="#" title="">Current Work</a></li>
					<li><a href="/?logout" title="">Logout</a></li>
				</ul>
			</li>
			<?php 
				$arrRequestData	= $this->requestDispatcher->getRequestData();
				while(list($intIndex, $strPath) = each($arrRequestData)) {
					// we dont want to display the controller
					if ($intIndex > 0) {
						$arrPath = array_slice($arrRequestData, 0, ($intIndex + 1));
						$blnIslast = (($intIndex >= (count($arrRequestData) - 1)) || $intIndex >= 2);
						echo ('<li class="' . (($blnIslast) ? 'current' : '') . '"><a href="/' . implode('/', $arrPath) . '">' . ucwords($strPath) . '</a></li>');
						if ($blnIslast) break;	
					}
				}
			?>	
		</ul>
	</div>
	
	<div class="breadLinks">
		<ul>
			<li><a href="#" title=""><i class="icos-list"></i><span>Orders</span> <strong>(+58)</strong></a></li>
			<li><a href="#" title=""><i class="icos-check"></i><span>Tasks</span> <strong>(+12)</strong></a></li>
			<li class="has">
				<a title="">
					<i class="icos-money3"></i>
					<span>Invoices</span>
					<span><img src="<?php echo($this->Application->getStaticResourcePath()); ?>images/elements/control/hasddArrow.png" alt="" /></span>
				</a>
				<ul>
					<li><a href="#" title=""><span class="icos-add"></span>New invoice</a></li>
					<li><a href="#" title=""><span class="icos-archive"></span>History</a></li>
					<li><a href="#" title=""><span class="icos-printer"></span>Print invoices</a></li>
				</ul>
			</li>
		</ul>
		 <div class="clear"></div>
	</div>
</div>
<?php		
		} 
	}
	
	/**
	 * OVEVRRIDE: This method outputs the partial's signature as a comments
	 */
	protected static function getSignature()
	{
		echo ("\n <!-- partial: " . str_replace('PARTIAL_', '', __CLASS__) . " | cached: " . (MENU::isCached() ? 'true' : 'false') . " --> \n");
	}
}