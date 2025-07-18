<?php

namespace App\Models\V2\Historical;

use App\Models\V2\Historical\HistoricalMatter;
use App\Traits\V2\UtilitiesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Historical extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'historical';

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'total',
    'total_count',
    'date',
    'historicalable_id',
    'historicalable_type',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'matters',
  ];
  
  /**
   * Get the parent historicalable model (ActionRegister or ObligationRegister).
   */
  public function historicalable()
  {
    return $this->morphTo();
  }

  /**
   * Get all of the matters for the Historical
   */
  public function matters()
  {
    return $this->hasMany(HistoricalMatter::class);
  }

  /**
   * subquery get last record in month per model
   */
  public function scopeLastInMonth($query, $year, $historicalableId, $historicalableType)
  {
    $subQuery = Historical::select(DB::raw('MAX(date) as max_date'))
      ->where('historicalable_id', $historicalableId)
      ->where('historicalable_type', $historicalableType)
      ->whereYear('date', $year)
      ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'));

    $query->whereIn('date', $subQuery);
  }
}