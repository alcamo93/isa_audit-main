<?php

namespace App\Models\V2\Audit;

use Illuminate\Database\Eloquent\Model;

class EvaluationRenewal extends Model
{
	protected $table = 'evaluation_renewals';

	/**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = [
    // 
  ];

	/*
  * The attributes that are mass assignable.
  *
  * @var array
  */
	protected $fillable = [
		'keep_files',
    'keep_risk',
    'date',
    'id_audit_processes',
    'id_user'
	];

	/**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_included = [
    // 
  ];

  /**
   * The relation that allow use in api request.
   *
   * @var array
   */
  protected $allow_filter = [
    // 
  ];
}