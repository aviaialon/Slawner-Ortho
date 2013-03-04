<?php 
/**
 * Call module Partial Class
 */	
class PARTIAL_CALL_MODULE extends PARTIAL_BASE
{	
	public function __construct() {}
	
	public function execute(array $arrparameters)
	{
		$this->Application = APPLICATION::getInstance();
		$this->strApplicationStaticResPath = $this->Application->getBaseStaticResourcePath();
	}
	
	public function render()
	{
?>
<div class="call-module">
	<div class="head">
		<div class="drg-handle"></div>
		<a class="button small grey float-right tooltip" title="<?php echo($this->Application->translate('Click here to close', 'Cliquez ici pour fermer')); ?>" href="#">X</a>
		<h2><?php echo($this->Application->translate('We would love to hear from you.', 'Des questions? Contactez nous!')); ?></h2>
		
		 <div class="row contact-wrap-info">
			<div class="six columns mobile-two">            
				<img src="<?php echo($this->strApplicationStaticResPath); ?>/images/icons/icon108.png" alt="" class="img-left">
				<h5><?php echo($this->Application->translate('Headquarters.', 'Siège social.')); ?></h5>
				<p>5713, ch. de la Côte-des-Neiges Montréal, QC, <br>H3S 1Y7</p>
			</div>
			<div class="six columns mobile-two">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>/images/icons/icon231.png" alt="" class="img-left">
				<h5><?php echo($this->Application->translate('Contact Info.', 'Info contact.')); ?></h5>
				<p>Tel: <?php echo(constant('__CONTACT_PHONE__')); ?><br>E: <?php echo(constant('__INFO_EMAIL__')); ?> </p>
			</div>
		</div>

	</div>
	<div class="content">
		<div class="section-right">
			<a class="button medium dark_blue float-right tooltip" id="module-call-btn-trigger" 
			title="<?php echo($this->Application->translate('Click here to call', 'Cliquez ici pour nous appeler')); ?>" href="#">
				<img src="<?php echo($this->strApplicationStaticResPath); ?>/images/headphone_music.png" width="30" align="absmiddle" /> 
				<?php echo($this->Application->translate('CLICK TO CALL', 'NOUS APPELER')); ?></a>
				
			<a class="button medium red float-right tooltip" id="module-call-btn-hangup" 
				title="<?php echo($this->Application->translate('Hangup Call', 'Raccrocher l\'appel')); ?>" href="#">
					<img src="<?php echo($this->strApplicationStaticResPath); ?>/images/headphone_music.png" width="30" align="absmiddle" /> 
					<?php echo($this->Application->translate('HANGUP CALL', 'RACCROCHER L\'APPEL')); ?></a>	
			<div class="headset">
				<form action="">
					<input type="checkbox" name="headset" id="headset" checked/> 
					<label for="headset"><?php echo($this->Application->translate('Are you using a headset?', 'Utilisez vous un casque d\'ecoute?')); ?></label>
				</form>
			</div>
			<span class="vlm">Volume:</span>
			<volume_section>	
				<span class="volume_tooltip"></span> <!-- Tooltip -->
				<div id="volume_slider"></div> <!-- the Slider -->
				<span class="volume"></span> <!-- Volume -->
			</volume_section>
			<div id="status"></div>
		</div>
		<div id="keypad">
			<table class="J-M" cellspacing="0px" cellpadding="0px">
				<tbody>
					<tr>
						<td class="J-N J-N-JT" data-value="1" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">1</div>
								<div class="Mr8HO">&nbsp;</div>
							</div></td>
						<td class="J-N" data-value="2" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">2</div>
								<div class="Mr8HO">ABC</div>
							</div></td>
						<td class="J-N" data-value="3" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">3</div>
								<div class="Mr8HO">DEF</div>
							</div></td>
					</tr>
					<tr>
						<td class="J-N" data-value="4" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">4</div>
								<div class="Mr8HO">GHI</div>
							</div></td>
						<td class="J-N" data-value="5" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">5</div>
								<div class="Mr8HO">JKL</div>
							</div></td>
						<td class="J-N" data-value="6" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">6</div>
								<div class="Mr8HO">MNO</div>
							</div></td>
					</tr>
					<tr>
						<td class="J-N" data-value="7" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">7</div>
								<div class="Mr8HO">PQRS</div>
							</div></td>
						<td class="J-N" data-value="8" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">8</div>
								<div class="Mr8HO">TUV</div>
							</div></td>
						<td class="J-N" data-value="9" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">9</div>
								<div class="Mr8HO">WXYZ</div>
							</div></td>
					</tr>
					<tr>
						<td class="J-N" data-value="*" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd star">*</div>
							</div></td>
						<td class="J-N" data-value="0" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">0</div>
								<div class="Mr8HO">+</div>
							</div></td>
						<td class="J-N" data-value="#" role="menuitem"><div class="J-N-Jz">
								<div class="UdSvGd">#</div>
								<div class="Mr8HO">&nbsp;</div>
							</div></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php	
	}
}