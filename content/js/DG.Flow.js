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

	var image = document.getElementById("fullViewImage");
	updateWindowVars();

	//alert("Image: " + image.src+"\n"+"Size: "+image.naturalWidth+"x"+image.naturalHeight);

	var imgAspect = (fullViewImage.naturalWidth / fullViewImage.naturalHeight);
	var imgAspectOp = (fullViewImage.naturalHeight/fullViewImage.naturalWidth);
	var viewAspect = (viewPortX/viewPortY);
	var maxWidthForHeight = ((fullViewImage.naturalWidth/fullViewImage.naturalHeight)*viewPortY);

	fullViewImageBlock.style.width = fullViewImage.naturalWidth+"px";
	var newWidth = fullViewImageBlock.style.width;
	//image is bigger than view in both dimensions
	if( image.naturalWidth > viewPortX && image.naturalHeight > viewPortY )
	{
		//image aspect ratio is taller the viewport
		if (imgAspect < viewAspect)
		{
			newWidth = maxWidthForHeight;
		}else{
			newWidth = viewPortX;
		}

	//image is taller than view
	}else if( image.naturalHeight > viewPortY )
	{
		newWidth = maxWidthForHeight;
	}

	//image is wider than view
	else if( image.naturalWidth > viewPortX )
	{
		newWidth = viewPortX;
	}

	fullViewImageBlock.style.width = newWidth+"px";
	var newHeight = imgAspectOp * newWidth;


	//rendered image is shorter than view
	if( newHeight < viewPortY)
	{
		fullViewImageBlock.style.top = 	(viewPortY/2 - newHeight/2)+"px";
	}else if(image.naturalHeight < viewPortY )
	{
		fullViewImageBlock.style.top = 	(viewPortY/2 - image.naturalHeight/2)+"px";
	}else
	{
		fullViewImageBlock.style.top = "0px";
	}
	


	console.log("centerdgBigView: "+fullViewImage.height);
	console.log("Fit width: " + (fullViewImage.offsetWidth / fullViewImage.offsetHeight ) * viewPortX);
}

var setBigPicture = function(id)
{
	var fullViewImage = document.getElementById("fullViewImage");
	var newImage = document.getElementById(id);
	
	fullViewImage.src = newImage.dataset.orig;
	currentImageId=id;
	fullViewImage.onload=function()
	{
	showdgBigView();	
	}
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

var showdgBigView = function()
{
	console.log("showdgBigView");
	centerdgBigView();
	show("fullView");
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
		//alert(hash);
		setBigPicture(hash);
	}
}

window.onresize = centerdgBigView;
window.onload = openLink;