<?php 
/**
 * Menu Partial Class
 */	
class PARTIAL_MESSAGES extends PARTIAL_BASE
{
	
	const PARTIAL_MESSAGES_MESSAGE_TYPE_OK 		= 'success';
	const PARTIAL_MESSAGES_MESSAGE_TYPE_ERROR 	= 'error';
	const PARTIAL_MESSAGES_MESSAGE_TYPE_INFO 	= 'info';
	
	protected  $strData = NULL;
	
	public function __construct()
	{
	}
	
	public function execute(array $arrparameters)
	{
		$this->Application 		= APPLICATION::getInstance();
		$this->strMessageType 	= NULL;
		$this->strMessage	 	= NULL;
		$this->strMessageTitle 	= NULL;
		switch (get_class($this->Application))
		{
			case ('APPLICATION') 		: { $this->objDataContainer = $_SESSION; break; }
			case ('ADMIN_APPLICATION') 	: { $this->objDataContainer = $_GET; break; }
		}
		 // Session or Get
		if (
			(true === isset($this->objDataContainer['msg'])) ||
			(true === isset($this->objDataContainer['info'])) ||
			(true === isset($this->objDataContainer['ok'])) ||
			(true === isset($this->objDataContainer['err'])) 
		) {
			$this->strMessage = (
				(true === isset($this->objDataContainer['ok']) ? $this->objDataContainer['ok'] : (
					(true === isset($this->objDataContainer['err']) ? $this->objDataContainer['err'] : 
						(true === isset($this->objDataContainer['msg']) ? $this->objDataContainer['msg'] : $this->objDataContainer['info'])) 
				))
			);	
			
			$this->strMessageVarName = (
				(true === isset($this->objDataContainer['ok']) ? 'ok' : (
					(true === isset($this->objDataContainer['err']) ? 'err' : 
						(true === isset($this->objDataContainer['msg']) ? 'msg' : 'info')) 
				))
			);	
			
			$this->strMessageType = (
				(true === isset($this->objDataContainer['ok']) ? PARTIAL_MESSAGES::PARTIAL_MESSAGES_MESSAGE_TYPE_OK : (
					(true === isset($this->objDataContainer['err']) ? PARTIAL_MESSAGES::PARTIAL_MESSAGES_MESSAGE_TYPE_ERROR : PARTIAL_MESSAGES::PARTIAL_MESSAGES_MESSAGE_TYPE_INFO) 
				))
			);	
			
			$this->strMessageTitle = (
				(true === isset($this->objDataContainer['ok']) ? 'Success!' : (
					(true === isset($this->objDataContainer['err']) ? 
						$this->Application->translate('An Error Occured', 'Une erreur est survenue') : 
						$this->Application->translate('Information Message', 'Message d\'information')) 
				))
			);	
			
			unset($_SESSION[$this->strMessageVarName]);
		}
	}
	
	public function render()
	{
		
?>
	<div class="push-wrapper"></div>		
	<?php if (false === empty($this->strMessage)) { ?>
		<script type="text/javascript">
			$(document).ready(function(e) {
				window.setTimeout(function() {
					$('body').push({
						'type': '<?php echo($this->strMessageType); ?>',
						'title': '<?php echo($this->strMessageTitle); ?>',
						'content': '<?php echo($this->strMessage); ?>'
					});	
					// 
					$('.push-wrapper').draggable({handle: '.push-header'});
				}, <?php echo($this->Application->getUser()->getId() > 0 ? 1300 : '500'); ?>);
			});
		</script>
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