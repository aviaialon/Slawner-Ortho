<?php
/* End of functions.php */
?>
<link rel="stylesheet" href="<?php echo  $arrRequestParams['View_Data']['Resource_Path']; ?>/assets/css/style.css"> 
<script>
	var current_group_id = <?php echo($arrRequestParams['View_Data']['Int_Current_Active_Group']); ?>;
</script>
<script src="<?php echo  $arrRequestParams['View_Data']['Resource_Path']; ?>/assets/js/interface-1.2.js"></script>
<script src="<?php echo  $arrRequestParams['View_Data']['Resource_Path']; ?>/assets/js/inestedsortable.js"></script>
<script src="<?php echo  $arrRequestParams['View_Data']['Resource_Path']; ?>/assets/js/menu.js"></script>
<script type="text/javascript">
	var navigate = function(__url__) {
		var objUrl = new LEGACY.NET.URL('/admin');
			objUrl.appendPath(_CONTROLLER);
			objUrl.appendPath(_ACTION);
			objUrl.appendPath(__url__);
			objUrl.setAttribute('authToken', _AUTH_TOKEN);
		window.location.href = objUrl.toString();
	};
</script>
<!-- Full width tabs -->
<div class="widget tabsRight">
  <div class="head">
      <h5 class="iList">Menu Editor</h5>	
  </div>
  <ul class="tabs" id="menu-group">
  	<!--
    <li class="addMenu">
    	<a href="#"><span><img src="/admin/static/images/icons/color/application-plus.png" alt="" class="icon" align="bottom" style="" /></span></a>
    </li>
    -->
    <?php 
    	foreach ((array) $arrRequestParams['View_Data']['Array_Menu_Groups'] as $intIndex => $arrGroupData) 
    	{
    		$strTabHref = 'edit-group/' . (int) $arrGroupData['id'];
    ?>
    	<li rel="tab<?php echo ((int) $arrGroupData['id']); ?>" id="group-<?php echo ((int) $arrGroupData['id']); ?>" 
            class="<?php echo (((int) $arrGroupData['id']) === $arrRequestParams['View_Data']['Int_Current_Active_Group'] ? 'activeTab' : ''); ?>">
    		<a href="#tab<?php echo ((int) $arrGroupData['id']); ?>" onclick="javascript:navigate('<?php echo ($strTabHref); ?>');">
    			<?php echo ($arrGroupData['title']); ?>
    		</a>
    	</li>
	<?php } ?>
  </ul>
  <div class="tab_container">
  	
    <div class="padding10 floatright paddingBottom0">
        <a href="#" title="" rel="Add_Menu" class="btn14 mr5 topDir" original-title="Add a New Menu">
            <img src="/admin/static/images/icons/color/application-plus.png" alt=""></a>
        <a href="#" onclick="javascript: $('#form-menu').submit(); return false;" title="" class="btn14 mr5 topDir" original-title="Update Menu Positions">
            <img src="/admin/static/images/icons/color/pencil.png" alt=""></a>
    </div>
                
    <?php 
    foreach ((array) $arrRequestParams['View_Data']['Array_Menu_Groups'] as $intIndex => $arrGroupData)
    {
    	echo ('<div class="tab_content" id="tab' . (int) $arrGroupData['id'] . '">');
		
    	if (((int) $arrGroupData['id']) === $arrRequestParams['View_Data']['Int_Current_Active_Group'])
    	{
			//
			// The current Active Tab
			//
    		?>
		     <!-- <h4 class="aligncenter red pt10">Edit Your Menu! Go Nuts!</h4> -->
             <form method="post" id="form-menu" action="<?php echo  $arrRequestParams['View_Data']['Str_Save_Menu_Position_Url']; ?>">
             	<input type="hidden" name="menu-group-id" id="menu-group-id" value="<?php echo (int) $arrGroupData['id']; ?>" />
                <!-- 
                <div class="ns-row" id="ns-header">
                    <div class="ns-actions">Actions</div>
                    <div class="ns-class">Class</div>
                    <div class="ns-url">URL</div>
                    <div class="ns-title">Title</div>
                </div>
                -->
                <div class="rowElem black mr10">
                    <?php echo $arrRequestParams['View_Data']['Str_Menu_Html']; ?>                        
                </div>
                <br />
                <div class="rowElem alignRight">
                	<a href="#" title="" class="btnIconLeft mr10 mt5 topDir" rel="Add_Menu" original-title="Add a New Menu">
                		<img src="/admin/static/images/icons/color/application-plus.png" alt="" class="icon"><span style="font-size:8pt">New Menu</span></a>
                		
                	<a href="#"onclick="javascript: $('#form-menu').submit(); return false;" title="" class="btnIconLeft mr10 mt5 topDir" original-title="Update Menu Positions">
                		<img src="/admin/static/images/icons/color/pencil.png" alt="" class="icon"><span style="font-size:8pt">Save Positions</span></a>
                			
                    <!-- 
                    <a href="#" title="" rel="Add_Menu" class="btn14 mr5 topDir" original-title="Add a New Menu">
                    	<img src="/admin/static/images/icons/color/application-plus.png" alt=""></a>
                    <a href="#" onclick="javascript: $('#form-menu').submit(); return false;" title="" class="btn14 mr5 topDir" original-title="Update Menu Positions">
                    	<img src="/admin/static/images/icons/color/pencil.png" alt=""></a>
                    -->
                </div>
            </form>
    		<?php 
    	}
    	else
    	{
			//
			// The other tabs with the 'please wait' loader..
			// TODO: Please remove this crap and find a better solution.
			//
    		?>
    			<center>Pleae Wait...<br />
    			<img class="p12" alt="" src="/admin/static/images/loaders/loader8.gif"></center>
    		<?php 	
    	}
    	
    	echo ('</div>');
    }
	?>		

  </div>
  <div class="fix"></div>
</div>
<!-- Dynamic table -->
<?php
 /*
<div class="box info">
	<h2>Info</h2>
	<section>
		<p>Drag the menu list to re-order, and click <b>Update Menu</b> to save the position.</p>
		<p>To add a menu, use the <b>Add Menu</b> form below.</p>
	</section>
</div>
<div class="box">
	<h2>Current Menu Group</h2>
	<section>
		<span id="edit-group-input"><?php echo $arrRequestParams['View_Data']['Str_Current_Active_Group_Title']; ?></span>
		(ID: <b><?php echo $arrRequestParams['View_Data']['Int_Current_Active_Group']; ?></b>)
		<div>
			<a id="edit-group" href="#">Edit</a>
			<?php if ($arrRequestParams['View_Data']['Int_Current_Active_Group'] > 1) : ?>
			&middot; <a id="delete-group" href="#">Delete</a>
			<?php endif; ?>
		</div>
	</section>
</div>
<div class="box">
	<h2>Add Menu</h2>
	<section>
		<form id="form-add-menu" method="post" action="#menu.add">
			<p>
				<label for="menu-title">Title</label>
				<input type="text" name="title" id="menu-title">
			</p>
			<p>
				<label for="menu-url">URL</label>
				<input type="text" name="url" id="menu-url">
			</p>
			<p>
				<label for="menu-class">Class</label>
				<input type="text" name="class" id="menu-class">
			</p>
			<p class="buttons">
				<input type="hidden" name="group_id" value="<?php echo $arrRequestParams['View_Data']['Int_Current_Active_Group']; ?>">
				<button id="add-menu" type="submit" class="button green small">Add Menu</button>
			</p>
		</form>
	</section>
</div>
<div id="loading">
	<img src="<?php echo  $arrRequestParams['View_Data']['Resource_Path']; ?>assets/images/ajax-loader.gif" alt="Loading">
	Processing...
</div>
*/ ?>
<!--


	/**
	 * Add menu action
	 * For use with ajax
	 * Return json data
	 */
	function add() {
		if (isset($_POST['title'])) {
			$data[MENU_TITLE] = trim($_POST['title']);
			if (!empty($data[MENU_TITLE])) {
				$data[MENU_URL] = $_POST['url'];
				$data[MENU_CLASS] = $_POST['class'];
				$data[MENU_GROUP] = $_POST['group_id'];
				$data[MENU_POSITION] = $this->get_last_position($_POST['group_id']) + 1;
				if ($this->db->insert(MENU_TABLE, $data)) {
					$data[MENU_ID] = $this->db->Insert_ID();
					$response['status'] = 1;
					$li_id = 'menu-'.$data[MENU_ID];
					$response['li'] = '<li id="'.$li_id.'" class="sortable">'.$this->get_label($data).'</li>';
					$response['li_id'] = $li_id;
				} else {
					$response['status'] = 2;
					$response['msg'] = 'Add menu error.';
				}
			} else {
				$response['status'] = 3;
			}
			header('Content-type: application/json');
			echo json_encode($response);
		}
	}

	/**
	 * Show edit menu form
	 */
	function edit() {
		if (isset($_GET['id'])) {
			$id = (int)$_GET['id'];
			$data['row'] = $this->get_row($id);
			$this->view('menu_edit', $data);
		}
	}

	/**
	 * Save menu
	 * Action for edit menu
	 * return json data
	 */
	function save() {
		if (isset($_POST['title'])) {
			$data[MENU_TITLE] = trim($_POST['title']);
			if (!empty($data[MENU_TITLE])) {
				$data[MENU_ID] = $_POST['menu_id'];
				$data[MENU_URL] = $_POST['url'];
				$data[MENU_CLASS] = $_POST['class'];
				if ($this->db->update(MENU_TABLE, $data, MENU_ID . ' = ' . $data[MENU_ID])) {
					$response['status'] = 1;
					$d['title'] = $data[MENU_TITLE];
					$d['url'] = $data[MENU_URL];
					$d['klass'] = $data[MENU_CLASS]; //klass instead of class because of an error in js
					$response['menu'] = $d;
				} else {
					$response['status'] = 2;
					$response['msg'] = 'Edit menu error.';
				}
			} else {
				$response['status'] = 3;
			}
			header('Content-type: application/json');
			echo json_encode($response);
		}
	}

	/**
	 * Delete menu action
	 * Also delete all submenus under current menu
	 * return json data
	 */
	function delete() {
		if (isset($_POST['id'])) {
			$id = (int)$_POST['id'];

			$this->get_descendants($id);
			if (!empty($this->ids)) {
				$ids = implode(', ', $this->ids);
				$id = "$id, $ids";
			}

			$sql = sprintf('DELETE FROM %s WHERE %s IN (%s)', MENU_TABLE, MENU_ID, $id);
			$delete = $this->db->Execute($sql);
			if ($delete) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
			header('Content-type: application/json');
			echo json_encode($response);
		}
	}

	/**
	 * Save menu position
	 */
	function save_position() {
		if (isset($_POST['easymm'])) {
			$easymm = $_POST['easymm'];
			$this->update_position(0, $easymm);
		}
	}

	/**
	 * Recursive function for save menu position
	 */
	function update_position($parent, $children) {
		$i = 1;
		foreach ($children as $k => $v) {
			$id = (int)$children[$k]['id'];
			$data[MENU_PARENT] = $parent;
			$data[MENU_POSITION] = $i;
			$this->db->update(MENU_TABLE, $data, MENU_ID . ' = ' . $id);
			if (isset($children[$k]['children'][0])) {
				$this->update_position($id, $children[$k]['children']);
			}
			$i++;
		}
	}

	/**
	 * Get items from menu table
	 *
	 * @param int $group_id
	 * @return array
	 */
	function get_menu($group_id) {
		$sql = sprintf(
			'SELECT * FROM %s WHERE %s = %s ORDER BY %s, %s',
			MENU_TABLE,
			MENU_GROUP,
			$group_id,
			MENU_PARENT,
			MENU_POSITION
		);
		return $this->db->GetAll($sql);
	}

	/**
	 * Get one item from menu table
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	function get_row($id) {
		$sql = sprintf(
			'SELECT * FROM %s WHERE %s = %s',
			MENU_TABLE,
			MENU_ID,
			$id
		);
		return $this->db->GetRow($sql);
	}

	/**
	 * Recursive method
	 * Get all descendant ids from current id
	 * save to $this->ids
	 *
	 * @param int $id
	 */
	function get_descendants($id) {
		$sql = sprintf(
			'SELECT %s FROM %s WHERE %s = %s',
			MENU_ID,
			MENU_TABLE,
			MENU_PARENT,
			$id
		);
		$data = $this->db->GetCol($sql);

		if (!empty($data)) {
			foreach ($data as $v) {
				$this->ids[] = $v;
				$this->get_descendants($v);
			}
		}
	}

	/**
	 * Get the highest position number
	 *
	 * @param int $group_id
	 * @return string
	 */
	function get_last_position($group_id) {
		$sql = sprintf(
			'SELECT MAX(%s) FROM %s WHERE %s = %s',
			MENU_POSITION,
			MENU_TABLE,
			MENU_GROUP,
			$group_id
		);
		return $this->db->GetOne($sql);
	}

	/**
	 * Get all items in menu group table
	 *
	 * @return array
	 */
	function get_menu_groups() {
		$sql = sprintf(
			'SELECT %s, %s FROM %s',
			MENUGROUP_ID,
			MENUGROUP_TITLE,
			MENUGROUP_TABLE
		);
		return $this->db->GetAssoc($sql);
	}

	/**
	 * Get menu group title
	 *
	 * @param int $group_id
	 * @return string
	 */
	function get_menu_group_title($group_id) {
		$sql = sprintf(
			'SELECT %s FROM %s WHERE %s = %s',
			MENUGROUP_TITLE,
			MENUGROUP_TABLE,
			MENUGROUP_ID,
			$group_id
		);
		return $this->db->GetOne($sql);
	}

	/**
	 * Get label for list item in menu manager
	 * this is the content inside each <li>
	 *
	 * @param array $row
	 * @return string
	 */
	
-->