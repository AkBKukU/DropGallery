<?php
//$DP = new DynamicPages();


//--How to use

//-----|Call Function|
//echo $footer->printf();

class HTMLGenerator
{
	
	static function flow ($images,$folders)
	{
        DropGalleryMain::debug("Start HTML gen");
/*

*/

		$HTML='
<div id="dropGallery" class="seamless">
	<div id="fullView" onClick="backgroundHideBigView(event)" style="display:none;">
		<figure id="fullViewFig" onClick="">
			<div id="fullViewImageBlock" onClick="">
				
				<img id="fullViewImage" src="/content/gallery-images/2012-12-26%2018.50.19.jpg" onClick="">
				<figcaption onclick="">text<div id="fullViewClose" class="close" onClick="hidedgBigView()">X</div></figcaption>
			</div>
		</figure>
	</div>
';
		if ($folders != false)
		{
			$HTML .= '
	<ul id="dropGalleryFolders">';
			foreach ($folders as $folder) 
			{
				$HTML .= '
		<li><span class="folderName">'.$folder['name'].'</span> <span class="folderItems">'.$folder['numItems'].' items </span> </li>';
			}
			$HTML .= '
	</ul>';
		}


		$HTML .= '
	<div class="dgalleryFiles">';
		$fileCounter=0;
		foreach ($images as $image) 
		{
		$thumb = new ThumbGen($image["path"], $image["mimetype"], $image["qhash"], 500, $image["htmlpath"],"w");
		$HTML .= '
		<figure class="dgalleryFile hoverZoom" id="f'.$fileCounter.'" >
			<a href="#i'.$image["qhash"].'">
				<div class="imageBlock" hieght="">
					<img id="i'.$image["qhash"].'" src="'.$thumb->getThumb().'" onclick="setBigPicture(this.id)">
					<figcaption>'.$image['title'].'</figcaption>
				</div>
			</a>
		</figure>
	';
		$fileCounter++;
		}

		$HTML .= '
	</div>';

		$HTML .= '
</div>';

        DropGalleryMain::debug("End HTML gen");
		return $HTML;
	}
}
?>
