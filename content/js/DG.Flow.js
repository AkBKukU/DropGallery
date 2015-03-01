var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    viewPortX = w.innerWidth || e.clientWidth || g.clientWidth,
    viewPortY = w.innerHeight|| e.clientHeight|| g.clientHeight;

var fullView = document.getElementById("fullView");
var fullViewFig = document.getElementById("fullViewFig");

var currentImageId;

var updateWindowVars = function()
{
	w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    viewPortX = w.innerWidth || e.clientWidth || g.clientWidth,
    viewPortY = w.innerHeight|| e.clientHeight|| g.clientHeight;
}


var centerdgBigView = function()
{
	var fullViewImage = document.getElementById("fullViewImage");
	var fullViewImageBlock = document.getElementById("fullViewImageBlock");

	var image = document.getElementById(currentImageId);
	updateWindowVars();

	var imgAspect = (image.naturalWidth / image.naturalHeight);
	var viewAspect = (viewPortX/viewPortY);
	var maxWidthForHeight = ((image.naturalWidth/image.naturalHeight)*viewPortY);

	fullViewImageBlock.style.width = image.naturalWidth+"px";
	//image is bigger than view in both dimensions
	if( image.naturalWidth > viewPortX && image.naturalHeight > viewPortY )
	{
		//image aspect ratio is taller the viewport
		if (imgAspect < viewAspect)
		{
			fullViewImageBlock.style.width = maxWidthForHeight+"px";
		}else{
			fullViewImageBlock.style.width = viewPortX+"px";
		}

	//image is taller than view
	}else if( image.naturalHeight > viewPortY )
	{
		fullViewImageBlock.style.width = maxWidthForHeight+"px";
	}

	//image is wider than view
	else if( image.naturalWidth > viewPortX )
	{
		fullViewImageBlock.style.width = viewPortX+"px";
	}

	//rendered image is shorter than view
	if( fullViewImage.height < viewPortY )
	{
		fullViewImageBlock.style.top = 	(viewPortY/2 - fullViewImage.height/2)+"px";
	}else
	{
		fullViewImageBlock.style.top = "0px";
	}
	


	console.log("centerdgBigView: "+image.height);
	console.log("Fit width: " + (fullViewImage.offsetWidth / fullViewImage.offsetHeight ) * viewPortX);
}

var setBigPicture = function(id)
{
	var fullViewImage = document.getElementById("fullViewImage");
	var newImage = document.getElementById(id);
	
	fullViewImage.src = newImage.src;
	currentImageId=id;
	showdgBigView();
}

var hidedgBigView = function()
{
	console.log("hidedgBigView");

	hide("fullView");
}

var backgroundHideBigView = function(e)
{
	if(
		e.target == document.getElementById('fullView') ||
		e.target == document.getElementById('fullViewFig')
		) 
	{
    	hidedgBigView();
    }
}

var showdgBigView = function(id)
{
	show("fullView");
	console.log("showdgBigView");
	centerdgBigView(id);
}

var show = function(id)
{
	var element = document.getElementById(id);
	element.style.display = "block";
}

var hide = function(id)
{
	var element = document.getElementById(id);
	element.style.display = "none";

}

var openLink = function()
{
	var hash = window.location.hash.substring(1);

	if( hash != "" )
	{
		setBigPicture(hash);
		showdgBigView(hash);
	}
}

window.onresize = centerdgBigView;
window.onload = openLink;