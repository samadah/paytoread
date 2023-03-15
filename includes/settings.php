<?php wp_head(); ?>
<?php
//Put global con outside
 global $wpdb; 
//If form is posted, do
if(isset($_POST['submit']) && !empty($_POST['submit'])) {
    $pid = "1";
 	$table = $wpdb->prefix . 'p2rplugins_settings';
    $wpdb->query($wpdb->prepare("UPDATE $table SET site_code='".sanitize_text_field($_POST['site_code'])."', button_label='".sanitize_text_field($_POST['button_label'])."',  first_text='".sanitize_text_field($_POST['first_text'])."', second_text='".sanitize_text_field($_POST['second_text'])."' WHERE pid=$pid"));    
}
//Check if the record is present, and get it out
$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}p2rplugins_settings WHERE pid = 1" );
 
	$site_code = $results->site_code;
	$button_label = $results->button_label;
	$first_text = $results->first_text;
	$second_text = $results->second_text;
 
?>
<div class="form-v4">
	<div class="page-content">
		<div class="form-v4-content"> 
				
			 
			<form class="form-detail" method="POST">
				<h2><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/logo.png"></h2> 

				<h2 style="line-height: normal;">Settings</h2>
				<p>You can update pre-added texts based on your preferences:</p>
				 
				<div class="form-row">
					<label for="your_email">Top Text</label>
					<textarea style="border:1px solid #ddd;" name="first_text" id="first_text" class="input-text"><?php echo esc_html($first_text); ?></textarea>
				</div> 

				<div class="form-row">
					<label for="your_email">Highlighted Text</label>
					<textarea style="font-weight: bold;border:1px solid #ddd;" name="second_text" id="second_text" class="input-text"><?php echo esc_html($second_text); ?></textarea>
				</div> 

				<div class="form-row">
					<label for="your_email">Button Label</label>
					<input type="text" style="font-weight: bold;" name="button_label" id="button_label" class="input-text" value="<?php echo esc_html($button_label); ?>">
				</div> 

				<?php if ( null != $results->site_code ) { ?>
				<div class="form-row">
					<label for="your_email">Site Code</label>
					<input type="text" style="font-weight: bold;" name="site_code" id="site_code" class="input-text" autocomplete="off" value="<?php echo esc_html($site_code); ?>">
				</div> 
			    <?php } ?>
				 
				<div class="form-row-last"> 
					<input type="submit" name="submit" class="register" value="Submit">
				</div>
				<br>
				Powered by:<br>
				<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>images/logo-light.png">
			</form> 		
			                       
		</div>
	</div>
</div>
