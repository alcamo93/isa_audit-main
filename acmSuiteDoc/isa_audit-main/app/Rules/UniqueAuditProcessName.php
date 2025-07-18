<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\V2\Audit\ProcessAudit;

class UniqueAuditProcessName implements Rule
{
	protected $id_corporate;
	protected $id_audit_process;
	protected $value;
	protected $is_renewal;

	public function __construct($id_corporate, $id_audit_process, $is_renewal)
	{
		$this->id_corporate = $id_corporate;
		$this->id_audit_process = $id_audit_process;
		$this->is_renewal = $is_renewal;
	}

	public function passes($attribute, $value)
	{
		if ($this->is_renewal) {
			$process = ProcessAudit::find($this->id_audit_process);
			$query = ProcessAudit::where('audit_processes', $value)
				->where('id_corporate', $process->id_corporate);
			return !$query->exists();
		}
		
		$this->value = $value;
		$query = ProcessAudit::where('audit_processes', $value)
			->where('id_corporate', $this->id_corporate);

		// If it is an update, we ignore the current record
		if ($this->id_audit_process) {
			$query->where('id_audit_processes', '!=', $this->id_audit_process);
		}

		return !$query->exists();
	}

	public function message()
	{
		return "Ya existe una evaluación con el nombre '{$this->value}' para esta Planta, define otro nombre por favor.";
	}
}
