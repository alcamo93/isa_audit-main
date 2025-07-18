<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use App\Models\V2\Admin\Customer;
use App\Models\V2\Admin\Corporate;
use App\Models\V2\Catalogs\ProfileType;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\RequirementType;
use App\Models\V2\Catalogs\RequirementRecomendation;
use App\Traits\V2\CustomOrderTrait;
use App\Traits\V2\UtilitiesTrait;

class SpecificRequirement extends Model
{
  use HasFactory, UtilitiesTrait, CustomOrderTrait;

  protected $table = 't_requirements';
  protected $primaryKey = 'id_requirement';

  protected $fillable = [
    'id_customer',
    'id_corporate',
    'id_matter',
    'id_aspect',
    'no_requirement',
    'requirement',
    'description',
    'order',
    'id_application_type',
    'id_requirement_type',
    'id_condition',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'matter',
		// 'aspect',
    // 'application_type',
    // 'requirement_type'
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'id_customer' => ['field' => 'id_customer', 'type' => 'number', 'relation' => null],
    'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => null],
    'id_matter' => ['field' => 'id_matter', 'type' => 'number', 'relation' => null],
    'id_aspect' => ['field' => 'id_aspect', 'type' => 'number', 'relation' => null],
    'requirement' => ['field' => 'requirement', 'type' => 'string', 'relation' => null],
    'no_requirement' => ['field' => 'no_requirement', 'type' => 'string', 'relation' => null],
  ];
  
	public function customer() 
	{
		return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
	}

	public function corporate() 
	{
		return $this->belongsTo(Corporate::class, 'id_corporate', 'id_corporate');
	}

  public function matter() {
    return $this->belongsTo(Matter::class, 'id_matter', 'id_matter');
  }

  public function aspect() {
    return $this->belongsTo(Aspect::class, 'id_aspect', 'id_aspect');
  }

  public function application_type()
  {
    return $this->belongsTo(ApplicationType::class, 'id_application_type', 'id_application_type');
  }

  public function requirement_type() 
  {
    return $this->belongsTo(RequirementType::class, 'id_requirement_type', 'id_requirement_type');
  }

  public function recomendations()
  {
    return $this->hasMany(RequirementRecomendation::class, 'id_requirement', 'id_requirement');
  }

  public function scopeSpecificFilters($query) 
  {
    $query->with(['customer', 'corporate'])->where('id_requirement_type', RequirementType::REQUIREMENT_SPECIFIC);

    $level = Session::get('user')['profile_level'];
  
    // get all records
    if ($level == ProfileType::ADMIN_GLOBAL || $level == ProfileType::ADMIN_OPERATIVE) {
      $query->whereNotNull('id_customer')->whereNotNull('id_corporate');
    }

    // get all record for customer
    if ($level == ProfileType::CORPORATE) {
      $idCustomer = Session::get('user')['id_customer'];
      $query->where('id_customer', $idCustomer);
      return;
    }

    // get all record for customer
    if ($level == ProfileType::COORDINATOR || $level == ProfileType::OPERATIVE) {
      $idCustomer = Session::get('user')['id_customer'];
      $idCorporate = Session::get('user')['id_corporate'];
      $query->where('id_customer', $idCustomer)->where('id_corporate', $idCorporate);
      return;
    }
  }

  public function scopeWithIndex($query)
  {
    return $query->with(['matter','aspect','application_type','requirement_type']);
  }
}