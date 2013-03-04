<?php
	/**
	 * Class SAVECONTENT
	 * @package CLASSES::CONTENT
	 */
	define('SAVECONTENT_TIMESTAMP', 	true); 
	define('SAVECONTENT_NOTIMESTAMP', 	false); 
	class SAVECONTENT {
		private $STR_CONTENT 		= NULL;
		private $BL_CONTENT_STOPPED = FALSE;
		private $LOAD_TIMESTAMP 	= SAVECONTENT_NOTIMESTAMP;
		
		public function SAVECONTENT() { }

		/**
		 * Class static initiator
		 * 
		 * @access public static final
		 * @return SAVECONTENT
		 */
		public static final function getInstance()
		{
			return new self();
		}
		
		public function start($intType = SAVECONTENT_NOTIMESTAMP) {
			$this->flush();
			$this->useTimeStamp($intType);
			ob_start();
		}
		
		public function stop() {
			$this->STR_CONTENT = ($this->LOAD_TIMESTAMP ? $this->getTimeStamp() : '' );
			$this->STR_CONTENT .= ob_get_clean();
			$this->STR_CONTENT .= ($this->LOAD_TIMESTAMP ? $this->getTimeStamp() : '' );
			$this->BL_CONTENT_STOPPED = true;
		}
		
		public function getContent() {
			if (! ($this->BL_CONTENT_STOPPED)) 
				$this->stop();
			return ($this->STR_CONTENT);
		}
		
		public function getData() {
			return ($this->getContent());
		}
		
		public function useTimeStamp($intType = SAVECONTENT_NOTIMESTAMP) {
			$this->LOAD_TIMESTAMP = ((strcmp($intType, SAVECONTENT_TIMESTAMP) == 0) ? SAVECONTENT_TIMESTAMP : SAVECONTENT_NOTIMESTAMP);
		}
		
		public function toString() {
			print ($this->getContent());	
		}
		
		private function flush() {
			$this->STR_CONTENT = NULL;
			$this->BL_CONTENT_STOPPED = FALSE;
			$this->LOAD_TIMESTAMP = SAVECONTENT_NOTIMESTAMP;
		}
		
		private function getTimeStamp() {
			return("\n<!-- Generated: " . date('l jS \of F Y h:i:s A') . " by: " . __CLASS__ . " -->\n");
		}
	}
?>