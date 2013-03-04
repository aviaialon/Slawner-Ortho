<?php
class INDEX_CONTROLLER extends ADMIN_APPLICATION implements IMODULE_BASE
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
		// Stats on page views....
		$this->setPageViewsStats(PAGE_VIEWS::getObjectClassView(array(
			'filter'		=> array(
				'timeDate' => 'DATE_SUB(NOW(), INTERVAL 7 DAY)'
			),
			'operator'		=> array('>='),
			'escapeData' 	=> false,
			'limit'			=> 50
		)));	
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
		return ("Dashboard");
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
	<!-- Statistics -->
    <div class="stats">
      <ul>
        <li><a title="" class="count grey" href="#">52</a><span>new pending requests</span></li>
        <li><a title="" class="count grey" href="#">520</a><span>pending orders</span></li>
        <li><a title="" class="count grey" href="#">14</a><span>new opened tickets</span></li>
        <li class="last"><a title="" class="count grey" href="#">48</a><span>new user registrations</span></li>
      </ul>
      <div class="fix"></div>
    </div>
    <!-- Charts -->
    <div class="widget first">
      <div class="head">
        <h5 class="iGraph">Charts</h5>
      </div>
      <div class="body">
        <div style="width: 700px; height: 200px;" class="chart"></div>
      </div>
    </div>
    <!-- Calendar -->
    <div class="widget">
      <div class="head">
        <h5 class="iDayCalendar">Schedule</h5>
      </div>
      <div id="calendar"></div>
    </div>
    <!-- Full width tabs -->
    <div class="widget">
      <ul class="tabs">
        <li><a href="#tab3">Tab Active</a></li>
        <li><a href="#tab4">Tab Inactive</a></li>
      </ul>
      <div class="tab_container">
        <div class="tab_content" id="tab3">
          <h4 class="aligncenter red pt10">Tab System Manager</h4>
          <p><a title="" href="#">Tab System Manager</a> Will be Placed Here..</p>
        </div>
        <div class="tab_content" id="tab4">This tab is active now</div>
      </div>
      <div class="fix"></div>
    </div>
    <!-- Dynamic table -->
    <div class="table">
      <div class="head">
        <h5 class="iFrames">Page Views: Last 7 Days</h5>
      </div>
      <!-- Begin Page Views -->
      <table cellspacing="0" cellpadding="0" border="0" id="example" class="display">
        <thead>
          <tr>
            <th>Browser</th>
            <th>Landing Page</th>
            <th>Request URI</th>
            <th>City</th>
            <th>Crawler</th>
          </tr>
        </thead>
        <tbody>
        	<?php foreach ($this->getPageViewsStats() as $intIndex => $arrData) { ?>
            <tr class="gradeX">
                <td><?php echo(get_browser($arrData['userAgent'])); ?></td>
                <td><?php echo($arrData['landingPage']); ?></td>
                <td><a class="topDir" href="<?php echo($arrData['requestUri']); ?>" title="Click to visit this page" target="_blank">
					<?php echo(strlen($arrData['requestUri']) > 50 ? substr($arrData['requestUri'], 0, 40) . '...' : $arrData['requestUri']); ?></a></td>
                <td><?php echo($arrData['city']); ?></td>
                <td><?php echo((bool) $arrData['isCrawler'] ? 'Yes' : 'No'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
      </table>
      <!-- Page Views EOF /-->
    </div>
    <!-- Widgets -->
    <div class="widgets">
      <div class="left">
        <!-- Search -->
        <div class="searchWidget">
          <form action="">
            <input type="text" placeholder="Enter search text..." name="search">
            <input type="submit" value="" name="find">
          </form>
        </div>
        <!-- Statistics -->
        <div class="widget">
          <div class="head">
            <h5 class="iChart8">Website statistic</h5>
            <div class="num"><a class="blueNum" href="#">+245</a></div>
          </div>
          <table width="100%" cellspacing="0" cellpadding="0" class="tableStatic">
            <thead>
              <tr>
                <td width="21%">Amount</td>
                <td>Description</td>
                <td width="21%">Changes</td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td align="center"><a class="webStatsLink" title="" href="#">980</a></td>
                <td>returned visitors</td>
                <td><span class="statPlus">0.32%</span></td>
              </tr>
              <tr>

                <td align="center"><a class="webStatsLink" title="" href="#">1545</a></td>
                <td>new registrations</td>
                <td><span class="statMinus">82.3%</span></td>
              </tr>
              <tr>
                <td align="center"><a class="webStatsLink" title="" href="#">457</a></td>
                <td>new affiliates registrations</td>
                <td><span class="statPlus">100%</span></td>
              </tr>
              <tr>
                <td align="center"><a class="webStatsLink" title="" href="#">9543</a></td>
                <td>new visitors</td>
                <td><span class="statPlus">4.99%</span></td>
              </tr>
              <tr>
                <td align="center"><a class="webStatsLink" title="" href="#">354</a></td>
                <td>new pending comments</td>
                <td><span class="statMinus">9.67%</span></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Latest orders -->
        <div class="widget">
          <div class="head">
            <h5 class="iMoney">Latest orders</h5>
            <div class="num"><a class="blueNum" href="#">+245</a></div>
          </div>
          <div class="supTicket nobg">
            <div class="issueType"> <span class="issueInfo"><a title="" href="#">VPS Basic</a></span> <span class="issueNum"><a title="" href="#">[ #21254 ]</a></span>
              <div class="fix"></div>
            </div>
            <div class="issueSummary"> <a class="floatleft" title="" href="#"><img alt="" src="/admin/static/images/user.png"></a>
              <div class="ticketInfo">
                <ul>
                  <li>Current order status:</li>
                  <li class="even"><strong class="green">[ pending ]</strong></li>
                  <li>User email:</li>
                  <li class="even"><a title="" href="#">user@company.com</a></li>
                </ul>
                <div class="fix"></div>
              </div>
              <div class="fix"></div>
            </div>
          </div>
          <div class="supTicket">
            <div class="issueType"> <span class="issueInfo"><a title="" href="#">VPS Basic</a></span> <span class="issueNum"><a title="" href="#">[ #21254 ]</a></span>
              <div class="fix"></div>
            </div>
            <div class="issueSummary"> <a class="floatleft" title="" href="#"><img alt="" src="/admin/static/images/user.png"></a>
              <div class="ticketInfo">
                <ul>
                  <li>Current order status:</li>
                  <li class="even"><strong class="green">[ pending ]</strong></li>
                  <li>User email:</li>
                  <li class="even"><a title="" href="#">user@company.com</a></li>
                </ul>
                <div class="fix"></div>
              </div>
              <div class="fix"></div>
            </div>
          </div>
          <div class="supTicket">
            <div class="issueType"> <span class="issueInfo"><a title="" href="#">VPS Basic</a></span> <span class="issueNum"><a title="" href="#">[ #21254 ]</a></span>
              <div class="fix"></div>
            </div>
            <div class="issueSummary"> <a class="floatleft" title="" href="#"><img alt="" src="/admin/static/images/user.png"></a>
              <div class="ticketInfo">
                <ul>
                  <li>Current order status:</li>
                  <li class="even"><strong class="green">[ pending ]</strong></li>
                  <li>User email:</li>
                  <li class="even"><a title="" href="#">user@company.com</a></li>
                </ul>
                <div class="fix"></div>
              </div>
              <div class="fix"></div>
            </div>
          </div>
        </div>
        <div class="fix"></div>
      </div>
      <!-- Right widgets -->
      <div class="right">
        <!-- Support tickets widget -->
        <div class="widget">
          <div class="head">
            <h5 class="iHelp">Support ticket widget</h5>
            <div class="num"><a class="redNum" href="#">+128</a></div>
          </div>
          <div class="supTicket nobg">
            <div class="issueType"> <span class="issueInfo">General issue</span> <span class="issueNum">[ #21254 ]</span>
              <div class="fix"></div>
            </div>
            <div class="issueSummary"> <a class="floatleft" title="" href="#"><img alt="" src="/admin/static/images/user.png"></a>
              <div class="ticketInfo">
                <ul>
                  <li><a title="" href="#">Avi Aialon</a></li>
                  <li class="even"><strong class="red">[ High priority ]</strong></li>
                  <li>Status: <strong class="green">[ Pending ]</strong></li>
                  <li class="even">Oct 25, 2011  23:12</li>
                </ul>
                <div class="fix"></div>
              </div>
              <div class="fix"></div>
            </div>
          </div>
          <div class="supTicket">
            <div class="issueType"> <span class="issueInfo">General financial issue</span> <span class="issueNum">[ #21254 ]</span>
              <div class="fix"></div>
            </div>
            <div class="issueSummary"> <a class="floatleft" title="" href="#"><img alt="" src="/admin/static/images/user.png"></a>
              <div class="ticketInfo">
                <ul>
                  <li><a title="" href="#">Avi Aialon</a></li>
                  <li class="even"><strong class="green">[ Resolved ]</strong></li>
                  <li>Status: <strong class="green">[ Closed ]</strong></li>
                  <li class="even">Oct 25, 2011  23:12</li>
                </ul>
                <div class="fix"></div>
              </div>
              <div class="fix"></div>
            </div>
          </div>
          <div class="supTicket">
            <div class="issueType"> <span class="issueInfo">General financial issue</span> <span class="issueNum">[ #21254 ]</span>
              <div class="fix"></div>
            </div>
            <div class="issueSummary"> <a class="floatleft" title="" href="#"><img alt="" src="/admin/static/images/user.png"></a>
              <div class="ticketInfo">
                <ul>
                  <li><a title="" href="#">Avi Aialon</a></li>
                  <li class="even"><strong class="blue">[ Low priority ]</strong></li>
                  <li>Status: <strong class="green">[ Pending ]</strong></li>
                  <li class="even">Oct 25, 2011  23:12</li>
                </ul>
                <div class="fix"></div>
              </div>
              <div class="fix"></div>
            </div>
          </div>
        </div>
        <!-- Tabs widget -->
        <div class="widget">
          <ul class="tabs">
            <li><a href="#tab1">Tab 1</a></li>
            <li><a href="#tab2">Tab 2</a></li>
          </ul>
          <div class="tab_container">
            <div class="tab_content" id="tab1">Active tab</div>
            <div class="tab_content" id="tab2">Now this one is active</div>
          </div>
          <div class="fix"></div>
        </div>
        <!-- User widget -->
        <div class="widget">
          <div class="head">
            <div class="userWidget">
              <form action="">
                <input type="checkbox" checked="checked" name="chbox" id="check1">
              </form>
              <a class="userLink" title="" href="#">Avi Aialon</a> </div>
            <div class="num"><span>Balance:</span><a class="greenNum" href="#">+128</a></div>
          </div>
          <table width="100%" cellspacing="0" cellpadding="0" class="tableStatic">
            <tbody>
              <tr class="noborder">
                <td width="50%">Current package:</td>
                <td align="right"><strong class="red">VPS Ultimate</strong></td>
              </tr>
              <tr>
                <td>Paid until:</td>
                <td align="right">Oct 25, 2011   23:12</td>
              </tr>
              <tr>
                <td>User email:</td>
                <td align="right"><a title="" href="#">user@company.com</a></td>
              </tr>
              <tr>
                <td>Support tickets:</td>
                <td align="right"><a class="green" href="#">no pending tickets</a></td>
              </tr>
              <tr>
                <td>Expiring domains:</td>
                <td align="right"><span class="expire">12 domains</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>   
        <?php	
	}
}