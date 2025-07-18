<?php

namespace App\Http\Controllers\Catalogues;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Catalogues\StatusModel;
use App\Models\Catalogues\CountriesModel;
use App\Models\Catalogues\StatesModel;
use App\Models\Catalogues\CitiesModel;

class LocationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Get states for Country
     */
    public function getStates(Request $request, $idCountry){
        $data = StatesModel::getStates($idCountry);
        return $data;
    }
    /**
     * Get cities for state
     */
    public function getCities(Request $request, $idState){
        $data = CitiesModel::getCities($idState);
        return $data;
    }
} 