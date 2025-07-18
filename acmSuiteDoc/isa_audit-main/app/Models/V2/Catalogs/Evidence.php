<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Evidence extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'c_evidences';
  protected $primaryKey = 'id_evidence';
  
  const SPECIFIC = 4;
}