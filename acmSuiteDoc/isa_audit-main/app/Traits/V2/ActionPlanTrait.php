<?php

namespace App\Traits\V2;

use App\Models\V2\Audit\ActionPlanRegister;
use App\Models\V2\Audit\ActionPlan;
use App\Models\V2\Catalogs\Status;
use Carbon\Carbon;

trait ActionPlanTrait
{
   /**
   * Filter to get only the usable requirements 
   * in the main Action Plan table 
   * or in the Expired Action Plan table.
   * @param Builder $query
   * @param int $idActionRegister
   */
  public function excludParents($query, $idActionRegister)
  {
    $exclud = ActionPlan::with('requirement')
      ->whereHas('requirement', fn($query) => $query->where('has_subrequirement', 1))
      ->where('id_action_register', $idActionRegister)
      ->whereNull('id_subrequirement')
      ->pluck('id_action_plan')->toArray();
    
    $query->whereNotIn('id_action_plan', $exclud)->where('id_action_register', $idActionRegister);
  }

  /**
   * Get Action Plan statuses by adding a count 
   * of Only Usable Requirements to the action plan
   * 
   * @param int $idActionRegister
   * @return Illuminate\Database\Eloquent\Collection 
   */
  public function getTotalsByStatus($idActionRegister) 
  {
    $relationships = [ 'action_plans' => fn($query) => $this->excludParents($query, $idActionRegister) ];
    $action = ActionPlanRegister::with($relationships)->findOrFail($idActionRegister);
    $status = Status::where('group', 7)->get();
    $data = $status->map(function ($item) use ($action) {
      $item->count = $action->action_plans->where('id_status', $item->id_status)->count();
      return $item;
    });
    return $data;
  }
}