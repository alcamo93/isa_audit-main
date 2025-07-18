<?php

namespace App\Models\V2\Catalogs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\V2\UtilitiesTrait;

class LegalClassification extends Model
{
  use HasFactory, UtilitiesTrait;

  protected $table = 'c_legal_classification';
  protected $primaryKey = 'id_legal_c';

  const LAW = 1;
  const REGULATION = 2;
  const RULE = 3;
  const AGREEMENTS = 4;
  const CODE = 5;
  const DECREE = 6;
  const GUIDELINES = 7;
  const LEGAL_ORDER = 8;
  const NOTICE = 9;
  const SUMMONS = 10;
  const PLAN = 11;
  const PROGRAM = 12;
  const RECOMMENDATIONS = 13;
  const REFORM = 14;
}