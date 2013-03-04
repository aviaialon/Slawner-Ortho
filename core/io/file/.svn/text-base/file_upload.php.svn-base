<?php
	/**
	 * FILE_UPLOAD Administration Class
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
	 class FILE_UPLOAD {
	 	protected	$ERRORS				= array();		// Upload errors
	 	protected	$ALLOWED_EXTENSION	= array();		// Defined allowed file extensions. if none are defined, then all are allowed.
	 	protected 	$MAX_FILE_SIZE 		= false; 		// Define a maximum file size for the uploaded file in Kb.
	 	protected	$FILESAVEPATH		= false;		// Upload file save path
	 	protected	$FILE_EXTENSION		= false;		// Uploaded File Extension
	 	protected	$FILE_NAME			= false;		// Original File Name
	 	protected	$NEW_FILE_NAME		= false;		// Uploaded File Name
	 	protected	$FILE_SIZE			= false;		// Uploaded File Size
	 	protected	$FORMFIELDNAME		= false;		// Upload form field name
		protected	$HAS_FILE_TO_UPLOAD	= false;		// If theres a file to upload
		protected	$IS_UPLOAD_SUCCESS	= false;		// If theres a file to upload
		protected	$IMAGE_WIDTH		= false;		// Image width (if applicable)
		protected	$IMAGE_HEIGHT		= false;		// Image height (if applicable)
	 	protected 	$IMAGE_TYPES		= array('image/gif', 'image/jpg', 'image/jpeg', 'image/png', 'image/bmp'); 
		public 	  function __construct() {  }
		
		/**
		 * Setters
		 */
		public function setError($strError = '') {
			$this->ERRORS[] = $strError;
		}
		
	 	public function setFormFieldName($strFieldName = false) {
			$this->FORMFIELDNAME = $strFieldName;
		}
		
		public function setMaxFileSize($intMaxFileSize = false) {
			$this->MAX_FILE_SIZE = (int) $intMaxFileSize;
		}
		
		public function setFileSavePath($strFileSavePath = false) {
			$this->FILESAVEPATH = $strFileSavePath;
		}
		
		public function setFileSize($intFileSize = false) {
			$this->FILE_SIZE = (int) $intFileSize;
		}
		
		public function setAllowedExtensions($arrExtensions = false) {
			$this->ALLOWED_EXTENSION = (array) $arrExtensions;
		}
		
		public function hasFileToUpload($blnHasFile=NULL) {
			if (! is_null($blnHasFile)) {
				$this->HAS_FILE_TO_UPLOAD = (bool) $blnHasFile;
			}
			return ($this->HAS_FILE_TO_UPLOAD);
		}
		
		public function isUploadSuccess($blSuccess=NULL) {
			if (! is_null($blSuccess)) {
				$this->IS_UPLOAD_SUCCESS = (bool) $blSuccess;
			}
			return ($this->IS_UPLOAD_SUCCESS);
		}
		
		protected function setNewFileName($strFileName=false) {
			$this->NEW_FILE_NAME = $strFileName;
		}
		
	 	protected function setFileName($strFileName=false) {
			$this->FILE_NAME = pathinfo($strFileName, PATHINFO_FILENAME);				
		}
		
		protected function setFileExtension($strFileName=false) {
			$this->FILE_EXTENSION = pathinfo($strFileName, PATHINFO_EXTENSION);		
		}
		
		/**
		 * Getters
		 */
		public function getMaxFileSize() 		{ return($this->MAX_FILE_SIZE); }
		public function getAllowedExtensions()	{ return($this->ALLOWED_EXTENSION); }
		public function getFileExtension()		{ return($this->FILE_EXTENSION); }
		public function getFileName()			{ return($this->FILE_NAME); }	
		public function getFileSize()			{ return($this->FILE_SIZE); }	
		public function getFormFieldName()		{ return($this->FORMFIELDNAME); }	
		public function getFileSavePath()		{ return($this->FILESAVEPATH); }	
		public function getErrors()				{ return($this->ERRORS); }	
		public function hasErrors()				{ return((bool) sizeof($this->ERRORS)); }	
		public function getNewFileExtension()	{ return($this->FILE_EXTENSION); }
		public function getNewFileName()		{ return($this->NEW_FILE_NAME); }
		public function getNewFileSize()		{ return($this->FILE_SIZE); }
		
		public function getNewFileDirectory()	{ 
			$strFilePath = false;
			if (strlen($this->getFileSavePath())) {
				$strFilePath = str_replace(__SITE_ROOT__, "", $this->getFileSavePath()) . DIRECTORY_SEPARATOR ;
				$strFilePath = str_replace("//", "/", $strFilePath);
			}
			return ($strFilePath);
		}
		
		public function getNewFilePath() {
			$strFileName = false;
			if (strlen($this->getNewFileName())) {
				$strFileName = str_replace(__SITE_ROOT__, "", $this->getFileSavePath()) . DIRECTORY_SEPARATOR . $this->getNewFileName();
				$strFileName = str_replace("//", "/", $strFileName);
			}
			return ($strFileName);
		}
		
		public function getNewFileInfo() {
			$arrFileInfo = array();
			if(
				$this->hasFileToUpload() &&
				$this->isUploadSuccess() 
			) {
				$arrFileInfo = array(
					'FILE_PATH'			=> 	$this->getNewFilePath(),
					'FILE_NAME'			=>	$this->getNewFileName(),	
					'FILE_SIZE'			=>	$this->getNewFileSize(),	
					'FILE_EXTENSION'	=>	$this->getNewFileExtension(),
					'FILE_DIRECTORY'	=>	$this->getNewFileDirectory()
				);		
				if ($this->IMAGE_WIDTH) {
					$arrFileInfo['IMAGE_WIDTH']	 = (int) $this->IMAGE_WIDTH;
					$arrFileInfo['IMAGE_HEIGHT'] = (int) $this->IMAGE_HEIGHT;
				}
			}
			return ($arrFileInfo);	
		}
		/**
		 * do'ers
		 */
		public function upload() {
			$blnContinue = true;
			$this->hasFileToUpload($blnContinue);
			
			// Check the save path
			if (
				($blnContinue) &&
				(! $this->getFileSavePath())
			) {
				$this->setError('Please provide a file save path.');
				$blnContinue = false;
			}
			
			// Check the save path exists
			if (
				(! strlen($this->getFileSavePath())) ||
				(! is_dir($this->getFileSavePath()))
			) {
				$this->setError('File save path does not exists');
				$blnContinue = false;
			}
				
			// Check that a file was submitted:
			if (
				($this->getFormFieldName()) &&
				(isset($_FILES[$this->getFormFieldName()])) &&
				(! (strlen($_FILES[$this->getFormFieldName()]['name'])))
			) {
				$this->clearErrors();
				$this->hasFileToUpload(false);
				$blnContinue = false;
			}	
			
			// Check the form field.
			if (
				($blnContinue) &&
				((! $this->getFormFieldName()) ||
				(! isset($_FILES[$this->getFormFieldName()])))
			) {
				$this->setError('Please provide a file form field to upload.');
				$blnContinue = false;
			} 
									
			// Check File type extension.
			if (
				($blnContinue) &&
				(! $this->checkFileExtension($_FILES[$this->getFormFieldName()]['name']))
			) {
				$this->setError('The selected file type is not accepted. Please select another file.');
				$blnContinue = false;
			}
			
			// Set the file info
			if ($blnContinue) {
				$this->setFileName($_FILES[$this->getFormFieldName()]['name']);
				$this->setFileExtension($_FILES[$this->getFormFieldName()]['name']);
				$this->setFileSize(filesize($_FILES[$this->getFormFieldName()]['tmp_name']));
				$this->setNewFileName(uniqid() . '_' . time() . '.' . $this->getFileExtension());
			}
			// Check the file size
			if(
				($blnContinue)				&&
				($this->getFileSize()) 		&&
				($this->getMaxFileSize()) 	&&
				((int) $this->getFileSize() > (int) ($this->getMaxFileSize() * 1024))
			) {
				$this->setError('File size exceeds ' . $this->getMaxFileSize() . 'kb. Please select a smaller file.');
				$blnContinue = false;
			}
			
			// All is good, begin upload.
			$blnContinue = ((bool) ($_FILES[$this->getFormFieldName()]["error"] == UPLOAD_ERR_OK));
			if ($blnContinue) {
				if (
					(strlen($_FILES[$this->getFormFieldName()]['tmp_name'])) &&
					(strlen($this->getFileSavePath() . DIRECTORY_SEPARATOR . $this->getNewFileName()))
				) {
					$blnContinue = copy(
						$_FILES[$this->getFormFieldName()]['tmp_name'], 
						$this->getFileSavePath() . DIRECTORY_SEPARATOR . $this->getNewFileName()
					);
				}
				
				// Check if uploaded file is an image, if so, add some meta...
				if(in_array($_FILES[$this->getFormFieldName()]["type"], $this->IMAGE_TYPES)) {
					$arrImageSize = getimagesize($_FILES[$this->getFormFieldName()]['tmp_name']);
					if (sizeof($arrImageSize)) {
						$this->IMAGE_WIDTH  = $arrImageSize[0];
						$this->IMAGE_HEIGHT = $arrImageSize[1];
						
					}
				}
			} else {
				$this->setError("Couldn't Upload File. Please Select a Different File.");	
			}
			
			// Clear the form files, to prevent multiple uploads on refresh.
			if ($blnContinue) {
				unset($_FILES[$this->getFormFieldName()]);	
			}
			
			$this->isUploadSuccess($blnContinue);
			return ($blnContinue);
		}
		
		protected function checkFileExtension($strFileName = NULL) {
			$blnIsValid = (is_null($strFileName) ? false : true);
			if (
				($blnIsValid) &&
				(sizeof($this->getAllowedExtensions()))
			) {
				$strFileExtension = pathinfo($strFileName, PATHINFO_EXTENSION);		
				if (
					(! strlen($strFileExtension)) ||
					(array_search(strtolower($strFileExtension), $this->getAllowedExtensions()) === false) 
				) {
					$blnIsValid = false;
				}
			}
			return ($blnIsValid);
		}
		
		protected function clearErrors() { $this->ERRORS = array(); }
	}
?>

<?php
	/*
	
		EXAMPLE USAGE:: 
		(dependencies: CLASSES::IO::IMAGE::IMAGE - Because were uploading images!)
	
	
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::IO::FILE::FILE_UPLOAD");
	SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::IO::IMAGE::IMAGE");
	
	$objUploader = new FILE_UPLOAD();
	$objUploader->setFileSavePath(__SITE_ROOT__ . '/dev/tmp/'); 
	$objUploader->setFormFieldName('test');
	$objUploader->setMaxFileSize(900);
	$objUploader->setAllowedExtensions(array(
		'gif', 'png', 'jpg', 'jpeg'
	));
	if ($objForm->isPost()) {
		$objUploader->upload();
		if(
			$objUploader->hasFileToUpload() &&
			$objUploader->isUploadSuccess() 
		) {
			// Upload success, process the image.
			//$arrImageInfo = $objUploader->getNewFileInfo();
			
			// Create an image sub instance.
			// Position 1 (50 X 50)
			$objImagePosition1 = new IMAGE(
				$objUploader->getNewFilePath(),
				$objUploader->getFileSavePath() . 'RESAMPLED_1_' . $objUploader->getNewFileName()
			);
			$objImagePosition1->resize(50, 50);
			
			
			// Position 2 (90 X 90)
			$objImagePosition2 = new IMAGE(
				$objUploader->getNewFilePath(),
				$objUploader->getFileSavePath() . 'RESAMPLED_2_' . $objUploader->getNewFileName()
			);
			$objImagePosition2->resize(90, 90);
			
			
			
			print_r('<h1>ORIGINAL IMAGE</h1>');
			print_r('<img src="' . $objUploader->getNewFilePath() . '" alt="">');
			print_r('<br />');
			
			print_r('<h1>POSITION 1 (50, 50)</h1>');
			print_r('<img src="' . $objImagePosition1->getTargetImagePath() . '" alt="">');
			print_r('<br />');
			
			print_r('<h1>POSITION 2 (90, 90)</h1>');
			print_r('<img src="' . $objImagePosition2->getTargetImagePath() . '" alt="">');
			print_r('<br />');
			
			print_r ('<h1>' . $objImagePosition1->getError() . '</h1>');
			print_r ('<h1>' . $objImagePosition2->getError() . '</h1>');
			
			
			new dump($objUploader->getNewFileInfo());
		} else {
			// Errors:	
			print_r('<h1>ERROR UPLOADING FILE.</h1>');
			new dump($objUploader->getErrors());	
		}
		
	}
	?>
    <form method="post" enctype="multipart/form-data">
    	<input type="file" name="test"/>
        <input type="submit" />
    </form>
*/ ?>	