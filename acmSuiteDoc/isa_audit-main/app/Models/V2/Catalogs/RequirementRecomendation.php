<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\Audit;
use App\Traits\V2\UtilitiesTrait;

class RequirementRecomendation extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 't_requirement_recomendations';
  protected $primaryKey = 'id_recomendation';

  protected $fillable = [
    'id_requirement',
    'recomendation'
  ];

  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'id_requirement' => ['field' => 'id_requirement', 'type' => 'number', 'relation' => null],
		'recomendation' => ['field' => 'recomendation', 'type' => 'string', 'relation' => null],
	];

  /**
   * custom filters or detail filters 
   */
  public function scopeCustomFilters($query, $idRequirement = null)
  {
    if ( !is_null($idRequirement) ) {
      $query->where('id_requirement', $idRequirement);
    }

    $filters = request('filters');
		if ( empty($filters) ) return;
  }

  /**
   * Get all of the audits for the RequirementRecomendation
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function audits()
  {
    return $this->hasMany(Audit::class, 'id_recomendation', 'id_recomendation');
  }
}
  