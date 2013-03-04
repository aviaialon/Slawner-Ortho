<?php 
/**
 * Menu Partial Class
 */	
class PARTIAL_MAP_DIRECTIONS extends PARTIAL_BASE
{
	public function execute(array $arrparameters)
	{
		$this->Application = APPLICATION::getInstance();
		$this->strStaticResourcePath = $this->Application->getBaseStaticResourcePath();
	}
	
	public function render()
	{
?>
<section id="map-wrapper">
	<iframe id="locationsMap" 
			style="border-bottom:1px solid #DDDDDD" 
			width="100%" 
			height="350" 
			allowtransparency="1" 
			frameborder="0" 
			marginheight="0" 
			marginwidth="0" 
			scrolling="0" src="/map/large/" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	<div class="row map_overlay">
		<div class="twelve columns">
			<h3 class="locationTitle"><?php echo($this->Application->translate('Head Office', 'Siège Social')); ?></h3>
			<p class="locationAddress">5713, ch. de la Côte-des-Neiges Montréal, QC, H3S 1Y7</p>
			<p>
			<div class="mapSearchContainer">
				<span class="txt-info">&darr; <?php echo($this->Application->translate(
					'Tell us where are you coming from to get directions', 'Dites-nous où venez-vous pour obtenir les directions')); ?></span>
				<input x-webkit-speech speech id="mapSearchTextField" type="text" size="50" placeholder="<?php echo($this->Application->translate('Enter Your Location', 'Entrez votre Adresse')); ?>" />
				<div id="delete"><span id="x">x</span><span id="travelMode" class="tooltip" 
					title="<?php echo($this->Application->translate('Select your travel mode', 'Choisissez votre mode de transport')); ?>"></span></div>
				<a class="g-button no-text tooltip" id="useLoc" title="<?php echo($this->Application->translate('Use My Current Location', 'Utiliser ma position actuelle')); ?>"><i class="icon-refresh"></i></a>
				<a class="button small blue" id="direction_submit" title="<?php echo($this->Application->translate('Search', 'Rechercher')); ?>">
				<?php echo($this->Application->translate('Search', 'Rechercher')); ?></a>
				<div id="travelModeSelection">
					<p><?php echo($this->Application->translate('Select your travel mode', 'Choisissez votre mode de transport')); ?>:</p>
					<ul>
						<li class="selected" rel-travel-mode="driving"><?php echo($this->Application->translate('Driving', 'Par voiture')); ?></li>
						<li rel-travel-mode="transit"><?php echo($this->Application->translate('By Bus', 'Par autobus')); ?></li>
						<li rel-travel-mode="walking"><?php echo($this->Application->translate('Walking', 'En marchant')); ?></li>
					</ul>
				</div>
			</div>
			</p>
		</div>
	</div>		
	<div class="row">
		<div class="twelve columns">
			<div class="systabspane lefttabs maptabs">
				<ul class="tabs up">
					<li class="current"><a rel-loc-index="0" href="#">Côte-des-Neiges <mark><?php echo($this->Application->translate('Head Office', 'Siège Social')); ?></mark> </a></li>
					<li><a rel-loc-index="1" href="#">Hôpital Santa Cabrini <!--<mark>Département d'Orthopédie</mark>--></a></li>
					<li><a rel-loc-index="2" href="#">Anna-Laberge <!--<mark>Centre Hospitalier</mark>--></a></li>
					<li><a rel-loc-index="3" href="#">St-Catherine</a></li>
					<li><a rel-loc-index="4" href="#">CRMSSO <!--<mark>Châteauguay</mark>--></a></li>
					<li><a rel-loc-index="5" href="#">Lachine</a></li>
				</ul>
			</div>
		</div>
	</div>	
	<div class="shadow"></div>
</section>

<section id="map-direction-wrapper">
	<div class="row">
		<div class="eight columns">
			<h3><?php echo($this->Application->translate('Directions to', 'Directions a')); ?>:  
			<strong class="locationTitle"><?php echo($this->Application->translate('Head Office', 'Siège Social')); ?></strong></h3>
		</div>
		<div class="four columns">
			<div id="more_info" class="float-right">
				<p><?php echo($this->Application->translate('Total Distance', 'Distance Totale')); ?>: <span id="total"></span></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="twelve columns">
			<div class="eight columns">
				<div id="directionsPanel" class="container">
					<div id="directionsPanelContainer"></div>
				</div>
			</div>
			<div class="four columns">
				<div id="map_canvas"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="twelve columns">
			<hr />
			<div class="float-right">
				<a href="#" id="mapDirectionPrint" class="button blue small"><?php echo($this->Application->translate('Print', 'Imprimer')); ?></a>
				<a href="#" id="mapDirectionDone" class="button grey small"><?php echo($this->Application->translate('Done', 'Terminer')); ?></a>
			</div>
		</div>
	</div>
</section>

<?php		
		
	}
}