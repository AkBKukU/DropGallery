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

	fullViewImageBlock.style.width = image.naturalWidth+"px";
	if( image.naturalHeight > viewPortY )
	{
		fullViewImageBlock.style.width = ((image.naturalWidth/image.naturalHeight)*viewPortY)+"px";
	}
	else if( image.naturalWidth > viewPortX )
	{
		fullViewImageBlock.style.width = viewPortX;
	}

	if( image.naturalHeight < viewPortY )
	{
		fullViewImageBlock.style.top = 	(viewPortY/2 - image.naturalHeight/2)+"px";
	}
	else
	{
		fullViewImageBlock.style.top = "0px";
	}
	


	console.log("centerdgBigView");
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