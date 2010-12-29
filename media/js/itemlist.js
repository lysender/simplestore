$(function(){
	$("#category_selector").change(function(){
		var url = base_url + "inventory/item/";
		
		if (this.value)
		{
			url += "index/" + this.value;
		}
		
		window.location.href = url;
	});
	
});