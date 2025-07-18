<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class AspectsModel extends Model
{
    protected $table = 'c_aspects';
    protected $primaryKey = 'id_aspect';

    public function audits()
    {
        return $this->hasMany('App\Models\Catalogues\AspectsModel', 'id_aspect', 'id_aspect');
    }

    /**
     * Return aspects
     */
    public function scopeGetAspects($query)
    {
        $query->select('id_aspect', 'aspect');
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Return aspects by matter id 
     */
    public function scopeGetAspectsByMatter($query, $idMatter)
    {
        $query->select('id_aspect', 'aspect')
            ->where('id_matter', $idMatter);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get data aspect by id_aspect
     */
    public function scopeGetAspect($query, $idAspect)
    {
        $query->select('id_aspect', 'id_matter', 'aspect')
            ->where('id_aspect', $idAspect);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get aspects for data table
     */
    public function scopeGetAspectsDT($query, $page, $rows, $search, $draw, $order, $filterName, $idMatter)
    {
        $query->select('c_aspects.id_aspect', 'c_aspects.aspect',  'c_aspects.order', 'c_aspects.id_matter');
        $where = [
            ['id_matter', $idMatter]
        ];
        if ($filterName != null) array_push($where, ['c_aspects.aspect', 'LIKE', '%' . $filterName . '%']);
        if ($search['value'] != null) array_push($where, ['c_aspects.aspect', 'LIKE', '%' . $search['value'] . '%']);
        $query->where($where);
        //Order by
        $query->orderBy('order', 'ASC');

        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = (sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
     * Set Aspect from add catalogs module
     */
    public function scopeSetAspect($query, $aspect, $order, $idMatter)
    {
        $response = null;
        $query
            ->select('id_aspect')
            ->where([
                ['aspect', $aspect],
                ['id_matter', $idMatter],
            ]);
        $data = $query->first();
        if ($data) $response = StatusConstants::WARNING;
        else {
            if (
                $query
                ->insert([
                    'aspect' => $aspect,
                    'order' => $order,
                    'id_matter' => $idMatter
                ])
            ) $response = StatusConstants::SUCCESS;
        }
        return $response;
    }
    /**
     * Update aspect from add catalogs module
     */
    public function scopeUpdateAspect($query, $idAspect, $aspect, $order, $idMatter)
    {
        $response = StatusConstants::ERROR;
        $query
            ->select('id_aspect')
            ->where([
                ['aspect', $aspect],
                ['id_matter', $idMatter],
                ['id_aspect', '<>', $idAspect]
            ]);
        $data = $query->first();
        if ($data) $response = StatusConstants::WARNING;
        else {
            try {
                \DB::table('c_aspects')
                    ->where('id_aspect', $idAspect)
                    ->update([
                        'aspect' => $aspect,
                        'order' => $order,
                        'id_matter' => $idMatter
                    ]);
                $response = StatusConstants::SUCCESS;
            } catch (\Exception $e) {
                $response =  StatusConstants::ERROR;
            }
        }
        return $response;
    }
    /**
     * delete aspect from add catalogs module
     */
    public function scopeDeleteAspect($query, $idAspect)
    {
        $response = StatusConstants::ERROR;
        try {
            $query
                ->where('id_aspect', $idAspect)
                ->delete();
            $response = StatusConstants::SUCCESS;
        } catch (\Exception $e) {
            if ($e->getCode() == '23000') $response =  StatusConstants::WARNING;
            else $response =  StatusConstants::ERROR;
        }
        return $response;
    }
    /**
     * Get matters by array aspects
     */
    public function scopeGetMattersByAspects($query, $aspectsArray)
    {
        $query->select('id_matter')
            ->whereIn('id_aspect', $aspectsArray);
        $data = $query->distinct()->get()->toArray();
        return $data;
    }
    /**
     * Get matters employes
     */
    public function scopeGetAspectsEmployDT($query, $page, $rows, $search, $draw, $order, $idContractMatter, $idMatter)
    {
        $query->select(
            'c_aspects.id_aspect',
            'c_aspects.aspect',
            'c_aspects.order',
            'c_aspects.id_matter',
            \DB::raw('(SELECT r_contract_aspects.id_contract_matter FROM r_contract_aspects WHERE r_contract_aspects.id_aspect = c_aspects.id_aspect AND r_contract_aspects.id_contract_matter = ' . $idContractMatter . ') AS id_contract_matter'),
            \DB::raw('(SELECT r_contract_aspects.id_contract_matter FROM r_contract_aspects WHERE r_contract_aspects.id_aspect = c_aspects.id_aspect AND r_contract_aspects.id_contract_matter = ' . $idContractMatter . ') AS contracted')
        )->where('c_aspects.id_matter', $idMatter);
        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = (sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
     * Get aspect by array id_aspect
     */
    public function scopeGetAspectGroup($query, $aspects)
    {
        $query->select('id_aspect', 'aspect')
            ->whereIn('id_aspect', $aspects);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetMatter($query, $idAspect)
    {
        $query->select('id_matter', 'aspect')
            ->where('id_aspect', $idAspect);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetAll($query)
    {
        $query->join('c_matters', 'c_aspects.id_matter', 'c_matters.id_matter')
            ->select('c_aspects.id_matter', 'c_aspects.aspect', 'c_matters.matter')
            ->groupBy('c_aspects.id_matter', 'c_aspects.aspect', 'c_matters.matter');
        $data = $query->get()->toArray();
        dd($data);
        return $data;
    }
    public function scopeGetMatters($query, $idAspect)
    {
        $query->select('id_matter')
            ->distinct()
            ->whereIn('id_aspect', $idAspect);
        $data = $query->get()->toArray();
        return $data;
    }

    public function forms()
    {
        return $this->hasMany('App\Models\V2\Catalogs\Form', 'aspect_id', 'id_aspect');
    }
}
