<style>
	#content{
		position: relative;
		top:10px;
		left:10px;
	}
	.bcholder{
		width: <?php echo $styles['container']['width'] . "px" ?>;
		height: <?php echo $styles['container']['height'] . "px" ?>;
		float:left;
		position:relative;
		top: <?php echo $styles['container']['top'] . "px" ?>;
		left: <?php echo $styles['container']['left'] . "px" ?>;
		border: 1px solid;
	}
	.titleHeader{
		font-size: <?php echo $styles['title']['fontSize'] . "px" ?>;

		letter-spacing: <?php echo $styles['title']['letterSpacing'] . "px" ?>;
		font-family: Optima, Segoe, "Segoe UI", Candara, Calibri, Arial, sans-serif;
		font-weight: bold;
		position:absolute;
		top: <?php echo $styles['title']['top'] . "px" ?>;
		left:<?php echo $styles['title']['left'] . "px" ?>;

	}
	.bcbar{
		position:absolute;
		top:<?php echo $styles['bar']['top'] . "px" ?>;
		left:<?php echo $styles['bar']['left'] . "px" ?>;
		overflow: hidden;
	<?php
		if($family == 'CEBUATLANTIC'){
		?>
		-ms-transform: scale(0.8, 0.8); /* IE 9 */
		-webkit-transform: scale(0.8, 0.8); /* Chrome, Safari, Opera */
		transform: scale(0.8, 0.8); /* Standard syntax */
	<?php
	}else if($family == 'ULTRASTEEL') {
			?>
		-ms-transform: scale(1.2, 1.2); /* IE 9 */
		-webkit-transform: scale(1.2, 1.2); /* Chrome, Safari, Opera */
		transform: scale(1.2, 1.2); /* Standard syntax */
	<?php
		} else {
	?>
		-ms-transform: scale(1.1, 1.1); /* IE 9 */
		-webkit-transform: scale(1.1, 1.1); /* Chrome, Safari, Opera */
		transform: scale(1.1, 1.1); /* Standard syntax */
	<?php
		}
	?>

	}
	.bclabel{
		position:absolute;
		letter-spacing: <?php echo $styles['barLabel']['letterSpacing'] . "px" ?>;
		font-size: <?php echo $styles['barLabel']['fontSize'] . "px" ?>;
		top:<?php echo $styles['barLabel']['top'] . "px" ?>;
		left:<?php echo $styles['barLabel']['left'] . "px" ?>;
		background-color:<?php echo $styles['barLabel']['backgroundColor'] . ' !important'; ?>;
		display: <?php echo  ($styles['barLabel']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['barLabel']['fontWeight']) ? 'bold' : 'normal' ?>;
	}
	.extradesc{
		position:absolute;
		font-size: <?php echo $styles['extraDesc']['fontSize'] . "px" ?>;
		top:<?php echo $styles['extraDesc']['top'] . "px" ?>;
		left:<?php echo $styles['extraDesc']['left'] . "px" ?>;
		display: <?php echo  ($styles['extraDesc']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['extraDesc']['fontWeight']) ? 'bold' : 'normal' ?>;

	}
	.itemcode{
		position:absolute;
		font-size: <?php echo $styles['itemcode']['fontSize'] . "px" ?>;
		top:<?php echo $styles['itemcode']['top'] . "px" ?>;
		left:<?php echo $styles['itemcode']['left'] . "px" ?>;
		display: <?php echo  ($styles['itemcode']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['itemcode']['fontWeight']) ? 'bold' : 'normal' ?>;

	}
	.category{
		position:absolute;
		font-size: <?php echo $styles['category']['fontSize'] . "px" ?>;
		top:<?php echo $styles['category']['top'] . "px" ?>;
		left:<?php echo $styles['category']['left'] . "px" ?>;
		display: <?php echo  ($styles['category']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['category']['fontWeight']) ? 'bold' : 'normal' ?>;
		padding:0;
		margin:0;

	}
	.price {
		position:absolute;
		padding:0;
		margin:0;
		font-size: <?php echo $styles['price']['fontSize'] . "px" ?>;
		top:<?php echo $styles['price']['top'] . "px" ?>;
		left:<?php echo $styles['price']['left'] . "px" ?>;
		display: <?php echo  ($styles['price']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['price']['fontWeight']) ? 'bold' : 'normal' ?>;
	}
	.supcateg {
		position:absolute;
		padding:0;
		margin:0;
		font-size: <?php echo $styles['supcateg']['fontSize'] . "px" ?>;
		top:<?php echo $styles['supcateg']['top'] . "px" ?>;
		left:<?php echo $styles['supcateg']['left'] . "px" ?>;
		display: <?php echo  ($styles['supcateg']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['supcateg']['fontWeight']) ? 'bold' : 'normal' ?>;
	}
	.storecode {
		position:absolute;
		padding:0;
		margin:0;
		font-size: <?php echo $styles['storecode']['fontSize'] . "px" ?>;
		top:<?php echo $styles['storecode']['top'] . "px" ?>;
		left:<?php echo $styles['storecode']['left'] . "px" ?>;
		display: <?php echo  ($styles['storecode']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['storecode']['fontWeight']) ? 'bold' : 'normal' ?>;
		-ms-transform: <?php echo  ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>; /* IE 9 */
		-webkit-transform:<?php echo  ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>; /* Chrome, Safari, Opera */
		-moz-transform:<?php echo  ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>;
		transform: <?php echo   ($styles['storecode']['rotate']) ? 'rotate('.$styles['storecode']['rotate'].'deg)' : 'rotate(0deg)'; ?>;
	}
	.date {
		position:absolute;
		padding:0;
		margin:0;
		font-size: <?php echo $styles['date']['fontSize'] . "px" ?>;
		top:<?php echo $styles['date']['top'] . "px" ?>;
		left:<?php echo $styles['date']['left'] . "px" ?>;
		display: <?php echo  ($styles['date']['display']) ? 'block' : 'none' ?>;
		font-weight: <?php echo  ($styles['date']['fontWeight']) ? 'bold' : 'normal' ?>;
	}
	.draggable {
		background-color:#999;
		cursor:move;
	}

</style>
<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<?php include 'includes/product_nav.php'; ?>
		<div class='hidden-xs'>
		<div id="content" >
			<div id='bcholder' data-targetid='bcholderValue' class="bcholder canbedrag">
				<div data-targetid='titleValue' class='titleHeader canbedrag'><?php echo strtoupper($thiscompany->name); ?></div>
				<div data-targetid='bcbarValue' class='bcbar canbedrag'></div>
				<div  data-targetid='bclabelValue' class="bclabel canbedrag"></div>
				<div data-targetid='extradescValue' class="extradesc canbedrag"><?php  echo $styles['extraDesc']['value']; ?></div>
				<div data-targetid='itemcodeValue' class="itemcode canbedrag">Test Item Code</div>
				<div data-targetid='categoryValue' class="category canbedrag">Category</div>
				<div data-targetid='priceValue' class="price canbedrag">P. 0.00</div>
				<div data-targetid='storecodeValue' class="storecode canbedrag"><?php  echo $styles['storecode']['value']; ?></div>
				<div data-targetid='dateValue' class="date canbedrag">0515</div>
				<div data-targetid='supcategValue' class="supcateg canbedrag"></div>
			</div>
			<div id="itemholder"></div>
		</div>
		<div id="editor" style='padding:10px;'>
			<div style='clear:both;'></div>
			<hr />
			<div class="row">
				<div class="col-md-3">
					<form action="" method='POST'>
						<select class="form-control" name="fid" id="fid">
							<?php
								foreach($barcode_format as $bf){
									echo "<option value='$bf->family'>$bf->family</option>";
								}
							?>
						</select>
					</form>
				</div>
				<div class="col-md-3">
					<input type="text" class='form-control' id='howMany' placeholder='How many?' value='<?php echo $styles['settings']['howmany'] ?>'/>
				</div>
				<div class="col-md-3">
					<select name="type" id="type" class='form-control'>
						<?php
							$arrtype = array ('codabar','code11','code39','code93','code128','ean8','ean13','std25','int25','msi');
						?>
						<option value="">--select type--</option>
						<?php
							foreach($arrtype as $a){
								if($styles['settings']['type'] == $a){
									$tselect ='selected';
								} else {
									$tselect='';
								}
								echo "<option value='$a' $tselect>$a</option>";
							}
						?>
					</select>
				</div>
			</div>
			<hr />

			<input type="button" class='btn btn-default' value='Save' id='save' />
			<hr />


			<table class="table">
				<thead>
				<tr><th>Parts</th><th>Top</th><th>Left</th><th>Size</th><th>Visibility</th><th>Bold</th><th>Value</th><th>Height</th><th>Width</th><th>Spacing</th><Th>BG</Th><th>Rotate</th></tr>
				</thead>
				<tbody>

				<tr >
					<td>Title Holder</td>
					<td><input  class='form-control' type='text' id='titleTop' value='<?php echo $styles['title']['top'] ?>' /></td>
					<td><input class='form-control' type='text' id='titleLeft' value='<?php echo $styles['title']['left'] ?>'/></td>
					<td><input  class='form-control' type='text' id='titleSize' value='<?php echo $styles['title']['fontSize'] ?>'/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input class='form-control' type='text' id='titleValue' value="<?php echo $styles['title']['value'];  ?>"/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input  class='form-control' type='text' id='titleSpacing' value='<?php echo $styles['title']['letterSpacing'] ?>'/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
				</tr>
				<tr >
					<td>Bar</td>
					<td><input  class='styleBar form-control' type='text' id='barTop' value='<?php echo $styles['bar']['top'] ?>' /></td>
					<td><input  class='styleBar form-control' type='text' id='barLeft' value='<?php echo $styles['bar']['left'] ?>'/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input  class='styleBar form-control' type='text' id='barHeight' value='<?php echo $styles['bar']['height'] ?>'/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
					<td><input type="text" class='form-control' disabled/></td>
				</tr>
				<tr>
					<td>Bar Label</td><!-- Title -->
					<td><input  class='styleBarLabel form-control' type='text' id='barLabelTop' value='<?php echo $styles['barLabel']['top'] ?>' /></td><!-- Top -->
					<td><input class='styleBarLabel form-control'  type='text' id='barLabelLeft' value='<?php echo $styles['barLabel']['left'] ?>' /></td><!-- Left -->
					<td><input  class='styleBarLabel form-control' type='text' id='barLabelSize' value='<?php echo $styles['barLabel']['fontSize'] ?>'/></td><!-- Size -->
					<td><input type="checkbox" id='barLabelVisible' <?php echo  ($styles['barLabel']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td><input type="checkbox" id='barLabelBold' <?php echo  ($styles['barLabel']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input type="text" class='form-control' disabled/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input  class='form-control' type='text' id='barLabelSpacing' value='<?php echo $styles['barLabel']['letterSpacing'] ?>'/></td><!-- Spacing -->
					<td><input  class='styleBarLabel form-control' type='text' id='barLabelBackgroundColor' value='<?php echo $styles['barLabel']['backgroundColor'] ?>'/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				<tr>
					<td>Extra Desc</td><!-- Title -->
					<td><input  class='styleExtraDisc form-control' type='text' id='extraDescTop' value='<?php echo $styles['extraDesc']['top'] ?>' /></td><!-- Top -->
					<td><input  class='styleExtraDisc form-control' type='text' id='extraDescLeft' value='<?php echo $styles['extraDesc']['left'] ?>'/></td><!-- Left -->
					<td><input  class='styleExtraDisc form-control' type='text' id='extraDescSize' value='<?php echo $styles['extraDesc']['fontSize'] ?>'/></td><!-- Size -->
					<td> <input type="checkbox" id='extraDescVisible' <?php echo  ($styles['extraDesc']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td><input type="checkbox" id='extraDescBold' <?php echo  ($styles['extraDesc']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input class='form-control' type='text' id='extraDescValue' value='<?php echo $styles['extraDesc']['value'] ?>'/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				<tr>
					<td>Item Code</td><!-- Title -->
					<td><input  class='styleItemCode form-control' type='text' id='itemCodeTop' value='<?php echo $styles['itemcode']['top'] ?>' /></td><!-- Top -->
					<td><input  class='styleItemCode form-control' type='text' id='itemCodeLeft' value='<?php echo $styles['itemcode']['left'] ?>'/></td><!-- Left -->
					<td><input  class='styleItemCode form-control' type='text' id='itemCodeSize' value='<?php echo $styles['itemcode']['fontSize'] ?>'/></td><!-- Size -->
					<td><input type="checkbox" id='itemCodeVisible' <?php echo  ($styles['itemcode']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td><input type="checkbox" id='itemCodeBold' <?php echo  ($styles['itemcode']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input type="text" class='form-control' disabled/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				<tr>
					<td>Category</td><!-- Title -->
					<td><input  class='styleCategory form-control' type='text' id='categoryTop' value='<?php echo $styles['category']['top'] ?>' /></td><!-- Top -->
					<td><input  class='styleCategory form-control' type='text' id='categoryLeft' value='<?php echo $styles['category']['left'] ?>'/></td><!-- Left -->
					<td> <input  class='styleCategory form-control' type='text' id='categorySize' value='<?php echo $styles['category']['fontSize'] ?>'/></td><!-- Size -->
					<td><input type="checkbox" id='categoryVisible' <?php echo  ($styles['category']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td> <input type="checkbox" id='categoryBold' <?php echo  ($styles['category']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input type="text" class='form-control' disabled/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				<tr>
					<td>Date</td><!-- Title -->
					<td><input  class='styleDate form-control' type='text' id='dateTop' value='<?php echo $styles['date']['top'] ?>' /></td><!-- Top -->
					<td><input  class='styleDate form-control' type='text' id='dateLeft' value='<?php echo $styles['date']['left'] ?>'/></td><!-- Left -->
					<td> <input  class='styleDate form-control' type='text' id='dateSize' value='<?php echo $styles['date']['fontSize'] ?>'/></td><!-- Size -->
					<td><input type="checkbox" id='dateVisible' <?php echo  ($styles['date']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td><input type="checkbox" id='dateBold' <?php echo  ($styles['date']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input type="text" class='form-control' disabled/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				<tr>
					<td>Store Code</td><!-- Title -->
					<td><input  class='styleStorecode form-control' type='text' id='storecodeTop' value='<?php echo $styles['storecode']['top'] ?>' /></td><!-- Top -->
					<td><input  class='styleStorecode form-control' type='text' id='storecodeLeft' value='<?php echo $styles['storecode']['left'] ?>'/></td><!-- Left -->
					<td><input  class='styleStorecode form-control' type='text' id='storecodeSize' value='<?php echo $styles['storecode']['fontSize'] ?>'/></td><!-- Size -->
					<td><input type="checkbox" id='storecodeVisible' <?php echo  ($styles['storecode']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td><input type="checkbox" id='storecodeBold' <?php echo  ($styles['storecode']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input class='form-control' type='text' id='storecodeValue' value='<?php echo $styles['storecode']['value'] ?>'/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input  class='styleStorecode form-control' type='text' id='storecodeRotate' value='<?php echo $styles['storecode']['rotate'] ?>'/></td><!-- Rotate -->
				</tr>
				<tr>
					<td>Price</td><!-- Title -->
					<td><input  class='stylePrice form-control' type='text' id='priceTop' value='<?php echo $styles['price']['top'] ?>' /></td><!-- Top -->
					<td> <input  class='stylePrice form-control' type='text' id='priceLeft' value='<?php echo $styles['price']['left'] ?>'/></td><!-- Left -->
					<td><input  class='stylePrice form-control' type='text' id='priceSize' value='<?php echo $styles['price']['fontSize'] ?>'/></td><!-- Size -->
					<td><input type="checkbox" id='priceVisible' <?php echo  ($styles['price']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td><input type="checkbox" id='priceBold' <?php echo  ($styles['price']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input type="text" class='form-control' disabled/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				<tr style='display:none;'>
					<td>Supplier Categ</td><!-- Title -->
					<td><input  class='styleSupcateg form-control' type='text' id='supcategTop' value='<?php echo $styles['supcateg']['top'] ?>' /></td><!-- Top -->
					<td><input  class='styleSupcateg form-control' type='text' id='supcategLeft' value='<?php echo $styles['supcateg']['left'] ?>'/></td><!-- Left -->
					<td><input  class='styleSupcateg form-control' type='text' id='supcategSize' value='<?php echo $styles['supcateg']['fontSize'] ?>'/></td><!-- Size -->
					<td><input type="checkbox" id='supcategVisible' <?php echo  ($styles['supcateg']['display']) ? 'checked' : '' ?> /></td><!-- visible -->
					<td><input type="checkbox" id='supcategBold' <?php echo  ($styles['supcateg']['fontWeight']) ? 'checked' : '' ?>/></td><!-- Bold -->
					<td><input type="text" class='form-control' disabled/></td><!-- Value -->
					<td><input type="text" class='form-control' disabled/></td><!-- Height -->
					<td><input type="text" class='form-control' disabled/></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				<tr>
					<td>Container</td><!-- Title -->
					<td><input  class='styleContainer form-control' type='text' id='conTop' value='<?php echo $styles['container']['top'] ?>' /></td><!-- Top -->
					<td><input   class='styleContainer form-control' type='text' id='conLeft' value='<?php echo $styles['container']['left'] ?>'/></td><!-- Left -->
					<td><input type="text" class='form-control' disabled/></td><!-- Size -->
					<td><input type="text" class='form-control' disabled/></td><!-- visible -->
					<td><input type="text" class='form-control' disabled/></td><!-- Bold -->
					<td><input type="text" class='form-control' disabled/></td><!-- Value -->
					<td> <input  class='styleContainer form-control' type='text' id='conHeight'  value='<?php echo $styles['container']['height'] ?>' /></td><!-- Height -->
					<td><input  class='styleContainer form-control'  type='text' id='conWidth' value='<?php echo $styles['container']['width'] ?>' /></td><!-- Width -->
					<td><input type="text" class='form-control' disabled/></td><!-- Spacing -->
					<td><input type="text" class='form-control' disabled/></td><!-- BG -->
					<td><input type="text" class='form-control' disabled/></td><!-- Rotate -->
				</tr>
				</tbody>
			</table>

			<!-- TEST-->


		</div>
	</div>
		<div class='visible-xs'><div class='alert alert-warning'>This module is not available in small screen. Please use desktop or laptop computer.</div></div>
	</div>
</div> <!-- end page content wrapper-->

<script type="text/javascript" src="../js/jquery.js"></script>
<!-- <script type="text/javascript" src="../js/gridster.js"></script> -->
<script type="text/javascript" src="../js/jquery-barcode.js"></script>
<script>
	$(function(){


		$('.loading').hide();
		$('#allcontent').fadeIn();


		$(".bcbar").barcode('GQ000001', '<?php echo ($styles['settings']['type']) ? $styles['settings']['type'] : 'ean13' ?>', {
			barWidth: 1, barHeight: <?php echo ($styles['bar']['height']) ? $styles['bar']['height'] : 25; ?>, showHRI: false, moduleSize: 5, output:'css'
		});
		$(".bclabel").html('GQ000001');

		$('body').on('mousedown', '.canbedrag', function() {
			$(this).addClass('draggable').parents().on('mousemove', function(e) {
				$('.draggable').offset({
					top: e.pageY - $('.draggable').outerHeight() / 2,
					left: e.pageX - $('.draggable').outerWidth() / 2
				}).on('mouseup', function() {
					$(this).removeClass('draggable');
					var myclass = $(this).attr('class').split(' ')[0];
					var targetid = $(this).attr('data-targetid');
					var dposition = $(this).position();

					$("."+myclass).css({top: dposition.top, left: dposition.left});
					if(targetid == 'titleValue'){
						updateTitle(dposition.top,dposition.left);
					} else if(targetid == 'bcbarValue'){
						updateBar(dposition.top,dposition.left);
					} else if(targetid == 'bclabelValue'){
						updateBarLabel(dposition.top,dposition.left);
					}else if(targetid == 'extradescValue'){
						updateExtraDesc(dposition.top,dposition.left);
					}else if(targetid == 'itemcodeValue'){
						updateItemCode(dposition.top,dposition.left);
					}else if(targetid == 'categoryValue'){
						updateCategory(dposition.top,dposition.left);
					}else if(targetid == 'priceValue'){
						updatePrice(dposition.top,dposition.left);
					}else if(targetid == 'supcategValue'){
						updateSupcateg(dposition.top,dposition.left);
					}
					else if(targetid == 'storecodeValue'){
						updateStorecode(dposition.top,dposition.left);
					}else if(targetid == 'dateValue'){
						updateDate(dposition.top,dposition.left);
					}else if(targetid == 'bcholderValue'){
						updateContainer(dposition.top,dposition.left);
					}
				});

			});
			e.preventDefault();
		}).on('mouseup', function() {
			$('.draggable').removeClass('draggable');
		});
		$('#howMany').keyup(function(){
			var n = $(this).val();
			if(isNaN(n)){
				n=1;
				alert('Not a number');
				$(this).val(1);
			}
			howMany(n);
		});
		howMany($('#howMany').val());
		function howMany(n){
			$('#itemholder').html('');
			for(var i=1;i<n;i++){
				$('#itemholder').append($('#bcholder').clone());
			}
		}
		function updateTitle(t,l){
			$('#titleTop').val(t);
			$('#titleLeft').val(l);
		}
		function updateBar(t,l){
			$('#barTop').val(t);
			$('#barLeft').val(l);
		}
		function updateBarLabel(t,l){
			$('#barLabelTop').val(t);
			$('#barLabelLeft').val(l);
		}
		function updateExtraDesc(t,l){
			$('#extraDescTop').val(t);
			$('#extraDescLeft').val(l);
		}
		function updateItemCode(t,l){
			$('#itemCodeTop').val(t);
			$('#itemCodeLeft').val(l);
		}
		function updateCategory(t,l){
			$('#categoryTop').val(t);
			$('#categoryLeft').val(l);
		}
		function updatePrice(t,l){
			$('#priceTop').val(t);
			$('#priceLeft').val(l);
		}
		function updateSupcateg(t,l){
			$('#supcategTop').val(t);
			$('#supcategLeft').val(l);
		}
		function updateStorecode(t,l){
			$('#storecodeTop').val(t);
			$('#storecodeLeft').val(l);
		}
		function updateDate(t,l){
			$('#dateTop').val(t);
			$('#dateLeft').val(l);
		}
		function updateContainer(t,l){
			$('#conTop').val(t);
			$('#conLeft').val(l);
		}

		$('#titleTop,#titleLeft,#titleValue,#titleSize').keyup(function(){
			var t = $('#titleTop').val();
			var l = $('#titleLeft').val();
			var tval = $('#titleValue').val();
			var s =  $('#titleSize').val();
			var spacing = $('#titleSpacing').val();
			$('.titleHeader').html(tval);
			changePosition(t,l,'titleHeader');
			changeSize(s,'titleHeader');
			changeSpacing(spacing,'titleHeader')
		});
		$('#titleSpacing').keyup(function(){
			changeSpacing($(this).val(),'titleHeader')
		});
		$('#barTop,#barLeft').keyup(function(){
			var t = $('#barTop').val();
			var l = $('#barLeft').val();

			changePosition(t,l,'bcbar');
		});
		$('#barLabelTop,#barLabelLeft,#barLabelSize').keyup(function(){
			var t = $('#barLabelTop').val();
			var l = $('#barLabelLeft').val();
			var s =  $('#barLabelSize').val();
			var spacing = $('#titleSpacing').val();
			var bg = $('#barLabelBackgroundColor').val();
			changeSpacing(spacing,'bclabel')
			changeSize(s,'bclabel');
			changePosition(t,l,'bclabel');
		});
		$('#barLabelSpacing').keyup(function(){
			changeSpacing($(this).val(),'bclabel')
		});
		$('#barLabelVisible').change(function(){
			changeVisibility($(this).is(":checked"),'bclabel');
		});
		$('#barLabelBold').change(function(){
			changeBold($(this).is(":checked"),'bclabel');
		});
		$('#extraDescTop,#extraDescLeft,#extraDescSize,#extraDescValue').keyup(function(){
			var t = $('#extraDescTop').val();
			var l = $('#extraDescLeft').val();
			var s =  $('#extraDescSize').val();
			var val = $('#extraDescValue').val();
			$('.extradesc').html(val);
			changeSize(s,'extradesc');
			changePosition(t,l,'extradesc');
		});
		$('#extraDescVisible').change(function(){
			changeVisibility($(this).is(":checked"),'extradesc');
		});
		$('#extraDescBold').change(function(){
			changeBold($(this).is(":checked"),'extradesc');
		});
		$('#itemCodeTop,#itemCodeLeft,#itemCodeSize').keyup(function(){
			var t = $('#itemCodeTop').val();
			var l = $('#itemCodeLeft').val();
			var s =  $('#itemCodeSize').val();
			changeSize(s,'itemcode');
			changePosition(t,l,'itemcode');
		});
		$('#itemCodeVisible').change(function(){
			changeVisibility($(this).is(":checked"),'itemcode');
		});
		$('#itemCodeBold').change(function(){
			changeBold($(this).is(":checked"),'itemcode');
		});
		$('#categoryTop,#categoryLeft,#categorySize').keyup(function(){
			var t = $('#categoryTop').val();
			var l = $('#categoryLeft').val();
			var s =  $('#categorySize').val();
			changeSize(s,'category');
			changePosition(t,l,'category');
		});
		$('#priceTop,#priceLeft,#priceSize').keyup(function(){
			var t = $('#priceTop').val();
			var l = $('#priceLeft').val();
			var s =  $('#priceSize').val();
			changeSize(s,'price');
			changePosition(t,l,'price');
		});
		$('#supcategTop,#supcategLeft,#supcategSize').keyup(function(){
			var t = $('#supcategTop').val();
			var l = $('#supcategLeft').val();
			var s =  $('#supcategSize').val();
			changeSize(s,'supcateg');
			changePosition(t,l,'supcateg');
		});
		$('#storecodeTop,#storecodeLeft,#storecodeSize,#storecodeValue, #storecodeRotate').keyup(function(){
			var t = $('#storecodeTop').val();
			var l = $('#storecodeLeft').val();
			var s =  $('#storecodeSize').val();
			var r =  $('#storecodeRotate').val();
			var val = $('#storecodeValue').val();
			$('.storecode').html(val);
			changeSize(s,'storecode');
			rotateElement(r,'storecode');
			changePosition(t,l,'storecode');
		});
		$('#dateTop,#dateLeft,#dateSize').keyup(function(){
			var t = $('#dateTop').val();
			var l = $('#dateLeft').val();
			var s =  $('#dateSize').val();
			changeSize(s,'date');
			changePosition(t,l,'date');
		});
		$('#dateVisible').change(function(){
			changeVisibility($(this).is(":checked"),'date');
		});
		$('#dateBold').change(function(){
			changeBold($(this).is(":checked"),'date');
		});
		$('#storecodeVisible').change(function(){
			changeVisibility($(this).is(":checked"),'storecode');
		});
		$('#storecodeBold').change(function(){
			changeBold($(this).is(":checked"),'storecode');
		});
		$('#priceVisible').change(function(){
			changeVisibility($(this).is(":checked"),'price');
		});
		$('#priceBold').change(function(){
			changeBold($(this).is(":checked"),'price');
		});
		$('#categoryVisible').change(function(){
			changeVisibility($(this).is(":checked"),'category');
		});
		$('#categoryBold').change(function(){
			changeBold($(this).is(":checked"),'category');
		});
		$('#conHeight,#conWidth,#conLeft,#conTop').keyup(function(){
			var t = $('#conTop').val();
			var l = $('#conLeft').val();
			var w = $('#conWidth').val();
			var h = $('#conHeight').val();

			changePosition(t,l,'bcholder');
			changeHW(h,w,'bcholder');
		});

		function changeVisibility(v,title){
			if(v){
				$("."+title).show();
			} else {
				$("."+title).hide();
			}
		}
		function changeBold(v,title){
			if(v){
				$("."+title).css({"fontWeight": "bold"});
			} else {
				$("."+title).css({"fontWeight": "normal"});
			}
		}
		function changeHW(h,w,title){
			$("."+title).css({"height": h+"px", "width": w+"px"});
		}
		function changeBG(bg,title){
			$("."+title).css({"backgroundColor":title});
		}
		function changePosition(t,l,title){
			$("."+title).css({"top": t+"px", "left": l+"px"});
		}
		function changeSize(s,title){
			$("."+title).css({"fontSize": s+"px"});
		}
		function changeSpacing(s,title){
			$("."+title).css({"letterSpacing": s+"px"});
		}
		function rotateElement(s,title){
			$("."+title).css({'-webkit-transform' : 'rotate('+ s +'deg)',
				'-moz-transform' : 'rotate('+ s +'deg)',
				'-ms-transform' : 'rotate('+ s +'deg)',
				'transform' : 'rotate('+ s +'deg)'});
		}
		$('#save').click(function(){
			$('.loading').show();
			var allstyle = {
				title : {
					top : $('#titleTop').val(),
					left : $('#titleLeft').val(),
					value : $('#titleValue').val(),
					fontSize :  $('#titleSize').val(),
					letterSpacing: $('#titleSpacing').val()
				},
				bar: {
					top : $('#barTop').val(),
					left : $('#barLeft').val(),
					height : $('#barHeight').val()
				},
				barLabel:{
					top : $('#barLabelTop').val(),
					left :  $('#barLabelLeft').val(),
					fontSize :  $('#barLabelSize').val(),
					backgroundColor :  $('#barLabelBackgroundColor').val(),
					display : $('#barLabelVisible').is(':checked'),
					fontWeight: $('#barLabelBold').is(':checked'),
					letterSpacing: $('#barLabelSpacing').val()
				},
				extraDesc: {
					top : $('#extraDescTop').val(),
					left : $('#extraDescLeft').val(),
					fontSize :  $('#extraDescSize').val(),
					display : $('#extraDescVisible').is(':checked'),
					fontWeight: $('#extraDescBold').is(':checked'),
					value : $('#extraDescValue').val()
				},
				itemcode: {
					top : $('#itemCodeTop').val(),
					left : $('#itemCodeLeft').val(),
					fontSize :  $('#itemCodeSize').val(),
					display : $('#itemCodeVisible').is(':checked'),
					fontWeight: $('#itemCodeBold').is(':checked')
				},
				category: {
					top : $('#categoryTop').val(),
					left : $('#categoryLeft').val(),
					fontSize :  $('#categorySize').val(),
					display : $('#categoryVisible').is(':checked'),
					fontWeight: $('#categoryBold').is(':checked')
				},
				price: {
					top : $('#priceTop').val(),
					left : $('#priceLeft').val(),
					fontSize :  $('#priceSize').val(),
					display : $('#priceVisible').is(':checked'),
					fontWeight: $('#priceBold').is(':checked')
				},
				supcateg: {
					top : $('#supcategTop').val(),
					left : $('#supcategLeft').val(),
					fontSize :  $('#supcategSize').val(),
					display : $('#supcategVisible').is(':checked'),
					fontWeight: $('#supcategBold').is(':checked')
				},
				storecode: {
					top : $('#storecodeTop').val(),
					left : $('#storecodeLeft').val(),
					fontSize :  $('#storecodeSize').val(),
					display : $('#storecodeVisible').is(':checked'),
					fontWeight: $('#storecodeBold').is(':checked'),
					value : $('#storecodeValue').val(),
					rotate: $('#storecodeRotate').val()
				},
				date: {
					top : $('#dateTop').val(),
					left : $('#dateLeft').val(),
					fontSize :  $('#dateSize').val(),
					display : $('#dateVisible').is(':checked'),
					fontWeight: $('#dateBold').is(':checked')
				},
				container:{
					top :  $('#conTop').val(),
					left : $('#conLeft').val(),
					width :  $('#conWidth').val(),
					height :  $('#conHeight').val()
				},
				settings: {
					howmany: $('#howMany').val(),
					type:$('#type').val()
				}
			};
			var family = $('#fid').val();

			$.ajax({
				url:'../ajax/ajax_query.php',
				type:'post',
				data: {fid:family,styles:JSON.stringify(allstyle),functionName:'saveBarcode'},
				success: function(data){
					alert(data);
					location.href ='barcode-generator.php';
				},
				error:function(){

					$('.loading').hide();
				}
			});
		});
		$('#print').click(function(){
			printBarcode();
		});
		$('#fid').change(function(){
			//$(this).parent('form').submit();
		});
		function printBarcode(){
			$('#editor').hide();
			$('#sidebar-wrapper').hide();
			window.print();
			$('#sidebar-wrapper').show();
			$('#editor').show();
		}
	});
</script>