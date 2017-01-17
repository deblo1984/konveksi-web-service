<?php

namespace App\Api\V1\Controllers;

use Validator;
use JWTAuth;
use App\Project;
use App\Http\Requests;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ProjectController extends Controller
{
    use Helpers;

    public function getAll()
    {
        $projects['projects'] = DB::table("projects")
            ->Join('users', 'users.id', '=', 'projects.user_id')
            ->select('projects.*', 'users.name as username')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        return $projects;
    }

    public function getByStatus($status) {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $projects['projects'] = $currentUser
            ->projects()
            ->where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        return $projects;
    }

    public function store(Request $request) 
    {
        $rules = array (
            'code' => 'required|unique:projects|max:50',
            'name' => 'required',
            'user_id' => 'required');

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $response['error'] = true;
            $response['message'] = "Data form not complete";
            $response['status_code'] = 200;
            return $response;
        }

    	$project = new Project;
    	$project->code = $request->get('code');
    	$project->name = $request->get('name');
        $project->user_id = $request->get('user_id');
    	$project->description = $request->get('description');
        $project->qty = $request->get('qty');
        $project->total = $request->get('total');
        $project->order_date = $request->get('order_date');
        $project->due_date = $request->get('due_date');
        try {
            $project->save();
            $response['error']= false;
            $response['message'] = "Data has been save";
            $response['status_code'] = 200;
            return $response;
        } catch(QueryException $e) {
            $response["error"] = true;
            $response['message'] = "SQL Error";
            $response['status_code'] = 500;
            return $response;
        }
    }

    public function show() {
        //to do
    }

    public function upload(Request $request, $id)
    {
        $response['error'] = array();
        $file = $request->get("image");
        $image = base64_encode($file);
        $png_url = "project_".uniqid().".jpg";
        $path = public_path('upload/' . $png_url);
        file_put_contents($path, $image);

        $project = Project::findOrFail($id);
        $project->image_path = $png_url;
        if($project->save()) {
            $response["error"] = false;
            $response['message'] = "Image Upload Successfully";
            $response['status_code'] = 200;
            return $response;;
            return $response;
        }
        else
        {
            $response["error"] = true;
            $response['message'] = "Could not upload image";
            $response['status_code'] = 500;
            return $response;
        }
    }

    public function destroy(Request $request, $id)
    {
        $project =Project::findOrFail($id);
        $project->delete();
        if($project != FALSE) {
            $response["error"] = FALSE;
            echo json_encode($response);
        }
        else {
            $response["error"] = TRUE;
            $response["error_msg"] = "Failed to delete project";
            echo json_encode($response);
        }
    }

    private function currentUser() {
        return JWTAuth::parseToken()->authenticate();
    }
}
