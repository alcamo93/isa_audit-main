<?php

namespace App\Classes\Files;

use App\Traits\FileTrait;
use Illuminate\Support\Facades\Storage;

class ImageDecodeToFile
{
	use FileTrait;

	public $richText, $createdImg, $linksExistImg, $diskStore, $dynamicDirectory, $maxFile, $allCreate, $diskName;

	public function __construct($diskStore, $dynamicDirectory, $maxFile = true)
	{
		$this->richText = null;
		$this->createdImg = [];
		$this->linksExistImg = [];
		$this->diskName = $diskStore;
		$this->diskStore = $this->getStorageEnviroment($diskStore);
		$this->dynamicDirectory = $dynamicDirectory;
		$this->maxFile = $maxFile;
		$this->allCreate = true;
	}
	/**
	 * Extract imgBase64 to store in file
	 */
	public function decodeImg64ToLink($text, $defineVersion = null)
	{
		if ($this->maxFile) {
			define('MAX_FILE_SIZE', 999999999999999);
		}
		ini_set('memory_limit', '2048M');
		ini_set('post_max_size', '64M');
		ini_set('upload_max_filesize', '64M');
		set_time_limit(3600);

		$this->createdImg = [];
		$this->linksExistImg = [];
		$this->diskName = is_null($defineVersion) ? $this->diskName : "{$defineVersion}/{$this->diskName}";
		try {
			// Create an instance of DOMDocument
			$dom = new \DOMDocument();
			// Omitting formatting errors in HTML
			libxml_use_internal_errors(true);
			// Load HTML in DOMDocument
			$dom->loadHTML('<?xml encoding="utf-8" ?>' . $text);
			// Get all image elements
			$tagImages = $dom->getElementsByTagName('img');
			// If there are no images, return the original content.
			if ($tagImages->length === 0) {
				$this->richText = $text;
				return;
			}
			foreach ($tagImages as $tag) {
				$image64 = $tag->getAttribute('src');
				// Eg: typeFile = data:image/png
				$typeFile = substr($image64, 0, strpos($image64, ';'));
				if ($typeFile != '') {
					// Eg: fileExtension = image/png
					$fileExtension = explode(':', $typeFile)[1];
					// Eg: //.png .jpg .jpeg
					$extension = explode('/', $fileExtension)[1];
					$replace = substr($image64, 0, strpos($image64, ',') + 1);
					// find substring fro replace here eg: data:image/png;base64,
					$bodyImage = str_replace($replace, '', $image64);
					$bodyImage = str_replace(' ', '+', $bodyImage);
					$imageName = md5(microtime()) . '.' . $extension;
					// Move / Create file in directory
					Storage::disk($this->diskStore)->put($this->dynamicDirectory . '/' . $imageName, base64_decode($bodyImage));
					// Verify if missing file in directory and cancel process
					if (Storage::disk($this->diskStore)->missing($this->dynamicDirectory . '/' . $imageName)) {
						$this->allCreate = false;
						return;
					}
					// replace string base64 with link image file
					$currentLink = "/storage/{$this->diskName}/{$this->dynamicDirectory}/{$imageName}";
					$tag->setAttribute('src', $currentLink);
					array_push($this->createdImg, $this->dynamicDirectory . '/' . $imageName);
				} else {
					$linkNameFile = basename($image64);
					$currentLink = "/storage/{$this->diskName}/{$this->dynamicDirectory}/{$linkNameFile}";
					$tag->setAttribute('src', $currentLink);
					array_push($this->linksExistImg, $this->dynamicDirectory . '/' . $linkNameFile);
				}
			}
			// Get the modified HTML
			$this->richText = $dom->saveHTML();
			// Restore error handling
			libxml_clear_errors();
			// Return HTML with changes applied
			$this->allCreate = true;
			return;
		} catch (\Throwable $th) {
			$this->allCreate = false;
		}
	}
}
