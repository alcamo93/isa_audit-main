<?php

namespace App\Http\Controllers\V2\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\V2\ResponseApiTrait;
use App\Models\V2\Audit\AplicabilityRegister;

class RiskRegisterController extends Controller
{
  use ResponseApiTrait;

  /**
   * Redirect to view.
   */
  public function view(Request $request, $aplicabilityRegisterId) 
  {
    $id = base64_decode($aplicabilityRegisterId);
    $data = AplicabilityRegister::with('process')->findOrFail($id);
    return view('v2.risk.main', ['data' => $data]);
  }

}