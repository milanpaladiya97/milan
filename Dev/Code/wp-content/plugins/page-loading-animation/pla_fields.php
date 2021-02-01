<?php


//*****************  Animation Control CallBack Function  ********************

function pla_animation_upload() {
	wp_enqueue_media();
	?>			
		<img src="<?php echo get_option("pla_animation_upload") ?>" id="pla_animation_pic_select" alt="" height="100px" width="117px" class="show"  style="display: none;"></br>
		<input type="button" name="pla_animation_upload" id="pla_animation_upload" value="Upload Image" class="show" style="display: none;">
		<input type="hidden" name="pla_animation_upload" id="pla_up_img" value="<?php echo get_option("pla_animation_upload") ?>">
	<?php
}

function pla_animation_choose() {
	?>
		<select name="pla_animation_choose" style="width: 155px">

			<?php $choose = get_option('pla_animation_choose'); ?>

			<option value="">Select</option>
			<option <?php if ($choose == 'no') echo 'selected' ;?> value="no">No Animation</option>
			<option <?php if ($choose == 'def1') echo 'selected';?> value="def1">Default 1</option>
			<option <?php if ($choose == 'def2') echo 'selected';?> value="def2">Default 2</option>
			<option <?php if ($choose == 'def3') echo 'selected';?> value="def3">Default 3</option>
			<option <?php if ($choose == 'def4') echo 'selected';?> value="def4">Default 4</option>
			<option <?php if ($choose == 'def5') echo 'selected';?> value="def5">Default 5</option>
			<option <?php if ($choose == 'def6') echo 'selected';?> value="def6">Default 6</option>
			<option <?php if ($choose == 'def7') echo 'selected';?> value="def7">Default 7</option>
			<option <?php if ($choose == 'def8') echo 'selected';?> value="def8">Default 8</option>
			<option <?php if ($choose == 'def9') echo 'selected';?> value="def9">Default 9</option>
			<option <?php if ($choose == 'def10') echo 'selected';?> value="def10">Default 10</option>
			<option <?php if ($choose == 'def11') echo 'selected';?> value="def11">Default 11</option>
			<option <?php if ( $choose == 'custom' ) echo 'selected' ; ?> value="custom"  id="show">Custom</option>
		</select>
	<?php
}


//*****************  Animation Background CallBack Function  ********************

function pla_animation_bg_color() {
	?>
		<input type="color" name="pla_animation_bg_color" id="pla_animation_bg_color" value="<?php echo get_option('pla_animation_bg_color') ?>" style="width: 155px">
	<?php
}
