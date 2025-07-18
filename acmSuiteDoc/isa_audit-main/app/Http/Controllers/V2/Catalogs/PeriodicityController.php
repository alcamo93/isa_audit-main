<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V2\Catalogs\Periodicity;
use App\Traits\V2\ResponseApiTrait;

class PeriodicityController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Periodicity::included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }
}
