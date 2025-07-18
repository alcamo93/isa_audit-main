<?php

namespace App\Http\Controllers\V2\Catalogs;

use App\Http\Controllers\Controller;
use App\Models\V2\Catalogs\Guideline;
use Illuminate\Http\Request;
use App\Models\V2\Catalogs\Topic;
use App\Traits\V2\ResponseApiTrait;

class KnowledgeTopicController extends Controller
{

  use ResponseApiTrait;
  /**
	 * Redirect to view.
	 */
	public function view($idTopic) 
	{
		$data = ['id_topic' => $idTopic];
		return view('v2.knowledge.topic.main', ['data' => $data]);
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
			$data = Topic::findOrFail($id);
			return $this->successResponse($data);
		} catch(\Throwable $th) {
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getGuidelinesByIdTopic($id)
    {
      try {
        $data = Guideline::with('topics')->withIndex()
          ->whereHas('topics', function ($query) use ($id) {
            $query->where('topic_id', $id);
          })
          ->getOrPaginate();

        return $this->successResponse($data);
      } catch (\Throwable $th) {
        throw $th;
      }
    }
}
