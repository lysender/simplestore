$(function(){
	$("#category_selector").change(function(){
		var url = base_url + "inventory/price/list";
		
		if (this.value)
		{
			url += "/" + this.value;
		}
		
		window.location.href = url;
	});
	
	
	// Price cell click turns into editor
	$("td.price-editor").click(function(){
		var elem_id = this.id;
		
		var id = elem_id.split("-").pop();
		
		if (id)
		{
			PriceCell.show_editor(id);
		}
	});
	
	// Price editor blur turns into span without saving
	$("td.price-editor input").blur(function(){
		var elem_id = this.id;
		
		var id = elem_id.split("-").pop();
		
		if (id)
		{
			PriceCell.hide_editor(id);
		}
		
		return false;
	});
	
	// Price editor press enter turns into span
	$("td.price-editor input").keyup(function(e){
		
		// 13 = enter, 27 = escape
		if ( e.which != 13 && e.which != 27)
		{
			return;
		}
		
		var elem_id = this.id;
		
		var id = elem_id.split("-").pop();
		
		if (id)
		{
			if (e.which == 13)
			{
				PriceCell.update_price(id);
			}
			else
			{
				PriceCell.hide_editor(id);
			}
		}
	});	
});

var PriceCell = {
	show_editor: function(id)
	{
		var price_span = $("#price-" + id);
		var price_editor = $("#price-editor-" + id);
		
		var price = price_span.html();
		
		if (isNaN(price))
		{
			price = 0;
		}
		
		// Hide price span
		price_span.hide();
		
		// Show price editor
		price_editor.show().val(price).focus();
		
		return this;
	},
	
	hide_editor: function(id)
	{
		$("#price-editor-" + id).hide();
		$("#price-" + id).show();
		
		return this;
	},
	
	update_price: function(id)
	{
		var price = $("#price-editor-" + id).val();
		
		if ( ! this.check_data(id, price))
		{
			return;
		}
		
		// Format price
		price = this.format_price(price);

		// Show spinner
		$("#price-update-spinner-" + id).show();
		
		// Hide input
		$("#price-editor-" + id).hide();
		
		// Update via ajax
		var url = base_url + "inventory/price/setprice";
		
		$.post(
				url,
				{
					"item": id,
					"price": price,
					"effective_date": ""
				},
				function(data)
				{
					if (data.success)
					{
						$("#price-" + id).html(price).show();
					}
					
					$("#price-update-spinner-" + id).hide();
					
					if (data.message)
					{
						alert(data.message);
					}
				},
				"json"
			);
		
	},
	
	check_data: function(id, price)
	{
		price = price + "";
		
		// Check for price
		var price_regex = /^[0-9]+(\.[0-9][0-9])?$/;
		
		// Test price
		if ( ! price_regex.test(price))
		{
			alert("Invalid price format");
			$("#price-editor-" + id).focus();
			
			return false;
		}
		
		return true;
	},

	format_price: function(price)
	{
		var chunks = price.split(".");

		if (chunks.length < 2)
		{
			// Add two decimal places
			price = price + ".00";
		}

		return price;
	}
};
