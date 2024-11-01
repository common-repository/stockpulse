jQuery(document).ready(function(){
	/*jQuery("#stockpulse .nav-tabs .nav-link").on("click", function(e){
		e.preventDefault();
		var href = jQuery(this).attr('href');
		
		jQuery("#stockpulse .nav-tabs .nav-link.active").removeClass("active");
		jQuery("#stockpulse .tab-pane.active").removeClass("active");
		jQuery(this).addClass("active");
		jQuery(href).addClass("active");
	});*/
	
	jQuery("#stockpulse .copyshortcode").on("click", function(e){
		e.preventDefault();
		var shortcodeid = jQuery(this).data('id');
		//var shortcode = jQuery("#"+shortcodeid).val();
		jQuery("#"+shortcodeid).select();
		document.execCommand("copy");
	});
	
	jQuery("#stockpulse .addWidget").on("click", function(e){
		e.preventDefault();
		jQuery("#stockpulse #widgetList").fadeOut("fast", function(){
			jQuery("#stockpulse #widgetBuilder").fadeIn("fast");
		});
	});
	
	jQuery("#stockpulse #cancelWidget").on("click", function(e){
		e.preventDefault();
		jQuery("#stockpulse #widgetBuilder").fadeOut("fast", function(){
			jQuery("#stockpulse .widgetType.active").removeClass("active");
			jQuery("#stockpulse #widgetSymbol").hide();

			jQuery("#stockpulse #widgetList").fadeIn("fast");
		});
	});
	
	jQuery("#stockpulse .widgetType").on("click", function(e){
		e.preventDefault();
		var widgetType = jQuery(this).data('type');
		
		jQuery('#stockpulse input[name=widgettype]').val(widgetType);
		
		jQuery("#stockpulse .widgetType.active").removeClass("active");
		jQuery(this).addClass("active");
		jQuery("#stockpulse #widgetSymbol").fadeIn("fast");
	});
	
	jQuery("#stockpulse #searchSymbol").on("click", function(e){
		e.preventDefault();
		var utm_domain = jQuery("#stockpulse #widgetBuilder input[name='utm_domain']").val();
		var token = jQuery("#stockpulse #widgetBuilder input[name='token']").val();
		var key = jQuery("#stockpulse #widgetBuilder input[name='key']").val();
		var search = jQuery("#stockpulse #widgetBuilder input[name='searchValue']").val();
		jQuery("#stockpulse #searchResults").empty();
		
		jQuery.ajax({
			method: "POST",
			url: "https://api.stockpulse.com/v1/getSymbol",
			data: { utm_domain:utm_domain, token:token, key:key, search: search },
			dataType: "json"
		}).done(function(jsonData){
			if(jsonData.error){
				//notification('We encountered an error!', 'danger');
			} else {
				// if Profile Data is available
				if(typeof jsonData.profiles !== 'undefined' && Object.keys(jsonData.profiles).length > 0){
					jQuery.each(jsonData.profiles, function(index, profile){
						var profile_name = jsonData.profiles[index].profile_name;
						var profile_exchange = jsonData.profiles[index].profile_exchange;
						var profile_symbol = jsonData.profiles[index].profile_symbol;
						var profile_datasymbol = jsonData.profiles[index].profile_datasymbol;
						
						var html = "<div class='row mt-2'>" +
							"<div class='col-1'><input class='' type='radio' name='widgetsymbol' value='" + profile_datasymbol + "'></div>" +
							"<div class='col-4'>" + profile_name + "</div>" +
							"<div class='col-4'>(" + profile_exchange + ": " + profile_symbol + ")</div>" +
							"</div>";
							
						jQuery("#stockpulse #searchResults").append(html);
					});
					jQuery("#stockpulse #widgetBuilder #submit").fadeIn("fast");
				} else {
					
					jQuery("#stockpulse #searchResults").append("No Results Found!");
					jQuery("#stockpulse #widgetBuilder #submit").hide();
				}
				
				jQuery("#grid").find("#gridBig, #gridSmall, #gridList").empty();
			}
		}).fail(function(){
			console.error(new Date().toUTCString(), 'StockPulse: SERVER ERROR There seems to be an issue with the connection with the server.');
			return false;
		}).always(function(){
			
		});
	});
});