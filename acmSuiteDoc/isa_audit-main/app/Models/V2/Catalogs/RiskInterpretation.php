<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class RiskInterpretation extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 't_risk_interpretations';
  protected $primaryKey = 'id_risk_interpretation';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'interpretation',
    'interpretation_min',
    'interpretation_max',
    'id_risk_category',
	];

  /**
	 * Attributes
	 */
	protected $appends = [
		'color',
	];

  /**
	 * Color
	 */
	public function getColorAttribute()
	{
    $string = strtolower($this->interpretation);
		$keyColor = [
			'bajo' => '#4eaf8f',
			'medio' => '#2581cc',
			'alto' => '#113C53',
		];

		return $keyColor[$string];
	}

	public function scopeFilterRiskCategory($query, $idRiskCategory)
	{
		$query->where('id_risk_category', $idRiskCategory);
	}
}
    
