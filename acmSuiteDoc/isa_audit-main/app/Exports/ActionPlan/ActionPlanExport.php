<?php

namespace App\Exports\ActionPlan;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\ActionPlan\Sheets\SheetGeneral;
use App\Exports\ActionPlan\Sheets\SheetActionPerAspects;
use App\Exports\ActionPlan\Sheets\SheetActionMonthly;
use App\Exports\ActionPlan\Sheets\SheetTaskPerAspects;
use App\Exports\ActionPlan\Sheets\SheetListTasks;

class ActionPlanExport implements WithMultipleSheets
{
	use Exportable;

	protected $sheetGeneral = null;
	protected $sheetActionPerAspects = null;
	protected $sheetActionMonthly = null;
	protected $sheetTaskPerAspects = null;
	protected $sheetListTasks = null;

	public function __construct(array $data)
	{
		$this->sheetGeneral = $data['sheet_general'];
		$this->sheetActionPerAspects = $data['sheet_action_per_aspects'];
		$this->sheetActionMonthly = $data['sheet_action_monthly'];
		$this->sheetTaskPerAspects = $data['sheet_task_per_aspects'];
		$this->sheetListTasks = $data['sheet_list_tasks'];
	}

	public function sheets(): array
	{
		$sheets = [];
    
		$sheets[] = new SheetGeneral($this->sheetGeneral);
		$sheets[] = new SheetActionPerAspects($this->sheetActionPerAspects);
		$sheets[] = new SheetActionMonthly($this->sheetActionMonthly);
		$sheets[] = new SheetTaskPerAspects($this->sheetTaskPerAspects);
		$sheets[] = new SheetListTasks($this->sheetListTasks);

		return $sheets;
	}
}
