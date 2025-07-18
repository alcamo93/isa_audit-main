<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V2\News\News;
use App\Traits\V2\ResponseApiTrait;
use App\Classes\Files\StoreImage;
use App\Models\V2\Admin\Image;
use Illuminate\Support\Facades\Storage;
use App\Classes\Files\ImageDecodeToFile;
use App\Traits\V2\FileTrait;
use Illuminate\Support\Facades\Validator;

class NewController extends Controller
{
  use ResponseApiTrait, FileTrait;
  /**
   * Redirect to view.
   */
  public function view()
  {
    return view('v2.new.main');
  }

  /**
   * Redirect to view new.
   */
  public function newView($idNew)
  {
    $data = ['id_new' => $idNew];
    return view('v2.new.view.main', ['data' => $data]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $data = News::with('topics', 'image')->banner()->included()->customFilters()->getOrPaginate();
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(NewRequest $request)
  {
    try {
      DB::beginTransaction();
      $new = News::create($request->all());
      $new->topics()->sync($request->topics);

      $storeDisk = 'newsBody';
			$decodeImgObject = new ImageDecodeToFile($storeDisk, $new->id);
			$decodeImgObject->decodeImg64ToLink($request->description, 'v2');
			if (!$decodeImgObject->allCreate) {
				DB::rollback();
				return $this->errorResponse('No se ha podido crear los archivos, verifica que las imagenes no esten dañadas');
			}
			$new->update(['description' => $decodeImgObject->richText]);

      $file = $request->file('file');
      $imageableId = $new->id;
      $origin = 'news';

      $store = new StoreImage($file, $imageableId, $origin);
      $image = $store->storeImage();
      if (!$image['success']) {
        return $this->errorResponse($image['message']);
      }

      DB::commit();
      return $this->successResponse($new);
    } catch (\Throwable $th) {
      DB::rollback();
      throw $th;
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    try {
      $data = News::with('topics')->included()->findOrFail($id);
      return $this->successResponse($data);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(NewRequest $request, $id)
  {
    try {
      DB::beginTransaction();
      $new = News::findOrFail($id);
      $new->update($request->all());
      $new->topics()->sync($request->topics);

      $storeDisk = 'newsBody';
			$decodeImgObject = new ImageDecodeToFile($storeDisk, $new->id);
			$decodeImgObject->decodeImg64ToLink($request->description, 'v2');
			if (!$decodeImgObject->allCreate) {
				DB::rollback();
				return $this->errorResponse('No se ha podido crear los archivos, verifica que las imagenes no esten dañadas');
			}
			$new->update(['description' => $decodeImgObject->richText]);

      if (!$request->hasFile('file')) {
        DB::commit();
        return $this->successResponse($new);
      }

      $file = $request->file('file');
      $imageableId = $new->id;
      $origin = 'news';

      $store = new StoreImage($file, $imageableId, $origin);
      $image = $store->storeImage();
      if (!$image['success']) {
        return $this->errorResponse($image['message']);
      }

      DB::commit();
      return $this->successResponse($new);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    try {
      DB::beginTransaction();
      $new = News::findOrFail($id);
      $image = $new->image;
      $disk = $this->getStorageEnviroment($image->store_origin);
      $deleteImage = Storage::disk($disk)->delete($image->directory_path);
      if (!$deleteImage) {
        DB::rollBack();
        return $this->errorResponse('No se pudo eliminar la imagen de banner');
      }
      $image->delete();
      $new->delete();
      DB::commit();
      return $this->successResponse($new);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
	 * Change status(published) the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function status($published, $id) 
	{
    $validator = Validator::make(
        ['published' => $published],
        ['published' => 'required|boolean']
    );

    if ($validator->fails()) {
        return response()->json(['error' => 'El valor de estatus debe ser falso o verdadero.'], 422);
    }

		try {
			$new = News::findOrFail($id);
			$status = $published == 1 ? 1 : 0;
			$new->update(['published' => $status]);
			return $this->successResponse($new);
		} catch(\Throwable $th) {
			throw $th;
		}
	}
}
