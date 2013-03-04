<?php
	class NOTIFIER  { 
		public function NOTIFIER () {}
		
		public function execute() {
			$basePath = dirname($_SERVER['PHP_SELF']) . DIRECTORY_SEPARATOR;
			
			?>
				<script type="text/javascript">
				<!--
					var <?php echo(__CLASS__); ?> = {
						basePath: '<?php echo($basePath); ?>'
					};
				//-->
				</script>
				<link href="<?php echo($basePath); ?>css/jnotifier.css" rel="stylesheet" type="text/css" />
				<script src="<?php echo($basePath); ?>js/jnotifier.js" type="text/javascript"></script>
		<?php
		}
	}
?>
