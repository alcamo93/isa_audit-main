<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class QuestionType extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_question_types';
  protected $primaryKey = 'id_question_type';

  // GROUP 1
  const FEDERAL = 1;
  const STATE = 2;
  const LOCAL = 4;
  // GROUP 2
  const UNLOCK_REQUIREMENT = 3;

  public function scopeGetMainGroup($query)
  {
    $query->whereIn('id_question_type', [$this::FEDERAL, $this::STATE, $this::LOCAL]);
  }
}