<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;
use Illuminate\Support\Facades\Session;

class AnswerValuesModel extends Model
{
    protected $table = 't_answer_values';
    protected $primaryKey = 'id_answer_value';
    /**
    * Return aspects
    */
    public function scopeGetAnswerValues($query){
        $query->orderBy('id_answer_value', 'ASC');
        $data = $query->get()->toArray();
        return $data;
    }
}