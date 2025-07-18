<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\AplicabilityRegister;
use App\Models\V2\Audit\ContractAspect;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class ContractMatter extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'r_contract_matters';
  protected $primaryKey = 'id_contract_matter';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'matter',
    // 'contract_aspects',
    // 'status'
  ];

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'self_audit',
    'id_aplicability_register',
    'id_contract',
    'id_matter',
    'id_audit_processes',
    'id_status',
  ];


  /**
   * Get the aplicability_register that owns the ContractMatter
   */
  public function aplicability_register()
  {
    return $this->belongsTo(AplicabilityRegister::class, 'id_aplicability_register', 'id_aplicability_register');
  }

  /**
   * Get the matter that owns the ContractMatter
   */
  public function matter()
  {
    return $this->belongsTo(Matter::class, 'id_matter', 'id_matter');
  }
  
  /**
   * Get all of the contract_aspects for the ContractMatter
   */
  public function contract_aspects()
  {
    return $this->hasMany(ContractAspect::class, 'id_contract_matter', 'id_contract_matter');
  }

  /**
   * Get the status associated with the ProcessAudit
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }
}