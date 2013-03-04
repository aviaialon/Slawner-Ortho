<?php
	class ALERT_NOTIFICATIONS_V1 {
		private static $IMAGE_PATH = "/core/components/messagebox/assets/img/";
		
		public static function getInstance($strLoadType = 'jquery')
		{
			return (new ALERT_NOTIFICATIONS_V1($strLoadType));
		}
		
		public function ALERT_NOTIFICATIONS_V1($_loadType = "jquery") {
			if ($this->hasRequest()) {
				$this->defaultParams();
				switch ($_loadType) {
					case "default" : {
						$this->loadJSandCSS();
						$this->captureRequest();
						break;
					}
					case "jquery" : {
						$this->loadJSandCSS();
						$this->captureRequestJquery();
						break;
					}
					default : {
						$this->loadJSandCSS();
						$this->captureRequest();
						break;
					}
				}	
			}
		}
		
		private function defaultParams() {
			if (! isset($_GET['err'])) 	$_GET['err'] 	= NULL;
			if (! isset($_GET['ok'])) 	$_GET['ok'] 	= NULL;
			if (! isset($_GET['msg'])) 	$_GET['msg'] 	= NULL;
			if (! isset($_GET['info'])) $_GET['info'] 	= NULL;
		}
		
		private function hasRequest() {
			if (((isset($_GET['err'])) && (strlen($_GET['err']) > 0) && ($_GET['err'] != NULL)) 
				|| (isset($_GET['msg'])) && (strlen($_GET['msg']) > 0 && ($_GET['msg'] != NULL)) 
				|| (isset($_GET['info'])) && (strlen($_GET['info']) > 0 && ($_GET['info'] != NULL)) 
				|| (isset($_GET['ok'])) && (strlen($_GET['ok']) > 0 && ($_GET['ok'] != NULL)) ) return (true);
			else return (false);	
		}
		
		private function captureRequest() {
			if ($this->hasRequest()) {
				switch ($this->getCase()) {
					case "ERROR" : {
						echo ('<table width="100%" border="0" cellspacing="0" cellpadding="0" id="msg_"' .$this->getCase() .'>' .
									'<tr><td width="100%" class="tdError" valign="middle">' .
										'<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>' .
											'<td valign="middle" style="padding-left:8px;vertical-align:middle;">' .
											'<img src="' . self::$IMAGE_PATH . '/err.png" align="absmiddle" /></td>' .
											'<td valign="middle"  width="100%" style="padding:10px;font-weight:bold;font-size:12px">' .
											$_GET['err'] .
											'</td>' .
										'</tr></table>' .
									'</td></tr>' .
								'</table><br />');
						//break; removed the breaks to allow multiple messages to be displayed.
					}
					case "MESSAGE" : {
						echo ('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="msg_"' .$this->getCase() .'>' .
									'<tr><td width="100%" class="tdMsg" valign="middle">' .
										'<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>' .
											'<td valign="middle" style="padding-left:8px;vertical-align:middle;">' .
											'<img src="' . self::$IMAGE_PATH . '/error.png" align="absmiddle" /></td>' .
											'<td valign="middle"  width="100%" style="padding:10px;font-weight:bold;font-size:12px">' .
											$_GET['msg'] .
											'</td>' .
										'</tr></table>' .
									'</td></tr>' .
								'</table><br />');
						//break; removed the breaks to allow multiple messages to be displayed.
					}
					case "SUCCESS" : {
						echo ('<table width="100%" border="0" cellspacing="0" cellpadding="0" class="msg_"' .$this->getCase() .'>' .
									'<tr><td width="100%" class="tdOK" valign="middle">' .
										'<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>' .
											'<td valign="middle" style="padding-left:8px;vertical-align:middle;">' .
											'<img src="' . self::$IMAGE_PATH . '/ok.gif" align="absmiddle" /></td>' .
											'<td valign="middle"  width="100%" style="padding:10px;font-weight:bold;font-size:12px">' .
											$_GET['ok'] .
											'</td>' .
										'</tr></table>' .
									'</td></tr>' .
								'</table><br />');
						//break; removed the breaks to allow multiple messages to be displayed.
					}
					default : {
					
					}
				}
			}
		}
		
		/*
		<div style="position: relative;" class="notice canhide">You have 7 new messages<div class="close"></div></div>
		<div style="position: relative;" class="info canhide">Welcome to Primo!<div class="close"></div></div>
		<div style="position: relative;" class="success canhide">Installation complete!<div class="close"></div></div>
		<div class="warning canhide">Install folder is still there! Hurry up, delete it!</div>
		*/
		
		private function captureRequestJquery() {
			if ($this->hasRequest()) {
				switch ($this->getCase()) {
					case "ERROR" : {
						if (strlen($_GET['err'])) {
							echo('<div style="position: relative;" class="warning canhide">' . 
							'&nbsp;&nbsp;<img src="' . self::$IMAGE_PATH . '/err.png" align="absmiddle" style="display:inline" />&nbsp;&nbsp;' . $_GET['err'] .'</div>');
							//break; removed the breaks to allow multiple messages to be displayed.
						}
					}
					case "MESSAGE" : {
						if (strlen($_GET['msg'])) {
							echo('<div style="position: relative;" class="notice canhide">&nbsp;&nbsp;' .
								'<img src="' . self::$IMAGE_PATH . '/error.png" align="absmiddle" style="display:inline" />&nbsp;&nbsp;' . $_GET['msg'] . '</div>');
							//break; removed the breaks to allow multiple messages to be displayed.
						}
					}
					case "SUCCESS" : {
						if (strlen($_GET['ok'])) {
							echo('<div style="position: relative;" class="success canhide">' . 
							'&nbsp;&nbsp;<img src="' . self::$IMAGE_PATH . '/ok.gif" align="absmiddle" style="display:inline" />&nbsp;&nbsp;' . $_GET['ok'] .'</div>');
							//break; removed the breaks to allow multiple messages to be displayed.
						}
					}
					case "INFO" : {
						if (strlen($_GET['info'])) {
							echo('<div style="position: relative;" class="info canhide">' . 
							'&nbsp;&nbsp;<img src="' . self::$IMAGE_PATH . '/info.png" align="absmiddle" style="display:inline" />&nbsp;&nbsp;' . $_GET['info'] . '</div>');
							//break; removed the breaks to allow multiple messages to be displayed.
						}
					}
					default : {
					
					}
				}
			}
		}
		
		
		private function getCase() {
			$case = NULL;
			if 		((isset($_GET['err'])) && (strlen($_GET['err']) != 0)) 		$case = "ERROR";
			else if ((isset($_GET['msg'])) && (strlen($_GET['msg']) != 0)) 		$case = "MESSAGE";
			else if ((isset($_GET['ok']))  && (strlen($_GET['ok']) != 0))  		$case = "SUCCESS";
			else if ((isset($_GET['info']))  && (strlen($_GET['info']) != 0))  	$case = "INFO";
			return ($case);
		}
		
		private function loadJSandCSS() {
			$CurrentCase = $this->getCase();
			$imgPath = self::$IMAGE_PATH;
			echo <<<SCRIPTS
			<script type="text/javascript">
				window.setTimeout(function() {
					jQuery('#msg_$CurrentCase').hide('normal');
				}, 500);
			</script>
			<script type="text/javascript">
				if (typeof jQuery !== "undefined") {
					jQuery(document).ready(function($){
						/* Elements closing system */
						$(".canhide").append("<div class='close'></div>").css("position", "relative");
						$(".close").click(function() {
							$(this).hide();
							$(this).parent().slideUp(300);
						});
					});
				};
			</script>
			<style type="text/css">
				.canhide img {background-image:none !important;}
				.tdError{
					border:solid 1px #990000;
					background-color: #FFEAEA;	
					padding-bottom:4px;
					padding-left:4px;
					padding-right:4px;
					padding-top:5px;
					font-weight:bold;
					font-size:11px;
					border-left:none;
					border-right:none;
					font-family:Arial;
				}
				.tdOK{
					font-family:Arial;
					border:solid 1px #006633;
					background-color: #D7FFD7;	
					color:#000;
					padding-bottom:4px;
					padding-left:4px;
					padding-right:4px;
					padding-top:5px;
					font-weight:bold;
					font-size:11px;
					border-left:none;
					border-right:none;
				}
				.tdMsg{
					font-family:Arial;
					border:solid 1px #FFCC00;
					background-color: #FFFFCC;	
					color:#000;
					padding-bottom:4px;
					padding-left:4px;
					padding-right:4px;
					padding-top:5px;
					font-weight:bold;
					font-size:11px;
					border-left:none;
					border-right:none;
				}/* System messages */
				.notice, .info, .warning, .success {
					border-radius: 5px !important ;
					-webkit-border-radius: 5px !important ;
					-moz-border-radius: 5px !important ;
					padding: 11px 15px 8px !important ;
					margin: 10px 0 !important ;
					color: #1d1d1d !important ;
					font-weight: normal !important ;
					font-size:12.5px !important ;
					color:#333 !important ;
					line-height:3px;
				}
				.notice a, .info a, .warning a, .success a {
					color: #000 !important ;
					text-decoration: underline !important ;
				}
				.notice, .info, .warning, .success {
					font-family:Verdana, Geneva, sans-serif;	
					font-size:15px;
				}
				.notice {
					background: #f4ef9a !important ;
					border: 1px solid #dcd783 !important ;
				}
				.info {
					background: #b5d1ff !important ;
					border: 1px solid #a4c1ee !important ;
				}
				.warning {
					background: #ffb5b5 !important ;
					border: 1px solid #e09799 !important ;
				}
				.success {
					background: #bcffb5 !important ;
					border: 1px solid #a3e59b !important ;
				}
				.close {
					width: 22px !important ;
					height: 22px !important ;
					position: absolute !important ;
					background: url($imgPath/close.png) no-repeat !important ;
					top: -8px !important ;
					left: -8px !important ;
					cursor: pointer !important ;
				}
				
			</style>
SCRIPTS;
		}
	};
?>