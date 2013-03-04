<?php 
	$Application = $this->getApplication(); 
	$strStaticResourcePath = $Application->getBaseStaticResourcePath();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="<?php echo($strStaticResourcePath); ?>/css/style.css"/>
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family='long'o:100,300,400|Nobile:400,700italic,700,400italic|Maven+Pro:400,500,700|Oswald:400,700,300"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCyG4O4t5fKaI_UdFazJos3w2tKEUK7x3Q&sensor=false"></script>
<script src="<?php echo($strStaticResourcePath); ?>js/jquery/jquery.mapstyle.js"></script>
<script type="text/javascript">
	$(document).ready(function(e) {
		function loadLargeMap()
		{
			$(".map_large").html('').css({
				width: $(window).width(),
				height: $(window).height()
			});
			var slawnerThumbs = [
				"/static/images/map/thumbs/slawner-1.jpg",
				"/static/images/map/thumbs/slawner-2.jpg",
				"/static/images/map/thumbs/slawner-3.jpg"
			];
			var cabriniThumbs = [
				"/static/images/map/thumbs/cabrini-1.jpg",
				"/static/images/map/thumbs/cabrini-2.jpg",
				"/static/images/map/thumbs/cabrini-3.jpg"
			];
			var annaThumbs = [
				"/static/images/map/thumbs/anna-1.jpg",
				"/static/images/map/thumbs/anna-2.jpg",
				"/static/images/map/thumbs/anna-3.jpg"
			];
			$.fn.StylizedMap.options.arrowUrl = "/static/images/map/arrow.png";
			var objMapRenderer = $(".map_large").StylizedMap({
				'ribbon': "#ribbon",
				'zoomLevel': 10,
				'styles': $().StylizedMap.styles.rainbow,
				'center': {
					'lat': 45.49907,
					'long': -73.62784,
				},
				'locations': [{ 
					'lat': 45.49907,
					'long': -73.62784,
					'title': 'Siège Social, Montréal ',
					'sub_title': "13, ch. de la Côte-des-Neiges Montréal,<br /> QC, H3S 1Y7",
					'slides': slawnerThumbs,
					'active': <?php echo($this->getRequestParam('location') == 1 ? 'true': 'false'); ?>
				}, {
					'lat': 45.57449,
					'long': -73.57418,
					'title': 'Hôpital Santa Cabrini - Département d\'Orthopédie',
					'sub_title': "5655, St-Zotique Est, Montréal, QC, H1T 1P7",
					'slides': cabriniThumbs,
					'active': <?php echo($this->getRequestParam('location') == 2 ? 'true': 'false'); ?>
				}, {
					'lat': 45.34631,
					'long': -73.76585,
					'title': 'Centre Hospitalier Anna-Laberge',
					'sub_title': "200, rue Brisebois, Châteauguay, QC, J6K 4W8",
					'slides': annaThumbs,
					'active': <?php echo($this->getRequestParam('location') == 3 ? 'true': 'false'); ?>
				}, {
					'lat': 45.39732,
					'long': -73.56860,
					'title': 'St-Catherine',
					'sub_title': "5320 Boul. St Laurent local 160 Saint-Catherine, QC, J5C 1A7",
					'slides': ["/static/images/map/thumbs/st-cat-1.jpg"],
					'active': <?php echo($this->getRequestParam('location') == 4 ? 'true': 'false'); ?>
				}, {
					'lat': 45.35207,
					'long': -73.71995,
					'title': 'CRMSSO (Châteauguay)',
					'sub_title': "185, St-Jean-Baptiste, local 300 Châteauguay, QC, J6K 3B4",
					'slides': ["/static/images/map/thumbs/crmsso-1.jpg"],
					'active': <?php echo($this->getRequestParam('location') == 5 ? 'true': 'false'); ?>
				}, {
					'lat': 45.43435,
					'long': -73.69374,
					'title': 'Clinique Lachine',
					'sub_title': "3360, rue Notre-Dame, local 15 Lachine, QC, H8T 3E2",
					'slides': ["/static/images/map/thumbs/lachine-1.jpg"],
					'active': <?php echo($this->getRequestParam('location') == 6 ? 'true': 'false'); ?>
				}]
			});	
		}	
		loadLargeMap();
		
		$(window).resize(function (event) {
			//google.maps.event.trigger(sliderMap, "resize");	
			loadLargeMap();
		});
	});
</script>
<style type="text/css">
	body {margin: 0px; padding: 0px;}
</style>
</head>

<body>
<div id="map" class="stylized_map map_large"></div>
</body>
</html>
