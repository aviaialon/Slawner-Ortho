<?php
	
	class ALERT_NOTIFICATIONS_V2 {
		private static $IMAGE_PATH =  "/core/components/messagebox/assets/img/v2/";
		private $arrCases = array(
			'warn' 	=> 	'warning',
			'err'	=>	'error',
			'ok'	=>	'success',
			'tip'	=>	'tip',
			'sec'	=>	'secure',
			'info'	=>	'info',
			'msg' 	=> 'info',
			'message' => 'message',
			'dwn'	=> 'download',
			'buy'	=> 'purchase',
			'print'	=>	'print'
		); // Possible URL cases => CSS declaration
		
		public static function getInstance($strLoadType = 'jquery')
		{
			return (new ALERT_NOTIFICATIONS_V2($strLoadType));
		}
		
		public function ALERT_NOTIFICATIONS_V2($_loadType = "jquery") {
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
			return ((bool) strlen($this->getCase()));	
		}
		
		private function getRequestData() {
			return ($_GET[$this->getCaseParam()]);	
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
		?>
        <div id="alertNotification">
            <div class="notification-wrap">
                <div class="notification <?php echo($this->getCase()); ?>">
                    <span></span>
                    <div class="text">
                        <p>
                            <strong><?php echo(ucwords($this->getCase())); ?></strong> 
                            <?php echo($this->getRequestData()); ?>
                        </p>
                    </div>
                </div>
                <?php if (isset($_GET['desc'])) { ?>
                <div class="description">
                    <p><?php echo($_GET['desc']); ?></p>
                    <span>&nbsp;</span>
                </div>
                <?php } ?>
            </div>
		</div>
        <?php		
			}
		}
		
		
		private function getCase() {
			$case = NULL;
			foreach ($_GET as $strCase => $strValue) {
				if (isset($this->arrCases[strtolower($strCase)])) {
					$case = $this->arrCases[strtolower($strCase)];
					break;
				}
			}
			return ($case);
		}
		
		private function getCaseParam() {
			$case = NULL;
			foreach ($_GET as $strCase => $strValue) {
				if (isset($this->arrCases[strtolower($strCase)])) {
					$case = $strCase;
					break;
				}
			}
			return ($case);
		}
		
		private function loadJSandCSS() {
			$CurrentCase = $this->getCase();
			$imgPath = self::$IMAGE_PATH;
			echo <<<SCRIPTS
			<script type="text/javascript">
				if (typeof jQuery !== "undefined") {
					jQuery(document).ready(function($){
						$('.description p').hide();
						$('.notification, .description span').hover(function() {
							$(this).css('cursor','pointer');
						}, function() {
								$(this).css('cursor','auto');
							});
						
						$('.notification span').click(function() {
							$('.notification-wrap').fadeOut(800);
						});
						
						$('.notification').click(function() {
							$('.notification-wrap').fadeOut(800);
						});
						
						$(".description span").click(function(){
							$(".description p").slideToggle("slow");
							$(this).toggleClass("close");
						});
					});
				};
			</script>
			<style type="text/css">
				#alertNotification .notification-wrap {
					width: 100% !important;
					margin-bottom: 30px !important;
					margin-top: 22px !important;
				}
				
				#alertNotification .notification-wrap .notification {
					min-height: 50px !important;
					display: block !important;
					position: relative !important;
					
					/*Border Radius*/
					border-radius: 5px !important;
					-moz-border-radius: 5px !important;
					-webkit-border-radius: 5px !important;	
					
					/*Box Shadow*/
					-moz-box-shadow: 2px 2px 2px #cfcfcf !important;
					-webkit-box-shadow: 2px 2px 4px #cfcfcf !important;
					box-shadow: 2px 2px 2px #cfcfcf !important;
					
					margin:0px !important;
					z-index: 1 !important;
				}
				
				#alertNotification .notification-wrap .notification span {
					background: url($imgPath/images/close.png) no-repeat right top !important;
					display: block !important;
					width: 19px !important;
					height: 19px !important;
					position: absolute !important;
					top:-9px !important;
					right: -8px !important;
				}
				
				#alertNotification .notification-wrap .notification .text { overflow: hidden !important; }
				
				#alertNotification .notification-wrap .notification p {
					width: auto !important;	
					font-family: Arial, Helvetica, sans-serif !important;
					color: #323232 !important;
					font-size: 14px !important;
					line-height: 30px !important;
					text-align: left !important;
					float: left !important;
					*margin-left: 15px !important;
					*margin-top: 15px !important; /*for lt IE8*/
					margin-top:10px;
					
					/* TEXT SHADOW */
					 text-shadow: 0px 0px 1px #f9f9f9 !important;
				}
				
				#alertNotification .notification-wrap .description {
					position: relative !important;
					min-height: 15px !important;
					width: 92% !important;
					margin: auto !important;
					
					/*Background Gradients*/
					background: #f0f0f0 !important;
					background: -moz-linear-gradient(top,#f7f7f7,#f0f0f0) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#f7f7f7), to(#f0f0f0)) !important;
					
					/*Border Radius*/
					-webkit-border-bottom-right-radius: 5px !important;
					-webkit-border-bottom-left-radius: 5px !important;
					-moz-border-radius-bottomright: 5px !important;
					-moz-border-radius-bottomleft: 5px !important;
					border-bottom-right-radius: 5px !important;
					border-bottom-left-radius: 5px !important;
					
					/*Box Shadow*/
					-moz-box-shadow: 2px 2px 2px #cfcfcf !important;
					-webkit-box-shadow: 2px 2px 4px #cfcfcf !important;
					box-shadow: 2px 2px 2px #cfcfcf !important;
				}
				
				#alertNotification .notification-wrap .description p {
					margin: 0px !important;
					padding: 15px !important;
					font-family: "Lucida Grande", Arial, Helvetica, sans-serif !important;
					font-size: 11px !important;
					color: #999 !important;
					line-height: 18px !important;
					text-align: justify !important;
					
					/* TEXT SHADOW */
					 text-shadow: 0px 0px 1px #fff !important;
				}
				
				#alertNotification .notification-wrap .description span {
					display: block !important;
					width: 19px !important;
					height: 19px !important;
					position: absolute !important;
					bottom:-9px !important;
					right: -8px !important;
					background-image: url($imgPath/images/open-close.png) !important;
					background-repeat: no-repeat !important;
				}
				
				#alertNotification .btn-desc {	background-position: left !important; }
				
				#alertNotification .close { background-position: right !important; }
				
				
				/*SUCCESS BOX*/
				
				#alertNotification .success {
					border-top: 1px solid #edf7d0 !important;
					border-bottom: 1px solid #b7e789 !important;
					
					/*Background Gradients*/
					background: #dff3a8 url($imgPath/images/success.png) no-repeat scroll 14px 16px !important;
					background: -moz-linear-gradient(top,#dff3a8,#c4fb92) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#dff3a8), to(#c4fb92)) !important;
					background-repeat:no-repeat !important;
					background-position:left middle !important;
				}
				
				#alertNotification .success:before {
					content: url($imgPath/images/success.png) !important;
					float: left !important;
					padding: 13px 15px 0px 15px !important;
				}
				
				#alertNotification .success strong {
					color: #61b316 !important;
					margin-right: 15px !important;
				}
				
				
				/*WARNING BOX*/
				
				#alertNotification .warning {
					border-top: 1px solid #fefbcd !important;
					border-bottom: 1px solid #e6e837 !important;
					
					/*Background Gradients*/
					background: #feffb1 url($imgPath/images/warning.png) no-repeat scroll 18px 12px !important;
					background: -moz-linear-gradient(top,#feffb1,#f0f17f) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#feffb1), to(#f0f17f)) !important;
				}
				
				#alertNotification .warning:before {
					content: url($imgPath/images/warning.png) !important;
					float: left !important;
					margin: 10px 35px 0px 25px !important;
				}
				
				#alertNotification .warning strong {
					color: #e5ac00 !important;
					margin-right: 15px !important;
				}
				
				
				/*QUICK TIP BOX*/
				
				#alertNotification .tip {
					border-top: 1px solid #fbe4ae !important;
					border-bottom: 1px solid #d9a87d !important;
					
					/*Background Gradients*/
					background: #f9d9a1 url($imgPath/images/tip.png) no-repeat scroll 18px 18px !important;
					background: -moz-linear-gradient(top,#f9d9a1,#eabc7a) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#f9d9a1), to(#eabc7a)) !important;
				}
				
				#alertNotification .tip:before {
					content: url($imgPath/images/tip.png) !important;
					float: left !important;
					margin: 13px 15px 0px 15px !important;
				}
				
				#alertNotification .tip strong {
					color: #b26b17 !important;
					margin-right: 15px !important;
				}
				
				
				/*ERROR BOX*/
				
				#alertNotification .error {
					border-top: 1px solid #f7d0d0 !important;
					border-bottom: 1px solid #c87676 !important;
					
					/*Background Gradients*/
					background: #f3c7c7 url($imgPath/images/error.png) no-repeat scroll 18px 18px !important;
					background: -moz-linear-gradient(top,#f3c7c7,#eea2a2) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#f3c7c7), to(#eea2a2)) !important;
				}
				
				#alertNotification .error:before {
					content: url($imgPath/images/error.png) !important;
					float: left !important;
					margin: 13px 15px 0px 15px !important;
				}
				
				#alertNotification .error strong {
					color: #b31616 !important;
					margin-right: 15px !important;
				}
				
				
				/*SECURE AREA BOX*/
				
				#alertNotification .secure {
					border-top: 1px solid #efe0fe !important;
					border-bottom: 1px solid #d3bee9 !important;
					
					/*Background Gradients*/
					background: #e5cefe url($imgPath/images/secure.png) no-repeat scroll 18px 14px !important;
					background: -moz-linear-gradient(top,#e5cefe,#e4bef9) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#e5cefe), to(#e4bef9)) !important;
				}
				
				#alertNotification .secure:before {
					content: url($imgPath/images/secure.png) !important;
					float: left !important;
					margin: 11px 15px 0px 15px !important;
				}
				
				#alertNotification .secure strong {
					color: #6417b2 !important;
					margin-right: 15px !important;
				}
				
				/*INFO BOX*/
				
				#alertNotification .info {
					border-top: 1px solid #f3fbff !important;
					border-bottom: 1px solid #bedae9 !important;
					
					/*Background Gradients*/
					background: #e0f4ff url($imgPath/images/info.png) no-repeat scroll 18px 14px !important;
					background: -moz-linear-gradient(top,#e0f4ff,#d4e6f0) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#e0f4ff), to(#d4e6f0)) !important;
				}
				
				#alertNotification .info:before {
					content: url($imgPath/images/info.png) !important;
					float: left !important;
					margin: 12px 15px 0px 21px !important;
				}
				
				#alertNotification .info strong {
					color: #177fb2 !important;
					margin-right: 15px !important;
				}
				
				/*MESSAGE BOX*/
				
				#alertNotification .message {
					border-top: 1px solid #f4f4f4 !important;
					border-bottom: 1px solid #d7d7d7 !important;
					
					/*Background Gradients*/
					background: #f0f0f0 url($imgPath/images/message.png) no-repeat scroll 18px 18px !important;
					background: -moz-linear-gradient(top,#f0f0f0,#e1e1e1) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#f0f0f0), to(#e1e1e1)) !important;
				}
				
				#alertNotification .message:before {
					content: url($imgPath/images/message.png) !important;
					float: left !important;
					margin: 16px 15px 0px 15px !important;
				}
				
				#alertNotification .message strong {
					color: #323232 !important;
					margin-right: 15px !important;
				}
				
				/*DONWLOAD BOX*/
				
				#alertNotification .download {
					border-top: 1px solid #ffffff !important;
					border-bottom: 1px solid #eeeeee !important;
					
					/*Background Gradients*/
					background: #f7f7f7 url($imgPath/images/download.png) no-repeat scroll 18px 18px !important;
					background: -moz-linear-gradient(top,#f7f7f7,#f0f0f0) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#f7f7f7), to(#f0f0f0)) !important;
				}
				
				#alertNotification .download:before {
					content: url($imgPath/images/download.png) !important;
					float: left !important;
					margin: 15px 15px 0px 18px !important;
				}
				
				#alertNotification .download strong {
					color: #037cda !important;
					margin-right: 15px !important;
				}
				
				/*PURCHASE BOX*/
				
				#alertNotification .purchase {
					border-top: 1px solid #d1f7f8 !important;
					border-bottom: 1px solid #8eabb1 !important;
					
					/*Background Gradients*/
					background: #c4e4e4 url($imgPath/images/purchase.png) no-repeat scroll 18px 18px !important;
					background: -moz-linear-gradient(top,#c4e4e4,#97b8bf) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#c4e4e4), to(#97b8bf)) !important;
				}
				
				#alertNotification .purchase:before {
					content: url($imgPath/images/purchase.png) !important;
					float: left !important;
					margin: 16px 21px 0px 15px !important;
				}
				
				#alertNotification .purchase strong {
					color: #426065 !important;
					margin-right: 15px !important;
				}
				
				/*PRINT BOX*/
				
				#alertNotification .print {
					border-top: 1px solid #dde9f3 !important;
					border-bottom: 1px solid #8fa6b2 !important;
					
					/*Background Gradients*/
					background: #cfdde8 url($imgPath/images/print.png) no-repeat scroll 18px 18px !important;
					background: -moz-linear-gradient(top,#cfdde8,#9eb3bd) !important;
					background: -webkit-gradient(linear, left top, left bottom, from(#cfdde8), to(#9eb3bd)) !important;
				}
				
				#alertNotification .print:before {
					content: url($imgPath/images/print.png) !important;
					float: left !important;
					margin: 13px 20px 0px 15px !important;
				}
				
				#alertNotification .print strong {
					color: #3f4c6b !important;
					margin-right: 15px !important;
				}
			</style>
SCRIPTS;
		}
	};
?>