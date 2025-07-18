<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\RiskCategory;
use App\Traits\V2\UtilitiesTrait;

class RiskTotal extends Model
{
  use UtilitiesTrait;

  protected $table = 't_risk_totals';
  protected $primaryKey = 'id_risk_total';

  /*
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'total',
    'registerable_id',
    'registerable_type',
    'id_risk_category',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'category',
  ];

  /**
   * Get the parent registerable model (AuditRegister or ObligationRegister).
   */
  public function registerable()
  {
    return $this->morphTo();
  }

  /**
   * Get the category that owns the RiskTotal
   */
  public function category() 
  {
    return $this->belongsTo(RiskCategory::class, 'id_risk_category', 'id_risk_category');
  }
}
