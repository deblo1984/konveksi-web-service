<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 12/01/2017
 * Time: 20:00
 */

namespace App\Api\V1\Controllers\Admin;

use App\WorkType;
use Config;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WorkTypeController extends Controller
{
    use Helpers;

    public function store(Request $request)
    {
        $rules = array (
            'code' => 'required',
            'work_name' => 'required|max:100',
            'description' => 'required');

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $response['error'] = true;
            $response['messages'] = "Data form not complete";
            $response['status_code'] = 500;
            return $response;
        }

        $work = new WorkType($request->all());
        $work->code = $request->get('code');
        $work->work_name = $request->get('work_name');
        $work->description = $request->get('description');
        if(!$work->save()) {
            return response()->json([
                'error' => true,
                'messages' => 'failed create work type',
            ], 500);
        }

        return response()->json([
            'error' => false,
            'messages' => 'Data has been save',
        ], 201);
    }

    public function update(Request $request,$id)
    {
        $work = WorkType::findOrFail($id);

        $work->code = $request->get('code');
        $work->work_name = $request->get('work_name');
        $work->description = $request->get('description');

        if(!$work->save()) {
            return response()->json([
                'error' => true,
                'messages' => 'failed updating data',
            ], 500);
        }

        return response()->json([
            'error' => false,
            'messages' => 'updated',
        ], 201);
    }

    public function destroy($id)
    {
        $work = WorkType::findOrFail($id);
        $work->delete();
        if($work != FALSE) {
            $response["error"] = FALSE;
            $response['message'] = 'work type deleted';
            echo json_encode($response);
        }
        else {
            $response["error"] = TRUE;
            $response["message"] = "Failed to delete work type";
            echo json_encode($response);
        }
    }

    public function show() {
        $works['work_types'] = DB::table('work_types')
            ->orderBy('id', 'ASC')
            ->get()
            ->toArray() ;

        return $works;
    }

    public function getById($id)
    {
        $response = array();
        
        $work = WorkType::findOrFail($id);

        $response['code'] = $work->code;
        $response['work_name']= $work->work_name;
        $response['description'] = $work->description;

        return json_encode($response);

    }

    private function currentUser() {
        return JWTAuth::parseToken()->authenticate();
    }
}
