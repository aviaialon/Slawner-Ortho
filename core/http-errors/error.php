
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php
			SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::HYBERNATE::OBJECTS::PAGES::PAGE_META");
			PAGE_META::getPageMeta();
		?>
		<?php if (isset($noCache)) { ?>
			<meta http-equiv="expires" value="Thu, 16 Mar 2000 11:00:00 GMT" />
			<meta http-equiv="pragma" content="no-cache" />
			<meta http-equiv="cache-control" content="no-cache">
		<?php } ?>
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Permanent+Marker" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<style type="text/css">
			body {background:#ECF9FF;}
			p {font: 34px 'Permanent Marker';}
			table#main {background:#FFF;}
			h1{font: 36px 'Permanent Marker';}
			p {
				margin:5px;
				padding:5px;
			}
		</style>
	</head>
	<body style="margin-top:25px;"><br /><br /><br />
		<table border="0" cellspacing="0" cellpadding="0" style="margin:0 auto;width:1050px;background:none;">
			<tr>
				<td colspan="2">
					<a href="<?php echo(__ROOT_URL__); ?>"><img src="/pre-launch.v2/images/bg-logo.png" style="border:0px" /></a>
				</td>
			</tr>
		</table><br /><br />
		<table id="main" border="0" cellspacing="0" cellpadding="0" style="margin:0 auto;width:1050px;background:#FFF;border:solid 1px #84D7FF">
			<tr>
				<td valign="middle" style="vertical-align:middle;padding-left:90px;">
				<h1 style="color:#0080C0;font-weight:bold">OOPS... We're Sorry.</h1>
				<p style="color:#666;font-size:16px;">
					There seems to be an error in our systems. Please be patient as our team of monkey developers will resolve this ASAP. If this problem persists, 
					<br />Please do <b>NOT</b> hesitate to <a href="mailto:<?php echo(__ADMIN_EMAIL__); ?>">contact us at <?php echo(__ADMIN_EMAIL__); ?></a>
					<br /><br />
					<b>We Appreciate Your Patience.</b><br />
					The <a href="<?php echo(__ROOT_URL__); ?>"><?php echo(ucwords(__SITE_NAME__)); ?></a> Team.
				</p>
				</td>
				<td valign="middle"><img src="<?php echo(__ROOT_URL__); ?>/index_files/404.jpg" /></td>
			</tr>
		</table>
	</body>
</html>