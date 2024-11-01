<?php

// Block If Accessed Directly
if(!defined('ABSPATH')){
	exit;
}

function stockpulse_help(){
settings_errors();
$stockpulse_error = get_option('stockpulse_error');

?>
<div id="stockpulse" class="wrap">
	<div class="row">
		<div class="col-8">
			<div class="row">
				<div class="col-12">
					<h1 class="p-0"><img src="<?php echo plugins_url('/assets/images/logo.png', __FILE__); ?>" width="150" alt="StockPulse" title="StockPulse"> | Help</h1>
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
					<a class="nav-link" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulsesettings')); ?>">Settings</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="<?php echo esc_url(admin_url('/admin.php?page=stockpulsehelp')); ?>">Help</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active">
					<h4>Getting Started</h4>
					<p>To get started, navigate to the StockPulse Plugin's Dashboard page. Follow the steps to choose which type of which you would like to use, search your stock symbol or company name and then save the widget. Then simply copy and paste the short code into any page or WordPress widget depending on where you would like the widget to be displayed.</p>

					<h4>About The StockPulse Plugin</h4>
					<p>The StockPulse plugin is a utility to provide tool for companies and sites providing information on publically traded companies. This plugin currently offers stock quote short codes, which are snippets of code you can place throughout your website to load a stock quote.</p>

					<h4>New Features Coming Soon</h4>
					<p>The StockPulse Development Team is actively working on improving this plugin to offer more tools for you to easily improve your website; such as historical stock charts, news feeds, latest media and more.</p>

					<h4>Upgrading to Pro or Enterprise</h4>
					<p>While this plugin is free to everyone, StockPulse offers addtional data access, premium support and feature request to those who chose to upgrade. We offer a number of plans which also include more exposure benefits from StockPulse.com and media options. View Plans Here: <a href="https://www.stockpulse.com/services/stockpulse-platform" target="_blank" title="StockPulse Plans">StockPulse Plans</a></p>
				</div>
			</div>
		</div>
		<div class="col-4 mt-5">
			<?php include_once('stockpulse-sidebar.php'); ?>
		</div>
	</div>
</div>
<?php } ?>