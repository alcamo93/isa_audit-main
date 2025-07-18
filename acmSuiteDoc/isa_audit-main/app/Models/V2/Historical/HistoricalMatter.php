<?php

namespace App\Models\V2\Historical;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Historical\HistoricalAspect;
use App\Models\V2\Catalogs\Matter;
use App\Traits\V2\UtilitiesTrait;

class HistoricalMatter extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'historical_matter';

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'total',
    'total_count',
    'date',
    'matter_id',
    'historical_id',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'matter',
    // 'aspects',
  ];

  /**
   * Get all of the aspects for the HistoricalMatter
   */
  public function aspects()
  {
    return $this->hasMany(HistoricalAspect::class, 'historical_matter_id', 'id');
  }

  /**
   * Get the matter that owns the HistoricalMatter
   */
  public function matter()
  {
    return $this->belongsTo(Matter::class, 'matter_id', 'id_matter');
  }
}