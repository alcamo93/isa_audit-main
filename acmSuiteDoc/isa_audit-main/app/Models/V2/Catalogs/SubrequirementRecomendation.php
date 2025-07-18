<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\Audit;
use App\Traits\V2\UtilitiesTrait;

class SubrequirementRecomendation extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 't_subrequirement_recomendations';
  protected $primaryKey = 'id_recomendation';

  protected $fillable = [
    'id_subrequirement',
    'recomendation'
  ];

  /**
	 * The relation that allow use in api request.
	 *
	 * @var array
	 */
	protected $allow_filter = [
		'id_subrequirement' => ['field' => 'id_subrequirement', 'type' => 'number', 'relation' => null],
		'recomendation' => ['field' => 'recomendation', 'type' => 'string', 'relation' => null],
	];

  /**
   * custom filters or detail filters 
   */
  public function scopeCustomFilters($query, $idSubrequirement = null)
  {
    if ( !is_null($idSubrequirement) ) {
      $query->where('id_subrequirement', $idSubrequirement);
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
    return $this->hasMany(Audit::class, 'id_subrecomendation', 'id_recomendation');
  }
} 