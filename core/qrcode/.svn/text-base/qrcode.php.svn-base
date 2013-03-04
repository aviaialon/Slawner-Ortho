<?php
	/**
	 * QRCODE Administration Class
	 * This class represents an image uploader
	 *
	 * @package		CLASSES::IO::FILE
	 * @subpackage	none
	 * @author      Avi Aialon <aviaialon@gmail.com>
	 * @copyright	2010 Deviant Logic. All Rights Reserved
	 * @license		http://www.deviantlogic.ca/license
	 * @version		SVN: $Id$
	 * @link		SVN: $HeadURL$
	 * @since		12:35:53 PM
	 *
	 */	
	 
	 /**
		EXAMPLE:
		
		// set BarcodeQR object 
		$qr = new QRCODE(); 
		
		// create URL QR code 
		$qr->url("www.shayanderson.com"); 
		
		// display new QR code image 
		$qr->draw();
		
		
		You can also save the QR code PNG image like this:
		
		 // save new QR code image (size 150x150) 
		$qr->draw(150, "tmp/qr-code.png");
		
		Other QR code types the class will create:
		// bookmark 
		$qr->boomark("title", "url"); 
		
		// contact 
		$qr->contact("name", "address", "phone", "email"); 
		
		// content 
		$qr->content("type", "size", "content"); 
		
		// email 
		$qr->email("email", "subject", "message"); 
		
		// geo location 
		$qr->geo("lat", "lon", "height"); 
		
		// phone 
		$qr->phone("phone"); 
		
		// sms 
		$qr->sms("phone", "text"); 
		
		// text 
		$qr->text("text"); 
		
		// URL 
		$qr->url("url"); 
		
		// wifi connection 
		$qr->wifi("type", "ssid", "password");
	 */
	 class QRCODE extends SITE_EXCEPTION {
		/**
		 * Chart API URL
		 */
		const API_CHART_URL = "http://chart.apis.google.com/chart";
	
		/**
		 * Code data
		 *
		 * @var string $_data
		 */
		private $_data;
	
		/**
		 * Bookmark code
		 *
		 * @param string $title
		 * @param string $url
		 */
		public function bookmark($title = null, $url = null) {
			$this->_data = "MEBKM:TITLE:{$title};URL:{$url};;";
		}
	
		/**
		 * MECARD code
		 *
		 * @param string $name
		 * @param string $address
		 * @param string $phone
		 * @param string $email
		 */
		public function contact($name = null, $address = null, $phone = null, $email = null) {
			$this->_data = "MECARD:N:{$name};ADR:{$address};TEL:{$phone};EMAIL:{$email};;";
		}
	
		/**
		 * Create code with GIF, JPG, etc.
		 *
		 * @param string $type
		 * @param string $size
		 * @param string $content
		 */
		public function content($type = null, $size = null, $content = null) {
			$this->_data = "CNTS:TYPE:{$type};LNG:{$size};BODY:{$content};;";
		}
	
		/**
		 * Generate QR code image
		 *
		 * @param int $size
		 * @param string $filename
		 * @return bool
		 */
		public function draw($size = 150, $filename = null) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, self::API_CHART_URL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "chs={$size}x{$size}&cht=qr&chl=" . urlencode($this->_data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$img = curl_exec($ch);
			curl_close($ch);
			if($img) {
				if((bool) $filename) {
					if(!preg_match("#\.png$#i", $filename)) {
						$filename .= ".png";
					}
					
					return file_put_contents($filename, $img);
				} else {
					header("Content-Type: image/png");
					print $img; die;
					return true;
				}
			}
	
			return false;
		}
		
		/**
		 * Generate a binary interpretation of the QR code image
		 * Ex: <img src="<?php echo($objQr->getBinaryImage(350)); ?>" />
		 *
		 * @param int $size
		 * @param string $filename
		 * @return BASE64 image
		 */
		public function getBinaryImage($size = 150)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, self::API_CHART_URL);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "chs={$size}x{$size}&cht=qr&chl=" . urlencode($this->_data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$img = curl_exec($ch);
			return ('data:image/gif;base64,' . base64_encode($img));
		}
	
		/**
		 * Email address code
		 *
		 * @param string $email
		 * @param string $subject
		 * @param string $message
		 */
		public function email($email = null, $subject = null, $message = null) {
			$this->_data = "MATMSG:TO:{$email};SUB:{$subject};BODY:{$message};;";
		}
	
		/**
		 * Geo location code
		 *
		 * @param string $lat
		 * @param string $lon
		 * @param string $height
		 */
		public function geo($lat = null, $lon = null, $height = null) {
			$this->_data = "GEO:{$lat},{$lon},{$height}";
		}
	
		/**
		 * Telephone number code
		 *
		 * @param string $phone
		 */
		public function phone($phone = null) {
			$this->_data = "TEL:{$phone}";
		}
	
		/**
		 * SMS code
		 *
		 * @param string $phone
		 * @param string $text
		 */
		public function sms($phone = null, $text = null) {
			$this->_data = "SMSTO:{$phone}:{$text}";
		}
	
		/**
		 * Text code
		 *
		 * @param string $text
		 */
		public function text($text = null) {
			$this->_data = $text;
		}
	
		/**
		 * URL code
		 *
		 * @param string $url
		 */
		public function url($url = null) {
			$this->_data = preg_match("#^https?\:\/\/#", $url) ? $url : "http://{$url}";
		}
	
		/**
		 * Wifi code
		 *
		 * @param string $type
		 * @param string $ssid
		 * @param string $password
		 */
		public function wifi($type = null, $ssid = null, $password = null) {
			$this->_data = "WIFI:T:{$type};S{$ssid};{$password};;";
		}	 
		
		/**
		 * Static initiator
		 */
		public static function getInstance()
		{
			return (new self());
		} 	
	 }
?>	 