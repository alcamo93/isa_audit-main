<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Category extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_categories';
  protected $primaryKey = 'id_category';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    // 
  ];
}
