<?php

namespace App\Models\V2\Historical;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\Aspect;
use App\Traits\V2\UtilitiesTrait;

class HistoricalAspect extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'historical_aspect';

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'total',
    'total_count',
    'date',
    'aspect_id',
    'historical_matter_id',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'aspect',
  ];

  /**
   * Get the aspect that owns the HistoricalAspect
   */
  public function aspect()
  {
    return $this->belongsTo(Aspect::class, 'aspect_id', 'id_aspect');
  }
}