<?php

namespace App\Traits\V2;

trait RichTextTrait {

  /**
   * set url in production for view
   */
  public function setUrlInRichText($richText) 
  {
    $productionStorage = Config('enviroment.production_storage');
    $imageProductionInDev = Config('enviroment.view_image_production_in_dev');
    $domain = $productionStorage || $imageProductionInDev ? Config('enviroment.aws_url_view') : env('APP_URL');
    // Create an instance of DOMDocument
    $dom = new \DOMDocument();
    // Omitting formatting errors in HTML
    libxml_use_internal_errors(true);
    // Load HTML in DOMDocument
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $richText);
    // Get all image elements
    $images = $dom->getElementsByTagName('img');
    // If there are no images, return the original content.
    if ($images->length === 0) {
      return $richText;
    }
    foreach ($images as $img) {
      // Get the current value of src
      $src = $img->getAttribute('src');
      // Modify the src by adding 'domain' at the beginning
      $img->setAttribute('src', $domain.$src);
    }
    // Get the modified HTML
    $newHtml = $dom->saveHTML();
    // Restore error handling
    libxml_clear_errors();
    // Return HTML with changes applied
    return $newHtml;
  }
}