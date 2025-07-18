<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class Matter extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_matters';
  protected $primaryKey = 'id_matter';

  /*
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
    'order',
		'matter',
		'description',
		'color',        
	];
  
  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // TODO: revisar como afecta en exports y dahsboard usar este nombre
    // 'image'
  ];

  /**
   * Attributes 
   */
  protected $appends = [
    'full_path',
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'matter' => ['field' => 'matter', 'type' => 'string', 'relation' => null],
    'id_matter' => ['field' => 'id_matter', 'type' => 'number', 'relation' => null],
  ];
  
  /**
   * Get all of the aspects that owns the Matter
   */
  public function aspects()
  {
    return $this->hasMany('App\Models\V2\Catalogs\Aspect', 'id_matter', 'id_matter');
  }

  /**
	 * Get the matter's image.
	 */
	public function image()
	{
		return $this->morphOne('App\Models\V2\Admin\Image', 'imageable');
	}

  /*
   * Get Full Name
   */
  public function getFullPathAttribute()
  {
    $domain = asset('');
    $fullPath = "{$domain}{$this->image}";
    return $fullPath;
  }

  public function scopeCustomOrder($query)
  {
    $query->orderBy('order', 'ASC');
  }

  public function scopeVerifyOwnershipMatterAspect($query, $idMatter, $idAspect)
  {
    $query->where('id_matter', $idMatter);
    
    if ( !is_null($idAspect) ) {
      $filter = fn($subquery) => $subquery->where('id_aspect', $idAspect);
      $query->with(['aspects' => $filter])
        ->whereHas('aspects', $filter);
    }

    return $query->exists();
  }
}
