$(function(){
	$("#name").keyup(function(){
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
});

/** 
 * Search results
 */
var SearchResultBoard = {
	_container: "duplicate-notifier",
	
	clear: function()
	{
		$("#" + this._container + " tbody").html("");
	},
	
	search: function(keyword)
	{
		var url = base_url + "inventory/item/search";
		
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