<?php

namespace App\Http\Controllers\Details;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\StatusModel;
use App\Classes\StatusConstants;
use App\Models\Catalogues\RequirementsLegalBasiesModel;
use App\Models\Catalogues\SubrequirementsLegalBasiesModel;

class DetailsController extends Controller
{
    /**
     * Get basis by id_audit 
     */
    public function getBasiesAudit($dataRequest) {
        $json = json_decode(base64_decode($dataRequest), true);
        if ($json['type'] == 'req') {
            $basis = RequirementsLegalBasiesModel::GetBasiesByRequirement($json['id']);
        }
        else {
            $basis = SubrequirementsLegalBasiesModel::GetBasiesBySubrequirement($json['id']);
        }
        
        return view('details.basis', [
            'title' => $json['name'],
            'basis' => $basis
        ]);
    }
}