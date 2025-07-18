<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\RiskCategory;
use App\Models\V2\Catalogs\RiskAttribute;
use App\Traits\V2\UtilitiesTrait;

class RiskAnswer extends Model
{
  use UtilitiesTrait;

  protected $table = 't_risk_answers';
  protected $primaryKey = 'id_risk_answer';

  /*
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'answer',
    'registerable_id',
    'registerable_type',
    'id_risk_attribute',
    'id_risk_category',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'category',
    // 'attribute',
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

  /**
   * Get the attribute that owns the RiskTotal
   */
  public function attribute() 
  {
    return $this->belongsTo(RiskAttribute::class, 'id_risk_attribute', 'id_risk_attribute');
  }
}
