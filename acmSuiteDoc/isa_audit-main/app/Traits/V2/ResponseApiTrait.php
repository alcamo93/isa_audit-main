<?php

namespace App\Traits\V2;

use Illuminate\Http\Response;

trait ResponseApiTrait
{
	/**
	 * Success response method.
	 * @param object|array $data
	 * @param int $code
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function successResponse($data, $code = Response::HTTP_OK, $messages = [], $info = null)
	{
		if (!empty($messages)) {
			$messages = is_array($messages) ? $messages : [$messages];
		} else {
			$messages = 'Registro exitoso';
			// $messages = ($code == Response::HTTP_CREATED) ? 'Ok' : [Response::$statusTexts[$code]];
		}

		$nodes = (gettype($data) == 'array') ? $data : $data->toArray();
		if (isset($nodes['data'])) {
			$response = $nodes;
		} else {
			$response['data'] = $nodes;
		}

		$response['success'] = true;
		$response['message'] = $messages;
		$response['code'] = $code;
		
		if ( !is_null($info) ) {
			$response['info'] = $info;
		}

		return response()->json($response, $code);
	}

	/**
	 * return error response.
	 *
	 * @param string $messages
	 * @param int $code
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function errorResponse($messages = [], $code = Response::HTTP_BAD_REQUEST, $extras = [])
	{
		if (!empty($messages)) {
			$messages = is_array($messages) ? $messages : [$messages];
		}
		$response['success'] = false;
		$response['message'] = $messages;
		$response['code'] = $code;

		if (!empty($extras)) {
			$extras = is_array($extras) ? $extras : [$extras];
			$response['extras'] = $extras;
		}
		return response()->json($response, $code);
	}

	/**
	 * confirmation response method.
	 * @param string $title
	 * @param string $message
	 * @param int $code
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function confirmationResponse($title, $message, $code = Response::HTTP_OK)
	{
		$response['success'] = false;
		$response['confirmation_required'] = true;
		$response['code'] = $code;
		$response['title'] = $title;
		$response['question'] = $message;

		return response()->json($response, $code);
	}
}