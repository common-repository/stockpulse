<?php

// Block If Accessed Directly
if(!defined('ABSPATH')){
	exit;
}

function stockpulse_dashboard(){
	// Example Data
	$example_name = 'Microsoft Corporation';
	$example_symbol = 'NGS: MSFT';
	$example_price = '138.40';
	$example_change = '0.55';
	$example_changepercent = '0.40';
	$example_lasttrade = 'Jul 11, 2019 8:00 pm UTC';
	$example_effect = ' positive';

	if(isset($_POST['stockpulse_newcode'])){
		if(!wp_verify_nonce($_POST['stockpulse_newcode'], 'stockpulse_create_shortcode')){
			exit('Error: Wrong nonce provided!');
		} elseif(isset($_POST['widgettype']) && !empty($_POST['widgettype']) && isset($_POST['widgetsymbol']) && !empty($_POST['widgetsymbol'])){
			$widgettype = sanitize_text_field($_POST['widgettype']);
			$widgetsymbol = sanitize_text_field($_POST['widgetsymbol']);

			$stockpulse_shortcodes = get_option('stockpulse_shortcodes');
			delete_option('stockpulse_shortcodes');
			if(isset($stockpulse_shortcodes) && is_array($stockpulse_shortcodes)){
				$stockpulse_shortcodes[] = ['type' => $widgettype, 'symbol' => $widgetsymbol];
				add_option('stockpulse_shortcodes', $stockpulse_shortcodes);
			} else {
				unset($stockpulse_shortcodes);
				$stockpulse_shortcodes[] = ['type' => $widgettype, 'symbol' => $widgetsymbol];
				add_option('stockpulse_shortcodes', $stockpulse_shortcodes);
			}
		}
	}

	if(isset($_POST['stockpulse_deletecode'])){
		if(!wp_verify_nonce($_POST['stockpulse_deletecode'], 'stockpulse_delete_shortcode')){
			exit('Error: Wrong nonce provided!');
		} elseif(isset($_POST['deleteshortcode']) && $_POST['deleteshortcode'] !== null){
			$deleteshortcode = sanitize_text_field($_POST['deleteshortcode']);
			$stockpulse_shortcodes = get_option('stockpulse_shortcodes');
			delete_option('stockpulse_shortcodes');	
			unset($stockpulse_shortcodes[$deleteshortcode]);
			add_option('stockpulse_shortcodes', $stockpulse_shortcodes);
		}
	}
	
	settings_errors();
	$stockpulse_shortcodes = get_option('stockpulse_shortcodes');
	$stockpulse_error = get_option('stockpulse_error');

?>
<div id="stockpulse" class="wrap">
	<div class="row">
		<div class="col-8">
			<div class="row">
				<div class="col-12">
					<h1 class="p-0"><img src="<?php echo plugins_url('/assets/images/logo.png', __FILE__); ?>" width="150" alt="StockPulse" title="StockPulse"> | Dashboard</h1>
					<?php
					if(isset($stockpulse_error) && !empty($stockpulse_error)){
						echo "<div class='error notice'><p>$stockpulse_error</p></div>";
					}
					?>
				</div>
			</div>
			<ul class="nav nav-tabs mt-4">
				<li class="nav-item">
					<a class="nav-link active" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulse')); ?>">Dashboard</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulsesettings')); ?>">Settings</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulsehelp')); ?>">Help</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active">
					<?php
					if(is_array($stockpulse_shortcodes) && !empty($stockpulse_shortcodes)){
						echo '<div id="widgetList" class="row mt-1">
						<div class="col-12">
							<p>
								<button class="addWidget btn btn-sm btn-stockpulse float-right" type="button" title="Add Widget">Add Widget</button>
								<h4 class="text-center">Saved Widgets</h4>
							</p>
							<form method="post" action="admin.php?page=stockpulse">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th scope="col">#</th>
										<th scope="col">Quote Type</th>
										<th scope="col">Symbol</th>
										<th scope="col">Short Code</th>
										<th scope="col" class="text-right"></th>
									</tr>
								</thead>
								<tbody>';
						wp_nonce_field('stockpulse_delete_shortcode', "stockpulse_deletecode");
						foreach($stockpulse_shortcodes as $key => $shortcode){
							$type = $shortcode['type'];
							$symbol = $shortcode['symbol'];
							$code = "[$type symbol=\"$symbol\"]";
							
							echo "<tr>
										<td>1</td>
										<td>$type</td>
										<td>$symbol</td>
										<td><input id='code$key' value='$code' readonly></td>
										<td>
											<button class='copyshortcode btn btn-sm btn-info' type='button' title='Copy' data-id='code$key'><i class='far fa-copy'></i></button>
											<button class='btn btn-sm btn-danger' type='submit' title='Delete' name='deleteshortcode' value='$key'><i class='far fa-trash-alt'></i></button>
										</td>
									</tr>";
						}
						echo '</tbody>
								</table>
							</form>
						</div>
					</div>';
					} else {
						echo '<div id="widgetList" class="row mt-1"><div class="col-12">';
						echo '<h4 class="text-center">Create Your First Widget</h4>';
						echo '<div class="text-center"><button class="addWidget btn btn-lg btn-stockpulse" type="button" title="Add Widget">Add Widget</button></div>';
						echo '</div></div>';
					}
					?>
				
					<div id="widgetBuilder" style="display: none;">
						<form method="post" action="admin.php?page=stockpulse">
							<?php wp_nonce_field('stockpulse_create_shortcode', 'stockpulse_newcode'); ?>
							<input type="hidden" name='utm_domain' value='<?php echo STOCKPULSE_DOMAIN; ?>'>
							<input type="hidden" name='token' value='<?php echo esc_attr(get_option('stockpulse_api_token')); ?>'>
							<input type="hidden" name='key' value='<?php echo esc_attr(get_option('stockpulse_api_key')); ?>'>
							<input type="hidden" name='widgettype' value=''>
							<div id="widgetTypes" class="row mt-1">
								<div class="col-12">
									<p>
										<button id="cancelWidget" class="btn btn-sm btn-warning float-right" type="button" title="Cancel">Cancel</button>
										<h4 class="text-center">Select A Widget Layout</h4>
									</p>
								</div>
								<div class="col-4 text-center mb-3">
									<div class='stockpulse-quotemini stockpulse-widget d-inline-block mb-2' title='<?php echo $example_name; ?>' style="height:75px; font-size:14px;">
										<span class='ticker'><?php echo $example_symbol; ?></span>
										<span class='price'>$<?php echo $example_price; ?></span>
										<span class='change<?php echo $example_effect; ?>'><?php echo $example_change; ?> (<?php echo $example_changepercent; ?>%)</span>
									</div>
									<button type="button" class="widgetType btn btn-outline-stockpulse btn-lg btn-block" data-type="stockpulse_mini">Build Mini Quote</button>
								</div>
								<div class="col-4 text-center mb-3">
									<div class='stockpulse-quotehead stockpulse-widget d-inline-block mb-2' title='<?php echo $example_name; ?>' style="height:75px; font-size:14px;">
										<span class='name'><?php echo $example_name; ?> (<?php echo $example_symbol; ?>)</span>
										<span class='price'>$<?php echo $example_price; ?> <span class='change<?php echo $example_effect; ?>'><?php echo $example_change; ?> (<?php echo $example_changepercent; ?>%)</span></span>
										<span class='detail'><?php echo $example_lasttrade; ?></span>
									</div>
									<button type="button" class="widgetType btn btn-outline-stockpulse btn-lg btn-block" data-type="stockpulse_head">Build Quote Head</button>
								</div>
								<div class="col-4 text-center mb-3">
									<div class='stockpulse-quoteinline stockpulse-widget d-inline-block mb-2' title='<?php echo $example_name; ?>' style="height:75px; font-size:14px;">
										<span class='name'><?php echo $example_symbol; ?></span> <span class='quote'>$<?php echo $example_price; ?> <span class='change<?php echo $example_effect; ?>'><?php echo $example_change; ?> (<?php echo $example_changepercent; ?>%)</span></span>
									</div>
									<div>
										<button type="button" class="widgetType btn btn-outline-stockpulse btn-lg btn-block" data-type="stockpulse_inline">Build Inline Quote</button>
									</div>
								</div>
							</div>
							<div id="widgetSymbol" class="row mt-3" style="display: none;">
								<div class="col-12">
									<h4 class="text-center">Search For Company</h4>
								</div>
								<div class="col-10">
									<div class="form-group">
										<input class="form-control" type="text" name="searchValue" placeholder="ABC Company">
									</div>
								</div>
								<div class="col-2">
									<button id="searchSymbol" type="button" class="btn btn-info btn-block">Search</button>
								</div>
								<div class="col-12">
									<div id="searchResults"></div>
									<input id="submit" class="button button-primary float-right mt-3" type="submit" name="submit" value="Save Widget" style="display: none;">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="col-4 mt-5">
			<?php include_once('stockpulse-sidebar.php'); ?>
		</div>
	</div>
</div>
<?php } ?>