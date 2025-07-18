<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\RiskHelp;
use App\Traits\V2\UtilitiesTrait;

class RiskAttribute extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_risk_attributes';
  protected $primaryKey = 'id_risk_attribute';
  
}
