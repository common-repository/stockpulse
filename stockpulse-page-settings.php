<?php

// Block If Accessed Directly
if(!defined('ABSPATH')){
	exit;
}

function stockpulse_settings(){
	
	
		if(isset($_POST['update_settings']) && isset($_POST['stockpulse_newcode']) && wp_verify_nonce($_POST['stockpulse_newcode'], 'stockpulse_edit_settings')){
			if(isset($_POST['stockpulse_api_token'])){
				$stockpulse_api_token = sanitize_text_field($_POST['stockpulse_api_token']);
				update_option('stockpulse_api_token', $stockpulse_api_token);
			} else {
				update_option('stockpulse_api_token', '');
			}
			if(isset($_POST['stockpulse_api_key'])){
				$stockpulse_api_key = sanitize_text_field($_POST['stockpulse_api_key']);
				update_option('stockpulse_api_key', $stockpulse_api_key);
			} else {
				update_option('stockpulse_api_key', '');
			}
			if(isset($_POST['stockpulse_shortcodes']) && !empty($_POST['stockpulse_shortcodes'])){
				$stockpulse_shortcodes = sanitize_text_field($_POST['stockpulse_shortcodes']);
				
				if($unserialized = unserialize($stockpulse_shortcodes)){
					update_option('stockpulse_shortcodes', $unserialized);
				}
			} else {
				update_option('stockpulse_shortcodes', '');
			}
		}
	
	settings_errors();
	$stockpulse_error = esc_attr(get_option('stockpulse_error'));

?>
<div id="stockpulse" class="wrap">
	<div class="row">
		<div class="col-8">
			<div class="row">
				<div class="col-12">
					<h1 class="p-0"><img src="<?php echo plugins_url('/assets/images/logo.png', __FILE__); ?>" width="150" alt="StockPulse" title="StockPulse"> | Settings</h1>
					<?php
					if(isset($stockpulse_error) && !empty($stockpulse_error)){
						echo "<div class='error notice'><p>$stockpulse_error</p></div>";
					}
					?>
				</div>
			</div>
			<ul class="nav nav-tabs mt-4">
				<li class="nav-item">
					<a class="nav-link" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulse')); ?>">Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulsesettings')); ?>">Settings</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulsehelp')); ?>">Help</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active">
					<p>Manage your StockPulse plugin settings and access credentials and widget defaults here.</p>
					<form method="post" action="admin.php?page=stockpulsesettings">
						<?php wp_nonce_field('stockpulse_edit_settings', 'stockpulse_newcode'); ?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row">Version</th>
								<td><?php echo STOCKPULSE_VERSION; ?></td>
							</tr>

							<tr valign="top">
								<th scope="row">Your Website</th>
								<td><?php echo STOCKPULSE_DOMAIN; ?></td>
							</tr>

							<tr valign="top">
								<th scope="row">API Token</th>
								<td><input type="text" name="stockpulse_api_token" placeholder="XXXXXXXXXXXXXXXXXXX" value="<?php echo esc_attr(get_option('stockpulse_api_token')); ?>" /></td>
							</tr>

							<tr valign="top">
								<th scope="row">API Key</th>
								<td><input type="text" name="stockpulse_api_key" placeholder="XXXXXXXXXXXXXXXXXXX" value="<?php echo esc_attr(get_option('stockpulse_api_key')); ?>" /></td>
							</tr>
							
							<?php
								$stockpulse_shortcodes = get_option('stockpulse_shortcodes');
								if(is_array($stockpulse_shortcodes)){
									$stockpulse_shortcodes = serialize($stockpulse_shortcodes);
							?>
								<tr valign="top">
									<th scope="row">Short Codes</th>
									<td><input type="text" name="stockpulse_shortcodes" placeholder="Serialize Shortcodes" value='<?php echo $stockpulse_shortcodes; ?>' /></td>
								</tr>
							<?php
								}
							?>
						</table>
						<input id="submit" class="button button-primary float-right mt-3" type="submit" name="update_settings" value="Save Changes">
					</form>
				</div>
			</div>
		</div>
		<div class="col-4 mt-5">
			<?php include_once('stockpulse-sidebar.php'); ?>
		</div>
	</div>
</div>
<?php } ?>