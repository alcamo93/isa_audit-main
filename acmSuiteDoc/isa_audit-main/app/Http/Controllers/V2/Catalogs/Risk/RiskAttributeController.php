<?php

namespace App\Http\Controllers\V2\Catalogs\Risk;

use App\Http\Controllers\Controller;
use App\Models\V2\Catalogs\RiskAttribute;
use App\Traits\V2\ResponseApiTrait;

class RiskAttributeController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    try {
      $data = RiskAttribute::included()->filter()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
