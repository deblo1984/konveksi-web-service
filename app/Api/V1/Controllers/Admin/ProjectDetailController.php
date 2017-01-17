<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 12/01/2017
 * Time: 20:52
 */

namespace App\Api\V1\Controllers\Admin;

use App\ProjectDetail;
use Validator;
use JWTAuth;
use App\Project;
use App\Http\Requests;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ProjectDetailController extends Controller
{
    use Helpers;

    public function getByProject($id)
    {
        $projects['project_details'] = DB::table("project_details")
            ->where('project_id',$id)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->toArray();

        if(!empty($projects['project_details']))
        {
            return $projects;
        }
        else
        {
            return response()->json([
                'messages' => 'No detail project',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $rules = array(
            'project_id' => 'required',
            'item_name' => 'required',
            'image' => 'required',
            'remarks' => 'required');

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['error'] = true;
            $response['message'] = "Data form not complete";
            $response['status_code'] = 200;
            return $response;
        }

        $project = new ProjectDetail();
        $project->project_id = $request->get('project_id');
        $project->item_id = $request->get('item_id');
        $project->item_name = $request->get('item_name');
        $project->qty = $request->get('qty');
        $project->price = $request->get('price');
        $project->total = $request->get('total');
        $project->remarks = $request->get('remarks');
        //image upload
        $file = $request->get("image");
        $image = base64_decode($file);
        $png_url = "project_details_" . uniqid() . ".jpg";
        $path = public_path('upload/' . $png_url);
        //===========
        $project->image_path = $png_url;

        try {
            if($project->save()) {
                file_put_contents($path, $image);
                $response['error'] = false;
                $response['message'] = "Data has been save";
                $response['status_code'] = 200;
                return $response;
            }
        } catch (QueryException $e) {
            $response["error"] = true;
            $response['message'] = "SQL Error";
            $response['status_code'] = 500;
            return $response;
        }
    }

    public function destroy(Request $request, $id)
    {
        $project = ProjectDetail::findOrFail($id);
        $project->delete();
        if ($project != FALSE) {
            $response["error"] = FALSE;
            echo json_encode($response);
        } else {
            $response["error"] = TRUE;
            $response["error_msg"] = "Failed to delete project";
            echo json_encode($response);
        }
    }

    public function getById($id)
    {
        $project = ProjectDetail::find($id);
        if(!empty($project))
        {
            return $project;
        }
        else
        {
            return response()->json([
                'messages' => 'no data found',
            ], 500);
        }
    }
}
