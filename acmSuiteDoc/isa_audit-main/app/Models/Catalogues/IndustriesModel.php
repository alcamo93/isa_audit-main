<?php

namespace App\Models\Catalogues;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class IndustriesModel extends Model
{
    protected $table = 'c_industries';
    protected $primaryKey = 'id_industry';
    /**
     * Return all industries
    */
    public function scopeGetIndustries($query){
        $query->select('id_industry', 'industry')
            ->orderBy('industry', 'ASC');
        $data = $query->get()->toArray();
        return $data;
	}
    /**
     * Set industry from add costumer function
     */
    public function scopeSetIndustry($query, $industry){
        $response = StatusConstants::ERROR;
        $query
            ->select('id_industry')
            ->where('industry', $industry);
        $data = $query->first();
        if($data) $response = $data->id_industry;
        else {
            $query
                ->insert([
                    'industry' => $industry, 
                    'id_status' => 1
                ]);
            $query
                ->select('id_industry')
                ->where('industry', $industry);
            $data = $query->first();
            $response = $data->id_industry;
        }
        return $response;
    }
    /**
     * Set industry from add catalogs module
     */
    public function scopeSetIndustryCT($query, $industry){
        $response = null;
        $query
            ->select('id_industry')
            ->where('industry', $industry);
        $data = $query->first();
        if($data) $response = StatusConstants::WARNING;
        else {
            if(
                $query
                ->insert([
                    'industry' => $industry, 
                    'id_status' => 1
                ])
            ) $response = StatusConstants::SUCCESS;
        }
        return $response;
    }
    /**
     * Get industries to datatables
     */
    public function scopeGetIndustriesDT($query, $page, $rows, $search, $draw, $order, $filterName){
        $query->select('c_industries.id_industry', 'c_industries.industry');
        if ($filterName != null) {
            $query->where(function($query) use ($filterName){
                $query->where('c_industries.industry','LIKE','%'.$filterName.'%');
            });
        }
        //Order by
        $query->orderBy('industry', 'ASC');
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('c_industries.industry','LIKE','%'.$search['value'].'%');
        });
        
        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = ( sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
     * Update industry from add catalogs module
     */
    public function scopeUpdateIndustryCT($query, $idIndustry, $name){
        $response = StatusConstants::ERROR;
        $query
            ->select('id_industry')
            ->where([
                    ['industry', $name],
                    ['id_industry','<>',$idIndustry]
            ]);
        $data = $query->first(); 
        if($data) $response = StatusConstants::WARNING;
        else {
            try{
                \DB::table('c_industries')
                ->where('id_industry', $idIndustry)
                ->update([
                    'industry' => $name
                ]);
                $response = StatusConstants::SUCCESS;
            }
            catch (\Exception $e) {
                $response =  StatusConstants::ERROR;
            }
        }
        return $response;
    }
    /**
     * delete industry from add catalogs module
     */
    public function scopeDeleteIndustryCT($query, $idIndustry){
        $response = StatusConstants::ERROR;
        try{
            $query
            ->where('id_industry', $idIndustry)
            ->delete();
             $response = StatusConstants::SUCCESS;
        }
        catch (\Exception $e) {
            if($e->getCode() == '23000') $response =  StatusConstants::WARNING;
            else $response =  StatusConstants::ERROR;
        }
        return $response;
    }
    
}