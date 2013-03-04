<?php 
/**
 * Alert Notifications Partial Class
 */	
class PARTIAL_JNOTIFIER_NOTIFICATIONS extends PARTIAL_BASE
{
	private static $baseImagePath = "/core/components/messagebox/assets/img/v2/images/";
	/**
	 * This array holds the possible get parameter were looking for to display a jnotifier.
	 * 
	 * @var  	Array
	 * @access  private static
	 */
	private static $arrCasesImg = array(
		'warn' 		=> 	'warning.png',
		'err'		=>	'error.png',
		'ok'		=>	'success.png',
		'tip'		=>	'tip.png',
		'sec'		=>	'secure.png',
		'info'		=>	'info.png',
		'msg' 		=> 'info.png',
		'message' 	=> 'message.png',
		'dwn'		=> 'download.png',
		'buy'		=> 'purchase.png',
		'print'		=>	'print.png'
	);
	
	public function __construct()
	{
		
	}
	
	public function execute(array $arrparameters)
	{
		
	}
	
	public function render()
	{
		if (false == empty($_GET)) 
		{
			while (list($strGetKey, $mxGetData) = each($_GET))
			{
				if (array_key_exists($strGetKey, self::$arrCasesImg))
				{
					?>
					<script type="text/javascript">
						$(document).ready(function(){
							$.jnotify(
								'', 
								'<?php echo ($mxGetData); ?>',
								'<?php echo self::$baseImagePath . self::$arrCasesImg[$strGetKey]; ?>',
								{lifeTime: 15000}
							);
						});
					</script>
					<?php 
				}
			}
		}
	}
}
