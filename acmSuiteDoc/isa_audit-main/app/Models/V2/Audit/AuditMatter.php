<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\V2\Audit\AuditRegister;
use App\Models\V2\Audit\AuditAspect;
use App\Models\V2\Catalogs\Matter;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\UtilitiesTrait;

class AuditMatter extends Model
{
  use UtilitiesTrait;
  
  protected $table = 'r_audit_matters';
  protected $primaryKey = 'id_audit_matter';

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 'matter',
    // 'audit_aspects',
    // 'status'
  ];

  /*
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'self_audit',
    'total',
    'id_audit_register',
    'id_contract',
    'id_matter',
    'id_audit_processes',
    'id_status',
  ];


  /**
   * Get the audit_register that owns the AuditMatter
   */
  public function audit_register()
  {
    return $this->belongsTo(AuditRegister::class, 'id_audit_register', 'id_audit_register');
  }

  /**
   * Get the matter that owns the AuditMatter
   */
  public function matter()
  {
    return $this->belongsTo(Matter::class, 'id_matter', 'id_matter');
  }
  /**
   * Get all of the audit_aspects for the AuditMatter
   */
  public function audit_aspects()
  {
    return $this->hasMany(AuditAspect::class, 'id_audit_matter', 'id_audit_matter');
  }

  /**
   * Get the status associated with the ProcessAudit
   */
  public function status()
  {
    return $this->belongsTo(Status::class, 'id_status', 'id_status');
  }
}