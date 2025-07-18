<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\EvaluateRequirement;
use App\Models\V2\Audit\Aplicability;
use App\Models\V2\Audit\ContractMatter;
use App\Models\V2\Catalogs\ApplicationType;
use App\Models\V2\Catalogs\Form;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Aspect;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class ContractAspect extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'r_contract_aspects';
  protected $primaryKey = 'id_contract_aspect';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'form',
    // 'matter',
    // 'aspect',
    // 'status',
    // 'aplicability_answers',
    // 'application_type'
  ];

  /*
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'form_id',
    'self_audit',
    'id_contract_matter',
    'id_contract',
    'id_matter',
    'id_aspect',
    'id_audit_processes',
    'id_status',
    'id_application_type',
    'id_state'
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    'id_contract_matter' => ['field' => 'id_contract_matter', 'type' => 'number', 'relation' => null],
    'id_contract_aspect' => ['field' => 'id_contract_aspect', 'type' => 'number', 'relation' => null],
    'id_status' => ['field' => 'id_status', 'type' => 'number', 'relation' => null],
  ];

  /**
   * Get the status associated with the ContractAspect
   */
  public function form()
  {
    return $this->belongsTo(Form::class);
  }

  /**
   * Get the matter that owns the ContractMatter
   */
  public function matter()
  {
    return $this->belongsTo(Matter::class, 'id_matter', 'id_matter');
  }

  /**
   * Get the aspect that owns the ContractAspect
   */
  public function aspect()
  {
    return $this->belongsTo(Aspect::class, 'id_aspect', 'id_aspect');
  }

  /**
   * Get the status associated with the ContractAspect
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }

  /**
   * Get all of the aplicability_answers for the ContractAspect
   */
  public function aplicability_answers()
  {
    return $this->hasMany(Aplicability::class, 'id_contract_aspect', 'id_contract_aspect');
  }

  /**
   * Get the status associated with the ContractAspect
   */
  public function contract_matter()
  {
    return $this->belongsTo(ContractMatter::class, 'id_contract_matter', 'id_contract_matter');
  }

  /**
   * Get all of the aplicability_answers for the ContractAspect
   */
  public function aplicability_evaluates()
  {
    return $this->hasMany(EvaluateRequirement::class, 'id_contract_aspect', 'id_contract_aspect');
  }

  /**
   * Get all of the evaluate_applicability_question for the ContractAspect
   */
  public function evaluate_question()
  {
    return $this->hasMany(EvaluateApplicabilityQuestion::class, 'id_contract_aspect', 'id_contract_aspect');
  }

  /**
	 * Get the application_type that owns the Guideline
	 */
	public function application_type()
	{
		return $this->belongsTo(ApplicationType::class, 'id_application_type', 'id_application_type');
	}

  public function scopeCustomFilter($query, $idAplicabilityRegister, $idContractMatter = null)
  {
    $queryContractMatter = ContractMatter::where('id_aplicability_register', $idAplicabilityRegister);
    if ( !is_null($idContractMatter) ) {
      $queryContractMatter->where('id_contract_matter', $idContractMatter);
    }
    $idsContractMatter = $queryContractMatter->get()->pluck('id_contract_matter')->toArray();
    $query->whereIn('id_contract_matter', $idsContractMatter);
  }

  public function scopeCustomOrder($query)
	{
		$query->whereHas('matter', function($subquery) {
			$subquery->orderBy('order', 'ASC');
		})->whereHas('aspect', function($subquery) {
			$subquery->orderBy('order', 'ASC');
		});
	}
}