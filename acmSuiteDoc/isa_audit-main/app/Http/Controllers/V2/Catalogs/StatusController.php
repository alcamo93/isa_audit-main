<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V2\Catalogs\Status;
use App\Traits\V2\ResponseApiTrait;

class StatusController extends Controller
{
  use ResponseApiTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function source()
  {
    $data = Status::included()->filter()->getOrPaginate();
    return $this->successResponse($data);
  }
}
