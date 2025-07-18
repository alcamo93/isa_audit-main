<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V2\Catalogs\ApplicationType;
use App\Traits\V2\ResponseApiTrait;

class ApplicationTypeController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = ApplicationType::included()->filter()->getMainGroup()->getOrPaginate();
    return $this->successResponse($data);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function sourceSpecific()
  {
    $data = ApplicationType::included()->filter()->getSpecificGroup()->getOrPaginate();
    return $this->successResponse($data);
  }
}
