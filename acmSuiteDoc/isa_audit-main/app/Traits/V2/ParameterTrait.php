<?php

namespace App\Traits\V2;

use App\Models\V2\Catalogs\Form;

trait ParameterTrait
{
    public function getParametersByFormId($form_id)
    {
        $form = Form::with([
            'matter:id_matter,matter',
            'aspect:id_aspect,aspect'
        ])
        ->select('id','matter_id','aspect_id','name')
        ->findOrFail($form_id);
        return $form;
    }
}