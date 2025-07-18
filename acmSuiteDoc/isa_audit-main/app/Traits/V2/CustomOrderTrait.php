<?php

namespace App\Traits\V2;

use Illuminate\Database\Eloquent\Builder;

trait CustomOrderTrait
{
	/**
	 * global order
	 */
	public function scopeCustomOrder($query)
	{
		$query->with(['requirement.matter', 'requirement.aspect', 'subrequirement'])
			->orderByRaw("(SELECT `order` FROM c_matters WHERE c_matters.id_matter = (SELECT id_matter FROM t_requirements WHERE t_requirements.id_requirement = {$this->table}.id_requirement LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `order` FROM c_aspects WHERE c_aspects.id_aspect = (SELECT id_aspect FROM t_requirements WHERE t_requirements.id_requirement = {$this->table}.id_requirement LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `id_application_type` FROM c_application_types WHERE c_application_types.id_application_type = (SELECT id_application_type FROM t_requirements WHERE t_requirements.id_requirement = {$this->table}.id_requirement LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `id_state` FROM c_states WHERE c_states.id_state = (SELECT id_state FROM t_requirements WHERE t_requirements.id_requirement = {$this->table}.id_requirement LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `id_city` FROM c_cities WHERE c_cities.id_city = (SELECT id_city FROM t_requirements WHERE t_requirements.id_requirement = {$this->table}.id_requirement LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `order` FROM t_requirements WHERE t_requirements.id_requirement = {$this->table}.id_requirement LIMIT 1)")
			->orderByRaw("(SELECT `order` FROM t_subrequirements WHERE t_subrequirements.id_subrequirement = {$this->table}.id_subrequirement LIMIT 1)");
	}

	public function scopeCustomOrderRequirement($query)
	{
		$query->with(['matter', 'aspect'])
			->orderByRaw("(SELECT `order` FROM c_matters WHERE c_matters.id_matter = {$this->table}.id_matter LIMIT 1)")
			->orderByRaw("(SELECT `order` FROM c_aspects WHERE c_aspects.id_aspect = {$this->table}.id_aspect LIMIT 1)")
			->orderByRaw("(SELECT `id_application_type` FROM c_application_types WHERE c_application_types.id_application_type = {$this->table}.id_application_type LIMIT 1)")
			->orderByRaw("(SELECT `id_state` FROM c_states WHERE c_states.id_state = {$this->table}.id_state LIMIT 1)")
			->orderByRaw("(SELECT `id_city` FROM c_cities WHERE c_cities.id_city = {$this->table}.id_city LIMIT 1)")
			->orderBy('order');
	}

	public function scopeCustomOrderQuestion($query)
	{
		$query->with(['matter', 'aspect'])
			->orderByRaw("(SELECT `order` FROM c_matters WHERE c_matters.id_matter = {$this->table}.id_matter LIMIT 1)")
			->orderByRaw("(SELECT `order` FROM c_aspects WHERE c_aspects.id_aspect = {$this->table}.id_aspect LIMIT 1)")
			->orderByRaw("(SELECT `id_question_type` FROM c_question_types WHERE c_question_types.id_question_type = {$this->table}.id_question_type LIMIT 1)")
			->orderByRaw("(SELECT `id_state` FROM c_states WHERE c_states.id_state = {$this->table}.id_state LIMIT 1)")
			->orderByRaw("(SELECT `id_city` FROM c_cities WHERE c_cities.id_city = {$this->table}.id_city LIMIT 1)")
			->orderBy('order');
	}
	
	public function scopeCustomOrderEvaluateQuestion($query)
	{
		$query->with(['question.matter', 'question.aspect'])
			->orderByRaw("(SELECT `order` FROM c_matters WHERE c_matters.id_matter = (SELECT id_matter FROM t_questions WHERE t_questions.id_question = {$this->table}.id_question LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `order` FROM c_aspects WHERE c_aspects.id_aspect = (SELECT id_aspect FROM t_questions WHERE t_questions.id_question = {$this->table}.id_question LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `id_application_type` FROM c_application_types WHERE c_application_types.id_application_type = (SELECT id_application_type FROM t_questions WHERE t_questions.id_question = {$this->table}.id_question LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `id_state` FROM c_states WHERE c_states.id_state = (SELECT id_state FROM t_questions WHERE t_questions.id_question = {$this->table}.id_question LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `id_city` FROM c_cities WHERE c_cities.id_city = (SELECT id_city FROM t_questions WHERE t_questions.id_question = {$this->table}.id_question LIMIT 1) LIMIT 1)")
			->orderByRaw("(SELECT `order` FROM t_questions WHERE t_questions.id_question = {$this->table}.id_question LIMIT 1)");
	}
}
