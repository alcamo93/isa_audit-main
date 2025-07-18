<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Aspect extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_aspects';
  protected $primaryKey = 'id_aspect';

  /*
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'aspect',
		'order',
		'id_matter',        
	];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    'matter',
    //'guidelines',
  ];

  /**
   * Attributes 
   */
  protected $appends = [
    'color',
    'full_path',
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'aspect' => ['field' => 'aspect', 'type' => 'string', 'relation' => null],
    'id_aspect' => ['field' => 'id_aspect', 'type' => 'number', 'relation' => null],
    'id_matter' => ['field' => 'id_matter', 'type' => 'number', 'relation' => null],
  ];

  /**
   * Get colors per matter
   */
  public function getColorAttribute()
  {
    $matters = [
      1 => '#4eaf8f',
      2 => '#113C53',
      3 => '#2581cc',
      4 => '#7F7F7F',
    ];
    return $matters[$this->id_matter] ?? '#ffffff';
  }

  /*
   * Get Full Name
   */
  public function getFullPathAttribute()
  {
    $domain = asset('');
    $fullPath = "{$domain}assets/img/services/s2.png";
    return $fullPath;
  }
  
  /**
   * Get the matter that owns the Aspect
   */
  public function matter()
  {
    return $this->belongsTo('App\Models\V2\Catalogs\Matter', 'id_matter', 'id_matter');
  }

  /**
   * Get the guidelines.
   */
  public function guidelines()
  {
      return $this->belongsToMany(Guideline::class, 'aspect_guideline', 'aspect_id', 'guideline_id');
  }

  /**
   * custom filters or detail filters 
   */
  public function scopeCustomFilters($query, $idMatter = null)
  {
    if ( !is_null($idMatter) ) {
      $query->where('id_matter', $idMatter);
    }

    $filters = request('filters');
		if ( empty($filters) ) return;
    
  }

  public function scopeCustomOrder($query) 
  {
    $query->orderBy('order', 'asc');
  }

  /**
   * get aspects for relations with a guideline
   */
  public function scopeGetRelationsForGuideline($query, $idGuideline)
  {
    $filters = request('filters');

    $query->with(['guidelines' => function($query) use ($idGuideline) {
      $query->where('guideline_id', $idGuideline);
    }]);

    if(isset($filters['has_relation']) && boolval($filters['has_relation'])){
      $query->whereHas('guidelines', function($subquery) use ($idGuideline) {
        $subquery->where('guideline_id', $idGuideline);
      });
    } else if (isset($filters['has_relation']) && !boolval($filters['has_relation'])){
      $query->whereDoesntHave('guidelines', function($subquery) use ($idGuideline) {
        $subquery->where('guideline_id', $idGuideline);
      });
    }

    if(isset($filters['id_matter'])){
      $id_matter = $filters['id_matter'];
      $query->whereHas('matter', function($subquery) use ($id_matter) {
        $subquery->where('id_matter', $id_matter);
      });
    }

    if (isset($filters['aspect_name']) && !empty($filters['aspect_name'])) {
      $query->where('aspect', 'like', '%' . $filters['aspect_name'] . '%');
    }
  }
}