$(function(){
	$(".crud-delete a").live("click", function(){
		if ( ! confirm("Are you sure you want to delete this record?"))
		{
			return false;
		}
		
		// Submit the form if present
		var form = $("#crud-pre-delete");
		
		if (typeof form == "object" && form.length)
		{
			// Mechanism for POST only delete
			form.attr("action", this.href);
			form.children("#target").val(this.href);
			form.children("#referer").val(window.location.href);
			form.submit();
			
			return false;
		}
		
		return true;
	});
});