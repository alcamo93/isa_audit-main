<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Catalogs\RiskAttribute;
use App\Models\V2\Catalogs\RiskInterpretation;
use App\Traits\V2\UtilitiesTrait;

class RiskCategory extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_risk_categories';
  protected $primaryKey = 'id_risk_category';

  /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'risk_category',
	];

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
    'risk_category' => ['field' => 'risk_category', 'type' => 'string', 'relation' => null],    
	];

  /**
   * The attributes that belong to the RiskCategory
   */
  public function attributes()
  {
    return $this->belongsToMany(RiskAttribute::class, 'risk_category_risk_attribute', 'id_risk_category', 'id_risk_attribute');
  }

  /**
   * Get all of the comments for the RiskCategory
   */
  public function interpretations()
  {
    return $this->hasMany(RiskInterpretation::class, 'id_risk_category', 'id_risk_category');
  }
}
