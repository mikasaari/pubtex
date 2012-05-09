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






