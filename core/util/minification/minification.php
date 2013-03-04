<?php
/**
 * MINIFICATION :: This class minifies a javascript / css file.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2011 Avi Aialon  (www.deviantlogic.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * **
 *
 * 	IN ORDER TO ADD A NEW MINIFICATION PLUGIN, SIMPLAY ADD THE PLUGIN CLASS IN THE /plugin DIRECTORY
 *  AND DEFINE THE minify() METHOD WHICH RECEIVED A FILE CONTENT AND RETURNS MINIFIED DATA.
 *
 * **
 *
 * @package CLASSES::UTIL
 * @author Avi Aialon <aviaialon@gmail.com>
 * @copyright 2011 Avi Aialon <aviaialon@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.1 (2011-03-02)
 */
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::UTIL::MINIFICATION::PROCESSORS::JS_MINIFICATION");
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::UTIL::MINIFICATION::PROCESSORS::CSS_MINIFICATION");

class MINIFICATION extends SITE_EXCEPTION
{
	/**
	 * These map out to the processor classes available in /processor
	 *
	 * @var string
	 */
	const MINIFICATION_PROCESSOR_JAVASCRIPT = 'JS_MINIFICATION';
	const MINIFICATION_PROCESSOR_CSS 		= 'CSS_MINIFICATION';

	/**
	 * Saves the current processot type in use.
	 *
	 * @var string
	 */
	protected static $strCurrentProcessor 	= NULL;

	/**
	 * Saves the current processor.
	 *
	 * @var Object
	 */
	protected $objProcessor 				= NULL;

	/**
 	 * override method: getInstance
 	 *
 	 * @access	public static final
 	 * @param	String 	$strProcessorClassName	- The Processor Classname
 	 * @return	Object
 	 */
	public static function getInstance($strProcessorClassName = MINIFICATION::MINIFICATION_PROCESSOR_JAVASCRIPT)
	{
		$objClassInstance 	= new self();
		$objClassInstance->getProcessor($strProcessorClassName);
		return ($objClassInstance);
	}

	/**
 	 * returns a specific minifation processor
 	 *
 	 * @access	public, final
 	 * @param	String 	$strProcessorClassName	- The Processor Classname
 	 * @return	Object	The minification processor
 	 */
	public final function getProcessor($strProcessorClassName = MINIFICATION::MINIFICATION_PROCESSOR_JAVASCRIPT)
	{
		if (
			(FALSE === is_null($strProcessorClassName)) &&
			(class_exists($strProcessorClassName))
		) {
			self::$strCurrentProcessor = $strProcessorClassName;
			$this->objProcessor = $strProcessorClassName::getInstance($strProcessorClassName);
		}

		return ($this);
	}

 	/**
 	 * returns the scripts 'server' path from the root
 	 * Ex: http://ice.dns05.com/static/scripts/js/test.js
 	 * To: /var/www//static/scripts/js/test.js
 	 *
 	 * @access	protected, static, final
 	 * @param	String 	$strFile	- The JavaScript File
 	 * @return	String	The File read path
 	 */
 	protected static final function getServerFilePath($strFile = NULL)
 	{
 		$strLocatedFile = str_replace(array(__SITE_ROOT__, __ROOT_URL__), '', $strFile);
		$strLocatedFile = str_replace('//', '/', $strLocatedFile);
		$strLocatedFile = DIRECTORY_SEPARATOR . $strLocatedFile;
		$strLocatedFile = preg_replace('[/{2,}]', '/', $strLocatedFile);

		return ($strLocatedFile);
 	}

	/**
	 * Gets the file read path in an absolute manner
	 * Ex: http://ice.dns05.com/static/tmp/fdg687d6fg8sd6fgs.js maps
	 * to: /var/www/ice.dns05.com/static/tmp/fdg687d6fg8sd6fgs.js
	 *
	 * @access 	protected, static
	 * @param 	string $strFileIn - The input file path
	 * @return 	string $strFileIn - The returned files path (in absolute)
	 */
	protected static function getFileReadPath($strFileIn = NULL)
	{
		if (FALSE === is_null($strFileIn))
		{
			$strLocatedFile = str_replace(array(__SITE_ROOT__, __ROOT_URL__), '', $strFileIn);
			$strLocatedFile = str_replace('//', '/', $strLocatedFile);
			$strLocatedFile = __SITE_ROOT__ . DIRECTORY_SEPARATOR . $strLocatedFile;
			$strFileIn = $strLocatedFile;
		}
		return ($strFileIn);
	}


	/**
	 * This method returns the export file path for the Javascript File.
	 *
	 * @access	protected, static
	 * @param 	string $strFileIn	- The JavaScript File.
	 * @return 	void
	 */
	protected static function getExportReadPath($strFileIn = NULL)
	{
		if (! is_null($strFileIn))
		{
			$strLocatedFile = str_replace(array(__SITE_ROOT__, __ROOT_URL__), '', $strFileIn);
			$strLocatedFile = str_replace('//', '/', $strLocatedFile);
			$strLocatedFile = DIRECTORY_SEPARATOR . $strLocatedFile;
			$strLocatedFile = preg_replace('[/{2,}]', '/', $strLocatedFile);
			$strFileIn = $strLocatedFile;
		}
		return ($strFileIn);
	}


	/**
	 * This method returns the full file path and name which we'll use
	 * to save to minified version of! The path returned is the server real path
	 * EX: /var/www/site_root/static/minification/sadf6a8s7d6f87as76df9a8s76df.js
	 * If you need the include path ex: /minification/sadf6a8s7d6f87as76df9a8s76df.js
	 * you need to filter the result with: self::getExportReadPath('/minification/sadf6a8s7d6f87as76df9a8s76df.js');
	 *
	 * @access	protected, static
	 * @param 	mixed 	mxJsFileOrArray - The javascript file or file array
	 * @return 	string
	 */
	protected static function getExportFileIdentity($mxJsFileOrArray = NULL)
	{
		$strSHA1FileName = NULL;

		if (
			(is_array($mxJsFileOrArray)) &&
			(FALSE === empty($mxJsFileOrArray))
		) {
			// 1. Get the export file name:
			$strFileExtension 	= pathinfo($mxJsFileOrArray[0], PATHINFO_EXTENSION);
			$strSHA1FileName 	= sha1(serialize($mxJsFileOrArray)) . '.' . $strFileExtension;
		}
		else if (is_string($mxJsFileOrArray))
		{
			$strLocatedFile = self::getFileReadPath($mxJsFileOrArray);
			$strFileExtension 	= pathinfo($strLocatedFile, PATHINFO_EXTENSION);
			$strFileDirectory 	= dirname($strLocatedFile);
			$strFileName 		= basename($strLocatedFile);
			$strSHA1FileName 	= sha1(trim($strLocatedFile)) . '.' . $strFileExtension;
		}

		if (FALSE === is_null($strSHA1FileName))
		{
			$strExportDirectory = constant('__DEV_NULL_PATH__');

			if (FALSE === is_dir($strExportDirectory))
			{
				SITE_EXCEPTION::raiseException('Minification Export Directory ' . $strExportDirectory . ' is not valid.');
			}

			$strSHA1FileName = $strExportDirectory . DIRECTORY_SEPARATOR .  $strSHA1FileName;
			$strSHA1FileName = preg_replace('[/{2,}]', '/', $strSHA1FileName);
		}

		return ($strSHA1FileName);
	}

	/**
	 * This method writes a minification file to disc. It will only write it if the
	 * requested file to write DOESNT already exists.
	 *
	 * @throws 	SITE_EXCEPTION
	 * @access	protected, static
	 * @param 	string 		$strFilePath 	- The server file path
	 * @param 	string 		$strFileContent - The server file content
	 * @return 	boolean		if the new file was created.
	 */
	protected static final function writeFileToDisk($strFilePath, $strFileContent = NULL)
	{
		$blnReturn = false;

		if (FALSE === file_exists($strFilePath))
		{
			$rsFilehandle = fopen($strFilePath, 'w+');
			chmod($strFilePath, 0777);
			$blnReturn = fwrite($rsFilehandle, $strFileContent);
		}

		return ($blnReturn);
	}

	/**
	 *  || MAIN EXECUTION METHOD ||
	 */

	/**
	 * This method minifies a file then writes it to disk.
	 * This feature only happens if the file does not already exists
	 *
	 * @access	Public, Static, Final
	 * @param 	String  $strFileIn - The File Path to minify
	 * @return	String	$strFileIncludeName - The newly (or existsing) JavaScript compressed file path
	 */
	public function minifyFile($strFileIn = NULL)
	{
		$strFileIncludeName	= $strFileIn;
		$strLocatedFile		= self::getFileReadPath($strFileIn);
		$strSHA1FileName 	= self::getExportFileIdentity($strLocatedFile);
		$strMinFileIncName  = self::getExportReadPath($strSHA1FileName);

		// Already minified...
		if (TRUE === file_exists($strSHA1FileName))
		{
			$strFileIncludeName = $strMinFileIncName;
		}
		else if
		(
			(FALSE === file_exists($strMinFileIncName)) &&
			(FALSE === is_null($strFileIn)) &&
			(TRUE  === file_exists($strLocatedFile))
		) {
			// Start building the minified file content and
			$strNewFileContent  = "/* " . $strFileIn . " = " . now() . " - " . __CLASS__ . " */\n";
			$strNewFileContent .= $this->objProcessor->minify(file_get_contents($strLocatedFile));

			// add a closure for JavaScript Files (jquery components dont compress well!)
			if (self::$strCurrentProcessor == self::MINIFICATION_PROCESSOR_JAVASCRIPT)
			{
				$strNewFileContent .= ';';
			}

			// Write the new file, and return the path
			self::writeFileToDisk($strSHA1FileName, $strNewFileContent);
			$strFileIncludeName = $strMinFileIncName;
		}

		return ($strFileIncludeName);
	}

	/**
	 * THIS IS THE MAIN ENTRY METHOD THAT SHOULD BE USED IN PRODUCTION
	 * This method minifies multiple JavaScript files and returns one compressed batch file
	 *
	 * @access	public, static, final
	 * @param 	array 	$arrFiles - The array of files to compress
	 * @return	String	$$strFileIncludeName - The newly (or existing) JavaScript file
	 */
	public function minifyFiles(array $arrFiles = NULL)
	{
		// Initialise some variables
		$strSHA1FileName 		= self::getExportFileIdentity($arrFiles);
		$strMinFileIncName  	= self::getExportReadPath($strSHA1FileName);
		$strFileIncludeName 	= $strMinFileIncName; 	// The actual compressed data that we will write
		$arrComplressedFiles	= array();				// The array of successfuly compressed files
		$strCompressedData 		= NULL;					// The compressed data container
		$intFilesCompressed 	= 0;					// The count of successfuly compressed files

		if (
			(FALSE == file_exists($strSHA1FileName)) &&
			(FALSE === empty($arrFiles))
		) {
			reset ($arrFiles);
			while (list($intFileIndex, $strFileToCompress) = each($arrFiles))
			{
				$strLocatedFile = self::getFileReadPath($strFileToCompress);

				if(TRUE === file_exists($strLocatedFile))
				{
					$strCompressedData .= "\n\n/*  [" . $strFileToCompress . "]  */\n";
					// add a closure for JavaScript Files (jquery components dont compress well!)
					$strCompressedData .= $this->objProcessor->minify(file_get_contents($strLocatedFile)) . ';';
					$arrComplressedFiles[] = $strFileToCompress;
					$intFilesCompressed++;
				}
			}

			if (FALSE === empty($strCompressedData))
			{
				$strFinalCompressedData	 = "";
				$strFinalCompressedData .= "/* \n";
				$strFinalCompressedData .= "  MINIFICATION MODULE: " . now() . "\n";
				$strFinalCompressedData .= "  FILES COMPRESSED: " . $intFilesCompressed . "\n";
				$strFinalCompressedData .= "  FILES : \n\t" . implode("\n\t", $arrComplressedFiles) . "\n";
				$strFinalCompressedData .= "*/ \n";
				$strFinalCompressedData	.= $strCompressedData;

				// Now write the file and get out of here
				self::writeFileToDisk($strSHA1FileName, $strFinalCompressedData);
			}
		}

		return ($strFileIncludeName);
	}
}



interface INTERFACE_MINIFICATION
{
	/**
	 * This method RETURN AN OBJCT INSTANCE
	 *
	 * @access	public static
	 * @param 	NONE
	 * @return 	OBJECT
	 */
	public static function getInstance();

	/**
	 * This method minifies content
	 *
	 * @access	public static
	 * @param 	string $strContent	- The File Content.
	 * @return 	string
	 */
	public function minify		($strContent = NULL);

	/**
	 * This method minifies a single file
	 *
	 * @access	public static
	 * @param 	string $strContent	- The File Path.
	 * @return 	string
	 */
	public function minifyFile	($strFilepath = NULL);

	/**
	 * This method minifies Multiple files
	 *
	 * @access	public static
	 * @param 	array $arrFiles	- The File Array.
	 * @return 	string
	 */
	public function minifyFiles	(array  $arrFiles = NULL);
}