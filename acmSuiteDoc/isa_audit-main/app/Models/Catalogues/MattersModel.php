<?php

namespace App\Models\Catalogues;

use App\Classes\StatusConstants;
use Illuminate\Database\Eloquent\Model;

class MattersModel extends Model
{
   protected $table = 'c_matters';
   protected $primaryKey = 'id_matter';
   /**
    * Return matters
    */
   public function scopeGetMatters($query){
          $query->select('id_matter', 'matter');
          $data = $query->get()->toArray();
          return $data;
   }
   /**
    * Get data matter
    */
    public function scopeGetMatter($query, $idMatter){
          $query->select('id_matter', 'matter')
                 ->where('id_matter', $idMatter);
          $data = $query->get()->toArray();
          return $data;
   }
   /**
   * Get industries to datatables
   */
   public function scopeGetMattersDT($query, $page, $rows, $search, $draw, $order, $filterName){
          $query->select('c_matters.id_matter', 'c_matters.matter', 'c_matters.description');
          if ($filterName != null) {
                 $query->where(function($query) use ($filterName){
                        $query->where('c_matters.matter','LIKE','%'.$filterName.'%');
                 });
          }
          //Order by
          $query->orderBy('id_matter', 'ASC');

          /*Search*/
          $query->where(function($query) use ($search){
                 $query->where('c_matters.matter','LIKE','%'.$search['value'].'%');
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
   * Set matter from add catalogs module
   */
   public function scopeSetMatter($query, $matter, $desc){
          $response = StatusConstants::ERROR;
          $query
          ->select('id_matter')
          ->where('matter', $matter);
          $data = $query->first();
          if($data) $response = StatusConstants::WARNING;
          else {
                 if(
                        $query
                        ->insert([
                        'matter' => $matter, 
                        'description' => $desc
                        ])
                 ) $response = StatusConstants::SUCCESS;
          }
          return $response;
   }
   /**
   * Update matter from add catalogs module
   */
   public function scopeUpdateMatter($query, $idMatter, $matter, $desc){
          $response = StatusConstants::ERROR;
          $query
          ->select('id_matter')
          ->where([
                 ['matter', $matter],
                 ['description', $desc],
                 ['id_matter','<>',$idMatter]
          ]);
          $data = $query->first(); 
          if($data) $response = StatusConstants::WARNING;
          else {
                 try{
                        \DB::table('c_matters')
                        ->where('id_matter', $idMatter)
                        ->update([
                               'matter' => $matter,
                               'description' => $desc
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
    * delete matter from add catalogs module
    */
   public function scopeDeleteMatter($query, $idMatter){
   $response = StatusConstants::ERROR;
          try{
                 $query
                 ->where('id_matter', $idMatter)
                 ->delete();
                 $response = StatusConstants::SUCCESS;
          }
          catch (\Exception $e) {
                 if($e->getCode() == '23000') $response =  StatusConstants::WARNING;
                 else $response =  StatusConstants::ERROR;
          }
          return $response;
   }
   /**
    * Get matters by array idMatter
    */
    public function scopeGetMattersByIds($query, $aspectsArray){
          $query->select('id_matter', 'matter')->whereIn('id_matter', $aspectsArray);
          $data = $query->get()->toArray();
          return $data;
    }
   /**
    * Get matters employes
    */
   public function scopeGetMattersEmployDT($query, $page, $rows, $search, $draw, $order, $idAplicabilityRegister){
          $query->select('c_matters.id_matter', 'c_matters.matter', 
                 \DB::raw('(SELECT r_contract_matters.id_contract FROM r_contract_matters WHERE r_contract_matters.id_matter = c_matters.id_matter AND r_contract_matters.id_aplicability_register = '.$idAplicabilityRegister.') AS id_contract'),
                 \DB::raw('(SELECT r_contract_matters.id_contract_matter FROM r_contract_matters WHERE r_contract_matters.id_matter = c_matters.id_matter AND r_contract_matters.id_aplicability_register = '.$idAplicabilityRegister.') AS id_contract_matter'),
                 \DB::raw('(SELECT r_contract_matters.id_contract_matter FROM r_contract_matters WHERE r_contract_matters.id_matter = c_matters.id_matter AND r_contract_matters.id_aplicability_register = '.$idAplicabilityRegister.') AS contracted')
          );
          $queryCount = $query->get();
          $result = $query->limit($rows)->offset($page)->get()->toArray();
          $total = $queryCount->count();
          $data['data'] = ( sizeof($result) > 0) ? $result : array();
          $data['recordsTotal'] = $total;
          $data['draw'] = (int) $draw;
          $data['recordsFiltered'] = $total;
          return $data;
   }

    public function forms()
    {
        return $this->hasMany('App\Models\V2\Catalogs\Form', 'matter_id', 'id_matter');
    }
}