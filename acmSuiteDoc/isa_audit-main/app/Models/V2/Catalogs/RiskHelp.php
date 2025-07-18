<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\RiskAttribute;
use App\Traits\V2\UtilitiesTrait;

class RiskHelp extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 't_risk_help';
  protected $primaryKey = 'id_risk_help';
    
  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'risk_help',
    'standard',
    'value',
    'id_risk_category',
    'id_risk_attribute',
	];

	/**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
		// 'attribute'
  ];

	/**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'risk_help' => ['field' => 'risk_help', 'type' => 'string', 'relation' => null],
		'standard' => ['field' => 'standard', 'type' => 'string', 'relation' => null],
		'id_risk_attribute' => ['field' => 'id_risk_attribute', 'type' => 'number', 'relation' => 'attribute'],
	];

	/**
	 * Get the attribute that owns the RiskHelp
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function attribute()
	{
		return $this->belongsTo(RiskAttribute::class, 'id_risk_attribute', 'id_risk_attribute');
	}

  public function scopeFilterRiskCategory($query, $idRiskCategory)
	{
		$query->where('id_risk_category', $idRiskCategory);
	}
}
