<div class="wrap">

<h2>WP LOYALTY by AD5</h2>

<?php AD5_Loyalty_Admin::admin_page_menu( 'ad5-loyalty-option' ); ?>

<form method="post" action="options.php" id="msf">
<?php
	settings_fields( 'ad5-loyalty-option-group' );
	do_settings_sections( 'ad5-loyalty-option-group' );
?>

<table class="form-table">
<tr>
<th scope="row"><label><?php $this->e( 'Register Disabled' ); ?></label></th>
<td>
	<p><?php $this->e( 'Check if not allow users to sign up.' ); ?></p>
	<input type="hidden" name="ad5_loyalty_setting[register_disabled]" value="0">
	<p><label><input type="checkbox" name="ad5_loyalty_setting[register_disabled]" value="1" <?php if ( $this->get_setting('register_disabled') ) { echo 'checked'; } ?>><?php $this->e( 'Register Disabled' ); ?></label></p>
</td>
</tr>

<tr>
<th scope="row"><label><?php $this->e( 'Default contents' ); ?></label></th>
<td>
	<p><?php $this->e( 'Contents below will be outputted after main content of all posts.' ); ?></p>
	<p><?php $this->e( 'These setting can be overwritten by Loyalty Contents setting of each post.' ); ?></p>
	<h4><?php $this->e( 'For guests' ); ?></h4>
	<?php wp_editor( get_option('ad5_loyalty_default_content_guest'), 'ad5_loyalty_default_content_guest' ); ?>
	<h4><?php $this->e( 'For members' ); ?></h4>
	<?php wp_editor( get_option('ad5_loyalty_default_content_user'), 'ad5_loyalty_default_content_user' ); ?>
</td>
</tr>

<tr>
<th scope="row"><label><?php $this->e( 'Design Setting' ); ?></label></th>
<td>
	<h4><?php $this->e( 'Sign Up button' ); ?></h4>
	<input type="text" name="ad5_loyalty_setting[color_button_primary]" class="jscolor {width:400, height:250, hash:true}" value="<?php echo $this->get_setting( 'color_button_primary' ); ?>">
	<h4><?php $this->e( 'Sign In / Sign Out button' ); ?></h4>
	<input type="text" name="ad5_loyalty_setting[color_button_secondary]" class="jscolor {width:400, height:250, hash:true}" value="<?php echo $this->get_setting( 'color_button_secondary' ); ?>">
</td>
</tr>

</table>

<?php submit_button(); ?>

</form>