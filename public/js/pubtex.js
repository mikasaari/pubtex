function searchGallery(ns) {
	$.get(ns, function(data)
	{
		var obj = jQuery.parseJSON(data);
		$("#freesearch").remove();

		$("#gallery").append('<div id="freesearch"><div style=\"clear: both;\"></div><br></div>');

		for(x in obj)
		{
			var first = obj[x].file_hash.substring(0,2);
			var second = obj[x].file_hash.substring(2,4);
			var imgfile = '/data/images/'+first+'/'+second+'/'+obj[x].file_hash+'_thumb.png';

			$("#freesearch").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><a href="/media-details?mn='+obj[x].media_hash+'"><img src="'+imgfile+'"/></a></span></span></span>');
		}
	});
}

function getParameterByName(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if(results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

$(document).ready(function() 
{
	$("#signin").mouseover(function()
	{
	
		$("#signin_popup").show();
		return false;
	}).mouseout(function()
	{
		$("#signin_popup").hide();
		return true;
	});

	$("body #main").click(function()
	{
		$("#signin_popup").hide();
		return false;
	});	

});






