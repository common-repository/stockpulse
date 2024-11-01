<?php

// Block If Accessed Directly
if(!defined('ABSPATH')){
	exit;
}

add_action('init', 'stockpulse_widget_init');

function stockpulse_widget_init(){
    add_shortcode('stockpulse_mini', 'stockpulse_build_quotemini');
	add_shortcode('stockpulse_head', 'stockpulse_build_quotehead');
	add_shortcode('stockpulse_inline', 'stockpulse_build_quoteinline');
}

function stockpulse_collect_data($symbol){
	if(isset($symbol) && !empty($symbol)){
		
		$API_Body = array(
			'utm_domain' => STOCKPULSE_DOMAIN,
			'token' => esc_attr(get_option('stockpulse_api_token')),
			'key' => esc_attr(get_option('stockpulse_api_key')),
			'symbol' => $symbol
		);
		$API_Args = array(
			'body' => $API_Body,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array()
		);

		$API_Response = wp_remote_retrieve_body(wp_remote_post('https://api.stockpulse.com/v1/getQuote', $API_Args));

		// Decode json Responce
		$API_Response = json_decode($API_Response, true);

		if($API_Response['status'] === 'success'){
			$data['status'] = $API_Response['status'];
			$data['name'] = $API_Response['equityinfo']['name'];
			$data['symbol'] = $API_Response['equityinfo']['symbol'];
			if(strlen(explode('.', (float)$API_Response['quote']['last'])[1]) >= 3){
				$data['price'] = number_format($API_Response['quote']['last'],3);
			} else {
				$data['price'] = number_format($API_Response['quote']['last'],2);
			}
			$data['change'] = $API_Response['quote']['change'];
			$data['changepercent'] = $API_Response['quote']['changepercent'];
			$data['bid'] = $API_Response['quote']['bid'];
			$data['bidsize'] = $API_Response['quote']['bidsize'];
			$data['ask'] = $API_Response['quote']['ask'];
			$data['asksize'] = $API_Response['quote']['asksize'];
			$data['volume'] = $API_Response['quote']['tradevolume'];
			$data['lasttrade'] = date("M d, Y g:i a e", strtotime($API_Response['quote']['lasttradedatetime']));
		} else if($API_Response['code'] === '00' || $API_Response['code'] === '10'){
			// Store API Error For Dashboard if Received
			update_option('stockpulse_error', 'StockPulse API: ' . $API_Response['message']);

			$data['status'] = $API_Response['status'];
			$data['code'] = $API_Response['code'];
			$data['message'] = $API_Response['message'];
		} else {
			// Clear API Error For Dashboard if Successful
			update_option('stockpulse_error', '');

			$data['status'] = $API_Response['status'];
			$data['code'] = $API_Response['code'];
			$data['message'] = $API_Response['message'];
		}
	} else {
		$data['message'] = 'Missing Symbol Parameter!';
	}

	return $data;
}

function stockpulse_build_quotemini($atts = [], $content = null){
	$symbol = $atts['symbol'];
	
	$data = stockpulse_collect_data($symbol);
	
	if($data['status'] === 'success'){
		$name = $data['name'];
		$symbol = $data['symbol'];
		$price = $data['price'];
		$change = $data['change'];
		$changepercent = $data['changepercent'];
		
		if($change < 0){
			$effect = ' negative';
		} elseif($change > 0) {
			$effect = ' positive';
		}

		$html = "<div class='stockpulse-quotemini stockpulse-widget' title='$name'>";
		$html .= "<span class='ticker'>$symbol</span>";
		$html .= "<span class='price'>$$price</span>";
		$html .= "<span class='change$effect'>$change ($changepercent%)</span>";
		$html .= '</div>';
	} else {
		$error = $data['message'];

		$html = '<div class="stockpulse-quotemini stockpulse-widget">' . $error . '</div>';
	}
	
	
	return $html;
}

function stockpulse_build_quotehead($atts = [], $content = null){
	$symbol = $atts['symbol'];
	
	$data = stockpulse_collect_data($symbol);

	if($data['status'] === 'success'){
		$name = $data['name'];
		$symbol = $data['symbol'];
		$price = $data['price'];
		$change = $data['change'];
		$changepercent = $data['changepercent'];
		//$bid = $data['bid'];
		//$bidsize = $data['bidsize'];
		//$ask = $data['ask'];
		//$asksize = $data['asksize'];
		//$volume = $data['volume'];
		$lasttrade = $data['lasttrade'];
		
		if($change < 0){
			$effect = ' negative';
		} elseif($change > 0) {
			$effect = ' positive';
		}

		$html = "<div class='stockpulse-quotehead stockpulse-widget' title=''>";
		$html .= "<span class='name'>$name ($symbol)</span>";
		$html .= "<span class='price'>$$price <span class='change$effect'>$change ($changepercent%)</span></span>";
		$html .= "<span class='detail'>$lasttrade</span>";
		$html .= '</div>';
	} else {
		$error = $data['message'];

		$html = '<div class="stockpulse-quotehead stockpulse-widget">' . $error . '</div>';
	}
	
	return $html;
}

function stockpulse_build_quoteinline($atts = [], $content = null){
	$symbol = $atts['symbol'];
	
	$data = stockpulse_collect_data($symbol);

	if($data['status'] === 'success'){
		$name = $data['name'];
		$symbol = $data['symbol'];
		$price = $data['price'];
		$change = $data['change'];
		$changepercent = $data['changepercent'];
		//$bid = $data['bid'];
		//$bidsize = $data['bidsize'];
		//$ask = $data['ask'];
		//$asksize = $data['asksize'];
		//$volume = $data['volume'];
		$lasttrade = $data['lasttrade'];
		
		if($change < 0){
			$effect = ' negative';
		} elseif($change > 0) {
			$effect = ' positive';
		}

		$html = "<div class='stockpulse-quoteinline stockpulse-widget' title='$name'>";
		$html .= "<span class='name'>$symbol</span> <span class='quote'>$$price <span class='change$effect'>$change ($changepercent%)</span></span>";
		$html .= '</div>';
	} else {
		$error = $data['message'];

		$html = '<div class="stockpulse-quoteinline stockpulse-widget">' . $error . '</div>';
	}
	
	return $html;
}

?>