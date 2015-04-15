<?php
//$DP = new DynamicPages();


//--How to use

//-----|Call Function|
//echo $footer->printf();

class HTMLGenerator
{
	
	static function flow ($files,$folders)
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
		foreach ($files as $file) 
		{
		$thumb = new ThumbGen($file["path"], $file["mimetype"], $file["qhash"], 500, $file["htmlpath"],"w");
		$HTML .= '
		<figure class="dgalleryFile hoverZoom" id="f'.$fileCounter.'" >
			<a href="#i'.$file["qhash"].'">
				<div class="imageBlock" hieght="">
					<img id="i'.$file["qhash"].'" src="'.$thumb->getThumb().'" onclick="setBigPicture(this.id)">
					<figcaption>'.$file['title'].'</figcaption>
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
