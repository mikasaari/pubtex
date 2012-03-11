$(document).ready(function() 
{ 
	var srchstr = "";

	$("#lnksign").mouseover(function()
	{
	
		$("#sign_box").show();
		return false;
	}).mouseout(function()
	{
		$("#sign_box").hide();
		return true;
	});

	$("body #main").click(function()
	{
		$("#sign_box").hide();
		return false;
	});	

	$('#target').keyup(function(event) 
	{
		
	}).keydown(function(event) 
	{
		if (event.which == 13) 
		{
			event.preventDefault();
  		}  
		
		if(event.which == 32)
		{
			var tmp = document.searchform.search.value;
			var ta = new Array();
			var ns = "/ajax-image?";

			ta = tmp.split(' ');
			for(x in ta)
			{
				ns = ns + ta[x];
				if(x < ta.length-1)
					ns = ns + "&";
			}

			searchGallery(ns);
		}		

		else if(event.which == 8)
		{
			var tmp = document.searchform.search.value;
			var ta = new Array();
			var ns = "/ajax-image?";

			ta = tmp.split(' ');
			last = ta.length - 1;
			if(last < 0)
				last = 0;

			for(x=0;x<last;x=x+1)
			{
				ns = ns + ta[x];
				if(x < ta.length-1)
					ns = ns + "&";
			}
			
			if(last > 0)	
			{
				searchGallery(ns);
			}
			else
			{
				ns = ns + 'latestgalleryimages=20';
				searchGallery(ns);
			}
		}

		else if(document.searchform.search.value != srchstr)
		{
			var tmp = document.searchform.search.value;
			var ta = new Array();
			var ns = "/ajax-image?";

			ta = tmp.split(' ');
			last = ta.length - 1;
			if(last < 0)
				last = 0;

			for(x=0;x<last;x=x+1)
			{
				ns = ns + ta[x] + "&";
			}
			
			ns = ns + 'having='+ta[last];
			
			searchGallery(ns);

			srchstr = document.searchform.search.value;
		}
	});

	function searchGallery(ns)
	{
			$.get(ns, function(data)
			{
				var obj = jQuery.parseJSON(data);
				$(".allimages").remove();

				$(".image-gallery").append('<div class="allimages">><div style=\"clear: both;\"></div><br></div>');

				for(x in obj)
				{
					var first = obj[x].hash_name.substring(0,2);
					var second = obj[x].hash_name.substring(2,4);
					var imgfile = '/images/'+first+'/'+second+'/'+obj[x].hash_name+'_thumb.png';
					$(".allimages").append('<span class="mediacontainer"><span class="media"><span class="mediathumb"><a href="/media-details?media_name='+obj[x].hash_name+'"><img src="'+imgfile+'"/></a></span></span></span>');
				}
			});
		}
});





