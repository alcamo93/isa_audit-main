<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V2\Catalogs\RequirementType;
use App\Traits\V2\ResponseApiTrait;

class RequirementTypeController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = RequirementType::included()->filter()->getMainGroup()->getOrPaginate();
    return $this->successResponse($data);
  }
}
