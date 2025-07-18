<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\V2\Audit\Audit;
use App\Models\V2\Catalogs\Requirement;
use App\Models\V2\Catalogs\Subrequirement;
use App\Traits\V2\RequirementTrait;
use App\Traits\V2\ResponseApiTrait;

class DetailController extends Controller
{
  use ResponseApiTrait, RequirementTrait;

  /**
   * view
   * 
   * @param string $items
   * @param string $encripted 
   * 
   */
  public function view($items, $encripted)
  {
    $options = collect(['articles', 'images']);
    if ( !$options->contains($items) ) return abort(404);

    $data['items'] = $items;
    $data['data'] = $encripted;
    return view('v2.detail.main', ['data' => $data]);
  }

  /**
   * render data for details
   * 
   * @param string $items
   * @param string $encripted 
   * 
   */
  public function render($items, $encripted)
  {
    $options = collect(['articles', 'images']);

    if ( !$options->contains($items) ) {
      return $this->errorResponse('Verifica la información que estas solicitando');
    }

    $desencripted = json_decode(base64_decode($encripted), true);

    if ( $items == 'articles' && ( $desencripted['type'] == 'req' || $desencripted['type'] == 'sub') ) 
    {
      $relationships = ['articles.guideline'];
      $isRequirement = $desencripted['type'] == 'req';
      $record = $isRequirement
        ? Requirement::with($relationships)->find($desencripted['id']) 
        : Subrequirement::with($relationships)->find($desencripted['id']);

      $info['title_page'] = 'Fundamentos Legales para:';
      $info['title_detail'] = $isRequirement ? $record['no_requirement'] : $record['no_subrequirement'];
      $info['render_type'] = 'rich_text';
      $info['details'] = $record['articles']->map(function($item) {
        return [
          'title' => $item['guideline']['guideline'],
          'subtitle' => $item['legal_basis'],
          'description' => $item['legal_quote_env'],
        ];
      })->toArray();
      
      return $this->successResponse($info);
    }

    if ( $items == 'images' && ( $desencripted['type'] == 'audit' ) ) 
    {
      $record = Audit::with(['images'])->find($desencripted['id']);

      $info['title_page'] = 'Imagenes de hallazgos de Auditoría:';
      $info['title_detail'] = $this->getFieldRequirement($record, 'no_requirement');
      $info['render_type'] = 'image';
      $info['details'] = $record['images']->map(function($item) {
        return [
          'title' => $item['original_name'],
          'subtitle' => '',
          'description' => $item['full_path'],
        ];
      })->toArray();
      
      return $this->successResponse($info);
    }

    return $this->errorResponse('Verifica la información que estas solicitando');
  }
}
