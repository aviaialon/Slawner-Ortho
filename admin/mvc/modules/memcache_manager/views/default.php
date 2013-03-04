<?php 
	$Application = ADMIN_APPLICATION::getInstance();
	$arrMemcacheData = $Application->getMemcacheStats();
	$arrSlabData 	 = $Application->getMemcacheKeyData();
?>
	<script type="text/javascript">
		$(document).ready(function() {
			/*------------------------------
			 * Memcache Usage Pie Chart
			 -------------------------------*/
			var data = [];
			data.push({
				label: "Total Available Memory", 
				data: <?php echo $arrMemcacheData['limit_maxbytes']; ?>
			});

			data.push({
				label: "Memory Used (%)", 
				data: <?php echo $arrMemcacheData['bytes']; ?>
			});

			$.plot($("#Memcache_Useage"), data, {
					series: {
						pie: { 
							show: true,
							radius: 3/4,
							innerRadius: 0.3,
							label: {
								show: true,
								radius: 2/3,
								threshold: 0,
								formatter: function(label, series){
			                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+ series.percent +'%</div>';
			                    },
			                    background: {
			                        opacity: 0.5,
			                        color: '#000'
			                    },
			                    highlight : {
			                    	opacity: 0.5 
			                    },
			                    offset : {
									top: 5
			                    }
							}
						} 
					},
					legend: {
						show: true,
						noColumns: 1,
						opacity: 0.5,
						margin: [0,5],
						position: "nw"
					},
					grid: {
						hoverable: false,
						clickable: true
					}
			});


			/*------------------------------
			 * Memcache Hits Pie Chart
			 -------------------------------*/
			var hitsData = [];
			
			hitsData.push({
				label: "Total Gets", 
				smlabel: "T. Gets", 
				data: <?php echo $arrMemcacheData['cmd_get']; ?>
			});

			hitsData.push({
				label: "Total Sets", 
				smlabel: "T. Sets", 
				data: <?php echo $arrMemcacheData['cmd_set']; ?>
			});

			hitsData.push({
				label: "Total Hits", 
				smlabel: "T. Hits", 
				data: <?php echo $arrMemcacheData['get_hits']; ?>
			});

			hitsData.push({
				label: "Total Misses", 
				smlabel: "T. Missses", 
				data: <?php echo $arrMemcacheData['get_misses']; ?>
			});

			hitsData.push({
				label: "Total Delete Misses", 
				smlabel: "T. D. Gets", 
				data: <?php echo $arrMemcacheData['delete_misses']; ?>
			});

			hitsData.push({
				label: "Total Inc Misses", 
				smlabel: "T. Inc. Gets", 
				data: <?php echo $arrMemcacheData['incr_misses']; ?>
			});

			hitsData.push({
				label: "Total Inc Hits", 
				smlabel: "T. Inc. Hits", 
				data: <?php echo $arrMemcacheData['incr_hits']; ?>
			});

			hitsData.push({
				label: "Total Decrease Misses", 
				smlabel: "T. Dec. Mis", 
				data: <?php echo $arrMemcacheData['decr_misses']; ?>
			});

			hitsData.push({
				label: "Total Decrease Hits", 
				smlabel: "T. Dec. Hits", 
				data: <?php echo $arrMemcacheData['decr_hits']; ?>
			});
			
			$.plot($("#Memcache_History"), hitsData, {
					series: {
						pie: { 
							show: true,
							radius: 3/4,
							innerRadius: 0.3,
							label: {
								show: true,
								radius: 2/3,
								threshold: 0,
								formatter: function(label, series){
			                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+ series.data[0][1] +'</div>';
			                    },
			                    background: {
			                        opacity: 0.5,
			                        color: '#000'
			                    },
			                    highlight : {
			                    	opacity: 0.5 
			                    },
			                    offset : {
									top: 5
			                    }
							}
						}
					},
					legend: {
						show: true,
						noColumns: 1,
						opacity: 0.5,
						margin: [-10,5],
						position: "nw",
						labelFormatter: function (label, series) {
							return (series.label);
						}
					},
					grid: {
						hoverable: true,
						clickable: true
					}
			});
		});
	</script>
		<div class="stats">
        	<ul>
            	<li><a title="" class="count grey" href="#"><?php echo $arrMemcacheData['total_connections']; ?></a><span>Total Conns.</span></li>
                <li><a title="" class="count orange" href="#"><?php echo $arrMemcacheData['curr_connections']; ?></a><span>Current Conns.</span></li>
                <li><a title="" class="count green" href="#"><?php echo $arrMemcacheData['get_hits']; ?></a><span>Get Hits</span></li>
                <li class="last"><a title="" class="count red" href="#"><?php echo $arrMemcacheData['get_misses']; ?></a><span>Get Misses</span></li>
            </ul>
            <div class="fix"></div>
            <!--
            <ul>
            	<li><a title="" class="count grey" href="#"><?php echo $arrMemcacheData['total_items']; ?></a><span>Total Items.</span></li>
            	<li><a title="" class="count grey" href="#"><?php echo $this->getMemcache()->bsize($arrMemcacheData['limit_maxbytes']); ?></a><span>Memory Alloc.</span></li>
            	<li><a title="" class="count grey" href="#"><?php echo $this->getMemcache()->bsize($arrMemcacheData['bytes']); ?></a><span>Memory In Use.</span></li>
            </ul>
            <div class="fix"></div>
        	-->
        </div>
        <a name="stats"></a>
		<div class="widgets">
            <div class="left">
                <div class="widget"><!-- Pie chart 1 -->
            	<div class="head"><h5 class="iChart8">Memcache Usage (MB)</h5></div>
                <div class="body">
                	<div id="Memcache_Useage" style="width: 316px; height: 316px;"></div>
					</div>
	            </div>
            </div>
            
            <div class="right">
                <div class="widget"><!-- Pie chart 2 -->
                    <div class="head"><h5 class="iChart8">Memcache History</h5></div>
                    <div class="body">
                       <div id="Memcache_History" style="width: 316px; height: 316px;"></div>
                    </div>
                </div>
            </div>
            <div class="fix"></div>
        </div>
        
        <!-- Begin Memcache Server Controls -->
        <a name="flush"></a>
        <div class="widgets">
	      <div class="left">
	        <!-- Flush Server -->
	        <div class="widget">
	          <div class="head">
	            <h5 class="iChart8">Flush Memcache Server</h5>
	            <div class="num"><a class="blueNum" href="#">+<?php echo $arrMemcacheData['total_items']; ?> Total Items</a></div>
	          </div>
	          <br />
	          <center>
	          	 <a class="btnIconLeft mr10 leftDir" original-title="Click here to flush the current memcache server" title="" href="#" rel="flush-server">
		          	<img class="icon" alt="" src="/admin/static/images/icons/dark/laptop.png"><span>Flush Memcache Server</span>
		         </a>
	          </center>
	          <br />
	        </div>
	        <div class="fix"></div>
	      </div>
	      
	      <!-- Right widgets -->
	      <a name="life-stats"></a>
	      <div class="right">
	        <div class="widget">
	          <div class="head">
	            <div class="userWidget">
	              <form action="">
	                <input type="checkbox" checked="checked" name="chbox" id="check1">
	              </form>
	              <a class="userLink" title="" href="#">Stats</a> </div>
	            <div class="num">
	            	<a class="greenNum" href="#"><?php echo ($this->getMemcache()->duration($arrMemcacheData['time']-$arrMemcacheData['uptime']));?></a>
	            </div>
	          </div>
	          <table width="100%" cellspacing="0" cellpadding="0" class="tableStatic">
	            <tbody>
	              <tr class="noborder">
	                <td width="50%">Start Time:</td>
	                <td align="right"><strong><?php echo date('F jS, Y g:i:sA T', $arrMemcacheData['time'] - $arrMemcacheData['uptime']); ?></strong></td>
	              </tr>
	              <tr>
	                <td>Memcache Version:</td>
	                <td align="right"><?php echo $arrMemcacheData['version']; ?></td>
	              </tr>
	              <tr>
	                <td>Reclaimed Items:</td>
	                <td align="right"><a title="" href="#" class="red"><?php echo $arrMemcacheData['reclaimed']; ?></a></td>
	              </tr>
	              <tr>
	                <td>Evictions:</td>
	                <td align="right"><span class="expire"><?php echo $arrMemcacheData['evictions']; ?></span></td>
	              </tr>
	              <tr>
	                <td>Active Threads:</td>
	                <td align="right"><a class="green" href="#"><?php echo $arrMemcacheData['threads']; ?></a></td>
	              </tr>
	            </tbody>
	          </table>
	        </div>
	      </div>
	    </div>   
	    <div class="fix"></div>
        <!-- /Begin Memcache Server Controls EOF -->
        
        <!-- Begin Memcache Keys -->
	     <a name="data"></a>
         <div class="table">
            <div class="head"><h5 class="iFrames">Memcache Keys (Key Namespace: <?php echo(str_replace('::', '', $Application->getSiteMemcachePrefixKey())); ?>)</h5></div>
            <div class="dataTables_wrapper" id="example_wrapper">
            	<div class="">
            		<div class="dataTables_filter" id="example_filter">
            			<label>
            				Search: 
            				<input type="text" placeholder="type here...">
            				<div class="srch"></div>
            			</label>
            		</div>
            	</div>


		      <table cellspacing="0" cellpadding="0" border="0" id="example" class="display">
		        <thead>
		          <tr>
		            <th>Server IP</th>
		            <th>Key</th>
		            <th>SlabID</th>
		            <th>&nbsp;</th>
		          </tr>
		        </thead>
		        <tbody>
		        <?php 
		        	foreach ($arrSlabData as $strKey => $arrData) { 
		        		// Show only the memcache keys available to the current site,
		        		$blnShow = true;
		        		if (
		        			($Application->getSiteMemcachePrefixKey()) &&
							(substr($strKey, 0, strlen($Application->getSiteMemcachePrefixKey())) !== $Application->getSiteMemcachePrefixKey()) &&
							(strcmp($strKey, 'VERION_KEY') <> 0)
		        		) {
		        			$blnShow = false;
		        		}
		        		
		        		if ($blnShow) {
		        ?>
			            <tr class="gradeX" id="tr_<?php echo(base64_encode($arrData['key'])); ?>">
			                <td><?php echo($arrData['server']); ?></td>
			                <td><a 	href='#' 
			                		class="rightDir"
			                		rel="memcacheKeyLink"
			                		data-key="<?php echo($arrData['key']);?>" 
									original-title="Click to see data">
								<?php $strDisKey = ( 
									($arrData['key'] !== 'VERION_KEY') ? (substr($arrData['key'], strlen($Application->getSiteMemcachePrefixKey()))) :  $arrData['key']
								); ?>	
			                	<?php echo substr($strDisKey, 0, 50) . (strlen($strDisKey) > 50 ? '...' : ''); ?></a></td>
			                <td><?php echo($arrData['slabId']); ?></td>
			                <td align="center">
			                	<a 	original-title="Delete This Key" style="padding:8px 3px 3px 3px" rel="mkey-delete" 
			                		class="btn14 leftDir" data-key="<?php echo $arrData['key']; ?>" title="" href="#"><img alt="" src="/admin/static/images/icons/dark/trash.png"></a>
			                </td>	
			            </tr>
	            <?php 
		        		}
		        	} 
		        ?>
	        </tbody>
	      </table>
	    </div>
	  </div>    
      <!-- Memcache Keys EOF /-->
      <script type='text/javascript'>
      	$(document).ready(function() {
      		/*
			 * View memcache data  
			 */
			$('a[rel=memcacheKeyLink]').live('click', function(event) {
				event.preventDefault();
				gbox.show({
					type: 'ajax',
					url: site_url('memcache-key/' + encodeURI(base64_encode($(this).attr('data-key'))))
				});
			});

			/*
			 * Delete Memcache Key  
			 */
			$('a[rel=mkey-delete]').live('click', function(event) {
				event.preventDefault();
				var memcacheKey = $(this).attr('data-key');
				var tableRow 	= $("tr#tr_" + base64_encode(memcacheKey));
				jConfirm(
					'<h2>Delete This Key</h2>Are you sure you want to delete this Memcache key / value?<br /><br />', 
					'Delete This Key?', function(blnConfirmedTrue) 
					{
						if (blnConfirmedTrue)
						{
							$.post(site_url('key-delete/' + base64_encode(memcacheKey)), {}, function(data) {
								if (data.success) {
									tableRow.remove();
									$.jGrowl('This key was removed', { header: 'Key Deleted' });
								} else {
									$.jGrowl('Failed to delete this key.', { header: 'Deleted Failed.' });
								}
							});
						}
					}
				);
			});
			
			/*
			 * Flush Memcache Server Data  
			 */
			$('a[rel=flush-server]').live('click', function(event) {
				event.preventDefault();
				var memcacheKey = $(this).attr('data-key');
				var tableRow 	= $("tr#tr_" + base64_encode(memcacheKey));
				jConfirm(
					'<h2>Delete This Key</h2>Are you sure you want to flush the memcache server? This action <strong>NOT UN-DOABLE</strong><br /><br />', 
					'Flush The memcache Server?', function(blnConfirmedTrue) 
					{
						if (blnConfirmedTrue)
						{
							$.post(site_url('server-flush/'), {}, function(data) {
								if (data.success) {
									tableRow.remove();
									$.jGrowl('The server was flushed. Please refresh the page.', { header: 'Server Flushed.' });
								} else {
									$.jGrowl('Failed to flush the memcache server.', { header: 'Flush Failed.' });
								}
							});
						}
					}
				);
			});
	    });
	</script>