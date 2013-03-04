<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once(__APPLICATION_ROOT__ . '/database/database.php'); ?>
<?php $objDb = DATABASE::getInstance(); ?>
<link href='http://fonts.googleapis.com/css?family=Cuprum' rel='stylesheet' type='text/css'>
<style type="text/css">
	* {font-family: 'Cuprum', sans-serif;} 
	a.tag_button, input[type=submit].tag_button {
	display:inline-block;
	width:auto;
	height:30px;
	line-height:30px;
	padding:0 9px;
	font-family:Helvetica, Arial, sans-serif;
	cursor:pointer;
	text-decoration:none;
	font-size:12px;
	font-weight:bold;
	color:#f4f4f4;
	text-shadow:0 1px 0 rgba(0, 0, 0, .25);
	text-align:center;
	border:1px solid #56636f;
	background:#7a8996;
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#97a7ae, endColorstr=#667385);
	background-image:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #97a7ae), color-stop(100%, #667385));
	background-image:-webkit-linear-gradient(center top, #97a7ae 0, #667385 100%);
	background-image:-moz-linear-gradient(center top, #97a7ae 0, #667385 100%);
	background-image:-o-linear-gradient(center top, #97a7ae 0, #667385 100%);
	background-image:-ms-linear-gradient(center top, #97a7ae 0, #667385 100%);
	background-image:linear-gradient(center top, #97a7ae 0, #667385 100%);
	-webkit-border-radius:5px;
	-khtml-border-radius:5px;
	-moz-border-radius:5px;
	border-radius:5px;
	-moz-box-shadow:0 2px 3px rgba(0, 0, 0, .24);
	-webkit-box-shadow:0 2px 3px rgba(0, 0, 0, .24);
	box-shadow:0 2px 3px rgba(0, 0, 0, .24)
}
a.tag_button:hover, input[type=submit].tag_button:hover {
filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#a7b9c1, endColorstr=#667385);
	background-image:-webkit-gradient(linear, left top, left bottom, color-stop(0%, #a7b9c1), color-stop(100%, #667385));
	background-image:-webkit-linear-gradient(center top, #a7b9c1 0, #667385 100%);
	background-image:-moz-linear-gradient(center top, #a7b9c1 0, #667385 100%);
	background-image:-o-linear-gradient(center top, #a7b9c1 0, #667385 100%);
	background-image:-ms-linear-gradient(center top, #a7b9c1 0, #667385 100%);
	background-image:linear-gradient(center top, #a7b9c1 0, #667385 100%)
}
</style>
</head>

<body style="margin:25px;background-color:#F7F7F7">
<?php if (! isset($_POST['go'])) { ?>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><img src="http://www.wincent.com/a/gfx/install-icon.jpg" align="bottom" /></td>
		<td valign="top"><h1><?php print strtoupper(__SITE_NAME__); ?> COMPILER</h1></td>
	</tr>
</table>
<div style="border-top:solid 1px #fff">
	<div style="border-top:solid 1px #eee"></div>
</div>

<p>Welcome to the <?php print ucwords(__SITE_NAME__); ?> site compiler.<br />
We will now compile and install the required components for the site. <br />
To continue, click on the submit button to begin installation.<br />
<span style="color:#900">* Please note that the <?php print ucwords(__SITE_NAME__); ?> site compiler will require the following GRANTS on the database:
<ul style="font-size:11px;color:#900">
	<li>DROP</li>
	<li>CREATE</li>
	<li>DELETE</li>
	<li>SELECT</li>
</ul>
</span></p>

	<div style="padding:1px;border:solid 1px #fff;width:450px;height:auto">
		<div style="border:solid 1px #F7F7F7;background-color:#fff;padding:15px;width:450px;color:#666;font-size:12px;">
			<p style="font-size:15px;color:#666;font-weight:bold"><u>IMPORTANT NOTICE</u></p>
			Please review the information below. if this information is not correct, please update the <b>config.php</b> file.<br /><br />
			<center>
				<!-- Begin Info -->
				<div style="height:auto;text-align:left">
                    <b>CURRENT DATABASE INFORMATION:</b>		  
                   <div style="padding:10px;padding-top:13px;font-family:arial;font-size:13px">
                       <ul title="Database">
                            <li><span style="color:#888">Database:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __DATABASE__; ?></em></strong></li>
                            <li><span style="color:#888">Database Host:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __DATABASE_HOST__; ?></em></strong></li>
                            <li><span style="color:#888">Database Port:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __DATABASE_PORT__; ?></em></strong></li>
                            <li><span style="color:#888">Database User:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __DATABASE_UNAME__; ?></em></strong></li>
                            <li><span style="color:#888">Database Pass:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __DATABASE_PASS__; ?></em></strong></li>
                       </ul>
                       <ul title="Site">
                            <li><span style="color:#888">Site Information</span></li>
                            <li><span style="color:#888">Site Root:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __SITE_ROOT__; ?></em></strong></li>
                            <li><span style="color:#888">Root Path:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __ROOT_PATH__; ?></em></strong></li>
                            <li><span style="color:#888">Root URL:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __ROOT_URL__; ?></em></strong></li>
                            <li><span style="color:#888">SSL Root:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __SSL_ROOT__; ?></em></strong></li>
                            <li><span style="color:#888">SSL URL:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __SSL_URL__; ?></em></strong></li>
                            <li><span style="color:#888">Site Domain:</span>&nbsp;&nbsp;&nbsp;<strong><em><?php echo __SITE_DOMAIN__; ?></em></strong></li>
                       </ul>
                  </div>
                  <br>
                </div>
				<div style="clear:both">&nbsp;</div>
				<!--- Info EOF -->
			</center>
		</div>
	</div>
<br />
<br />

<form method="post">
	<input type="hidden" value="go" name="go" />
	<input type="submit" class="tag_button" value="BEGIN COMPILATION" />
</form>
<?php } else { ?>
<div id="result" style="font-weight:bold;">Please wait as we compile <?php print ucwords(__SITE_NAME__); ?></div>
<br />
<br />
<hr /> 

<h2>System Installation Progress:</h2>
<?php
error_reporting(E_ALL);


/**
	DRTOP AND CREATE THE DATABASE {{{
**/
mysql_pconnect(
	__DATABASE_HOST__.':'.__DATABASE_PORT__, 
	__DATABASE_UNAME__, 
	__DATABASE_PASS__
) or die("FATAL ERROR [@CONNECT]: Unable to connect to MySQL -"  . mysql_error());
 
mysql_query("
	DROP DATABASE IF EXISTS " . __DATABASE__ 			
);
if (mysql_errno() > 0) {
	print "Error Dropping Database " . __DATABASE__ .": ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
} else {
	print "<div style='background-color:#eee;border:solid 1px #ccc;padding:10px'><p style='font-size:16px;color:green'>Dropped Database: " . __DATABASE__ . " successfully</p><br /><br /><br /></div><br />";
}

mysql_query("
	CREATE DATABASE " . __DATABASE__ 			
);
if (mysql_errno() > 0) {
	print "Error Creating Database " . __DATABASE__ .": ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
} else { 
	print "<div style='background-color:#eee;border:solid 1px #ccc;padding:10px'><p style='font-size:16px;color:green'>Created Database: " . __DATABASE__ . " successfully</p><br /><br /><br /></div><br />";
}

mysql_select_db(__DATABASE__);
// }}}


/*
$settings_dir = __SITE_ROOT__ . "/search/settings";
include "$settings_dir/database.php";

$error = 0;
mysql_query("create table `".$mysql_table_prefix."sites`(
	site_id int auto_increment not null primary key,
	url varchar(255),
	title varchar(255),
	short_desc text,
	indexdate date,
	spider_depth int default 2,
	required text,
	disallowed text,
	can_leave_domain bool)");
if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}
mysql_query("create table `".$mysql_table_prefix."links` (
	link_id int auto_increment primary key not null,
	site_id int,
	url varchar(255) not null,
	title varchar(200),
	description varchar(255),
	fulltxt mediumtext,
	indexdate date,
	size float(2),
	md5sum varchar(32),
	key url (url),
	key md5key (md5sum),
	visible int default 0, 
	level int)");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}
mysql_query("create table `".$mysql_table_prefix."keywords`	(
	keyword_id int primary key not null auto_increment,
	keyword varchar(30) not null,
	unique kw (keyword),
	key keyword (keyword(10)))");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}

for ($i=0;$i<=15; $i++) {
	$char = dechex($i);
	mysql_query("create table `".$mysql_table_prefix."link_keyword$char` (
		link_id int not null,
		keyword_id int not null,
		weight int(3),
		domain int(4),
		key linkid(link_id),
		key keyid(keyword_id))");

	if (mysql_errno() > 0) {
		print "Error: ";
		print mysql_error();
		print "<br>\n";
		$error += mysql_errno();
	}
}

mysql_query("create table `".$mysql_table_prefix."categories` (
	category_id integer not null auto_increment primary key, 
	category text,
	parent_num integer
	)");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}

mysql_query("create table `".$mysql_table_prefix."site_category` (
	site_id integer,
	category_id integer
	)");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}

mysql_query("create table `".$mysql_table_prefix."temp` (
	link varchar(255),
	level integer,
	id varchar (32)
	)");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}

mysql_query("create table `".$mysql_table_prefix."pending` (
	site_id integer,
	temp_id varchar(32),
	level integer,
	count integer,
	num integer
)");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}

mysql_query("create table `".$mysql_table_prefix."query_log` (
	query varchar(255),
	time timestamp(14),
	elapsed float(2),
	results int, 
	key query_key(query))");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}

mysql_query("create table `".$mysql_table_prefix."domains` (
	domain_id int auto_increment primary key not null,	
	domain varchar(255))");

if (mysql_errno() > 0) {
	print "Error: ";
	print mysql_error();
	print "<br>\n";
	$error += mysql_errno();
}


if ($error >0) {
	print "<b>Creating tables failed. Consult the above error messages.</b><br /><hr />";
} else {
	print "<div style='background-color:#eee;border:solid 1px #ccc;padding:10px'><p style='font-size:16px;color:green'><b>Creating tables successfully completed. You will need to start indexing page to enable searching. <a href='/admin/?menu=7'>Click here to start indexing pages</a>.</div><br />";
}

*/
?>

<?php  
	try {
		$handle 	= @fopen(__SITE_ROOT__ . '/install/database.sql', 'r');
		$arrQueries = array();
		$query 		= null;
		if ($handle) {
			while (!feof($handle)) {
				$query .= fgets($handle, 4096);
				if (substr(rtrim($query), -1) == ';') {
					// ...run your query, then unset the string
					$arrQueries[] = $query;
					$query = '';
				}
			}
			fclose($handle);
		}
		foreach ($arrQueries as $intIndex => $strSql) {
			print "<br /><b>RUNNING:</b> <textarea style='width:580px;height:150px;'>" . $strSql . "</textarea><hr />"; 	
			if (strlen(trim($strSql)) > 3) $objDb->query($strSql);
		}
		/*
		$queries = explode(";||", $strSqlQString); 
		foreach ($queries as $strSql) {
			print "<br /><b>RUNNING:</b> <textarea style='width:580px;height:150px;'>" . $strSql . "</textarea><hr />"; 	
			if (strlen(trim($strSql)) > 3) $objDb->query($strSql);
		}
		*/
		
		//exec("mysql -u {__DATABASE_UNAME__} -p {__DATABASE_PASS__} -D {__DATABASE__} < " . __SITE_ROOT__ . '/database/database.sql'); 
	} catch (Exception $e) {
		print "ERROR IN CREATING TABLE STEP 1 - " . $e->getMessage();	
	} 
?>
<script language="javascript">
	document.getElementById('result').innerHTML = "<div style='background-color:#eee;border:solid 1px #ccc;padding:10px'><p style='font-size:16px;color:green'>You have successfully installed <?php print __SITE_NAME__;?>.</p><p>This file will delete itself.</p><p><a href='<?php print __ROOT_URL__;?>'>Click here to return to the <?php print __SITE_NAME__;?> home page.</a></p><br /><br /><br /></div>";
</script> 
<?php
	function delete_directory($dirname) {
	   if (is_dir($dirname))
		  $dir_handle = opendir($dirname);
	   if (!$dir_handle)
		  return false;
	   while($file = readdir($dir_handle)) {
		  if ($file != "." && $file != "..") {
			 if (!is_dir($dirname."/".$file))
				unlink($dirname."/".$file);
			 else
				delete_directory($dirname.'/'.$file);     
		  }
	   }
	   closedir($dir_handle);
	   rmdir($dirname);
	   return true;
	}
 
	
	delete_directory(__SITE_ROOT__ . '/install');
	//unlink(__APPLICATION_ROOT__ . "/database/database.sql"); # No need to remove this file. should be contained in /install
?>
<?php /* URL::redirect(__ROOT_URL__); */ ?>
<?php } ?>
</body>
</html>
