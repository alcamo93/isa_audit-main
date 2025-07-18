<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class ApplicationType extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_application_types';
  protected $primaryKey = 'id_application_type';

  // GROUP 1
  const FEDERAL = 1;
  const STATE = 2;
  const LOCAL = 4;
  // GROUP 2
  const NOT_APPLICABLE = 3;
  // GROUP 3
  const CORPORATE = 5;
  const CONDITIONER = 6;
  const ACT = 7;
  const SPECIFIC = 8;

  public function scopeGetMainGroup($query)
  {
    $query->where('group', 1);
  }

  public function scopeGetSpecificGroup($query)
  {
    $query->where('group', 3);
  }
}