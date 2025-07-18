<?php

namespace App\Classes\Utilities;

class DetailPath 
{
  private $domain = null;
  private $path = null;

  public function __construct($items, $params)
  {
    $this->domain = Config('enviroment.domain_frontend');
    $encodeURL = base64_encode(json_encode($params));
    $this->path = "v2/details/{$items}/{$encodeURL}/view";
  }

  public function getPath()
  {
    return "{$this->domain}{$this->path}";
  }
}
