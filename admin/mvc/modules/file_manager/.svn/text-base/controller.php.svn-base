<?php
class FILE_MANAGER_CONTROLLER extends ADMIN_APPLICATION implements IMODULE_BASE
{	
	/**
	 * Module index action
	 * 
	 * @access	protected, final
	 * @param 	array $arrRequestParams
	 * @return 	void
	 */
	protected final function indexAction(array $arrRequestParams)
	{
	}

	/**
	 * Abstraction method: This method sets the config read from the config.ini file
	 * 
	 * @access	public, static
	 * @param 	array $arrConfig
	 * @return 	void
	 */
	public static function setIniConfig(array $arrConfig)
	{
		
	}
	
	/**
	 * Abstraction method, this method returns the module's display name
	 * 
	 * @access	public, static
	 * @return string
	 */
	public static function getDisplayName()
	{
		return ("File Manager");
	}
	
	/**
	 * Abstraction method, this method returns the module's sub menus
	 * 
	 * (non-PHPdoc)
	 * @see IMODULE_BASE::getSubMenuActions()
	 * @access	public, static
	 * @return 	array
	 */
	public static function getSubMenuActions()
	{
		return array();
	}
	
	/**
	 * Abstraction method, this method returns the module's output
	 * 
	 * (non-PHPdoc)
	 * @see IMODULE_BASE::renderOutput()
	 * @access	public
	 * @return 	void
	 */
		
	public function renderOutput(array $arrRequestParams)
	{
	?>
        <!-- File manager -->        
        <div class="widget first">
            <div class="head"><h5 class="iFiles">File manager</h5></div>
            <div id="fileManager"></div>
        </div>
        <!-- File upload -->
        <fieldset>
            <div class="widget">    
                <div class="head"><h5 class="iUpload">File upload: Uploaded to: <small style="color:#DDD;font-weight:normal"><?php echo(str_replace('//', '/', __DEV_NULL_PATH__)); ?></small></h5></div>
                <div id="uploader">You browser doesn't have HTML 4 support.</div>                    
            </div>
        </fieldset>
    <?php
	}
}