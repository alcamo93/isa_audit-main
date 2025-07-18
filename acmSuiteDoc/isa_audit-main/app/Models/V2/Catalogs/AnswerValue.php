<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class AnswerValue extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 't_answer_values';
  protected $primaryKey = 'id_answer_value';

  protected $fillable = [
    // 
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
	 * Attributes
	 */
	protected $appends = [
		'color',
	];

  const AFFIRMATIVE = 1;
  const NEGATIVE = 2;

  public function getColorAttribute()
	{
		return $this->id_answer_value == 1 ? 'success' : 'danger';
	}
}