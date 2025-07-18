<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V2\Catalogs\Country;
use App\Traits\V2\ResponseApiTrait;

class CountryController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Country::included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }
}
