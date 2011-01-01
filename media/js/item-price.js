$(function(){
	$("#search_key").keyup(function(){
		var value = this.value;
		
		if (value.length >= 2)
		{
			SearchResultBoard.search(value);
		}
		else
		{
			SearchResultBoard.clear();
		}
	});
	
	$("a.search-select-price").live("click", function(){
		var id = this.id;
		var item_id = id.split("-").pop();
		
		ItemPriceBoard.load_item(item_id);
		
		return false;
	});
	
	$("#cancel-update").click(function(){
		ItemPriceBoard.hide();
		
		return false;
	});
	
	$("#submit-update").click(function(){
		ItemPriceBoard.submit();
		
		return false;
	});
});

/** 
 * Search results
 */
var SearchResultBoard = {
	_container: "item-price-search",
	
	clear: function()
	{
		$("#" + this._container + " tbody").html("");
	},
	
	search: function(keyword)
	{
		var url = base_url + "inventory/price/search";
		
		$.post(
				url,
				{
					"keyword": keyword
				},
				function(data)
				{
					if (data.success)
					{
						SearchResultBoard.fill(data.content);
					}
					else
					{
						SearchResultBoard.clear();
					}
				},
				"json"
			);
	},
	
	fill: function(html)
	{
		$("#" + this._container + " tbody").html(html);
	}
};

/** 
 * Price board
 */
var ItemPriceBoard = {
	_container: "item-price-editform",
	
	show: function()
	{
		$("#" + this._container).show();
	},
	
	hide: function()
	{
		$("#" + this._container).hide();
	},
	
	load_item: function(item_id)
	{
		var url = base_url + "inventory/price/itemlookup";
		
		$.post(
			url,
			{
				"item_id": item_id
			},
			function(data)
			{
				if (data.success)
				{
					// Load data into form
					$("#name").html(data.name);
					$("#description").html(data.description);
					$("#current_price").html(data.price);
					$("#prev_effective_date").html(data.effective_date);
					
					$("#price").val(data.new_price);
					$("#effective_date").val(data.new_effective_date);
					
					$("#item_id").val(data.item_id);
					
					// Show the price board
					ItemPriceBoard.show();
					
					// Focus to price
					$("#price").focus();
				}
				else
				{
					alert("Cannot load item, reload the page and try again");
				}
			},
			"json"
		);
	},
	
	submit: function()
	{	
		var url = base_url + "inventory/price/setprice";
		var item_id = $("#item_id").val();
		var price = $("#price").val();
		var effdate = $("#effective_date").val();
		
		if ( ! this.check_data(price, effdate))
		{
			return;
		}
	
		$("#submit-spinner").show();
		
		price = this.format_price(price);

		$.post(
			url,
			{
				"item": item_id,
				"price": price,
				"effective_date": $("#effective_date").val()
			},
			function(data)
			{
				if (data.success)
				{
					$("#pricerow-" + item_id).children("td.price-cell").html(price);
				}
				
				$("#submit-spinner").hide();
				
				if (data.message)
				{
					alert(data.message);
				}
			},
			"json"
		);
	},
	
	check_data: function(price, effdate)
	{
		price = price + "";
		effdate = effdate + "";
		
		// Check for price
		var price_regex = /^[0-9]+(\.[0-9][0-9])?$/;
		var date_regex = /^201[0-9]-[01][0-9]-[0123][0-9]$/;
		
		// Test price
		if ( ! price_regex.test(price))
		{
			alert("Invalid price format");
			$("#price").focus();
			
			return false;
		}
		
		// Test date (very minimal)
		if ( ! date_regex.test(effdate))
		{
			alert("Invalid date format");
			$("#effective_date").focus();
			
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
