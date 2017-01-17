<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 1/12/2017
 * Time: 10:13 AM
 */


namespace App\Api\V1\Controllers\Admin;

use Validator;
use JWTAuth;
use Carbon\Carbon;
use App\Project;
use App\Number;
use App\Http\Requests;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class AdminProjectController extends Controller
{
    use Helpers;

    public function getAll()
    {
        $projects['projects'] = DB::table("projects")
            ->Join('users', 'users.id', '=', 'projects.user_id' )
            ->select('projects.*', 'users.name as username')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        if(!empty($projects['projects'])) {
            return $projects;
        }
        else {
            return response()->json([
                'messages' => 'No data found',
            ], 201);
        }


    }

    public function getByStatus($status)
    {
        $projects['projects'] = DB::table("projects")
            ->Join('users', 'users.id', '=', 'projects.user_id' )
            ->select('projects.*', 'users.name as username')
            ->where('status',$status)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        if(!empty($projects['projects'])) {
            return $projects;
        } else {
            return response()->json([
                'messages' => ' No Data found'], 201);
        }


        return $projects;
    }

    public function getByFinishStatus($status) 
    {
        $projects['projects'] = DB::table("projects")
            ->Join('users', 'users.id', '=', 'projects.user_id' )
            ->select('projects.*', 'users.name as username')
            ->where('is_finish',$status)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();


        return $projects;
    }

    public function getImageById($id)
    {
        $project = Project::find($id);
        return response()->json(
            $project);
    }

    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'user_id' => 'required');

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['error'] = true;
            $response['message'] = "Data form not complete";
            $response['status_code'] = 200;
            return $response;
        }

        //Get Format code number
        $carbon = Carbon::today();
        $year = $carbon->year;
        $q= Number::where('type','proj')
            ->where('year',$year)
            ->first();
        if(!empty($q)) {
            $result = $q->number+1;
            $kd = sprintf("%05s", $result);
            $q->number = $result;
            $q->save();
            $code = 'PROJ'.$year.$kd;
        } else {
            $number = new Number();
            $number->type = 'PROJ';
            $number->year = $year;
            $number->number = 1;
            $number->save();
            $code = 'proj'.$year.'00001';
        }

        $project = new Project;
        $project->code = $code;
        $project->name = $request->get('name');
        $project->user_id = $request->get('user_id');
        $project->description = $request->get('description');
        $project->qty = $request->get('qty');
        $project->total = $request->get('total');
        $project->order_date = $request->get('order_date');
        $project->due_date = $request->get('due_date');
        $project->status = $request->get('status');
        $project->is_finish= 'F';
        try {
            $project->save();
            $response['error'] = false;
            $response['message'] = "Data has been save";
            $response['status_code'] = 200;
            return $response;
        } catch (QueryException $e) {
            $response["error"] = true;
            $response['message'] = "Cannot save data";
            $response['status_code'] = 500;
            return $response;
        }
    }

    public function finishProject(Request $request, $id)
    {
        $project = Project::find($id);
        $project->is_finish = 'T';
        $project->status = 'Finished';
        if ($project->save()) {
            $response["error"] = false;
            $response['message'] = "Project is Finish";
            $response['status_code'] = 200;

            return $response;
        } else {
            $response["error"] = true;
            $response['message'] = "Could not Finish Project";
            $response['status_code'] = 500;

            return $response;
        }

    }

    public function acceptProject($id) {

        $project = Project::find($id);
        $project->status = "Processed";
        if($project->save()) {
            $response["error"] = false;
            $response['message'] = "project Processed";
            $response["status_code"] = 200;

            return $response;
        } else {
            $response["error"] = true;
            $response['message'] = "Could not processed project";
            $response["status_code"] = 500;

            return $response;
        }
    }

    public function show()
    {
        //to do
    }

    public function upload(Request $request, $id)
    {
        $rules = array(
            'image' => 'required');

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['error'] = true;
            $response['message'] = "select Imgae first";
            $response['status_code'] = 200;
            return $response;
        }
        $response['error'] = array();
        $file = $request->get("image");
        $image = base64_decode($file);
        $png_url = "project_" . uniqid() . ".jpg";
        $path = public_path('upload/' . $png_url);
        file_put_contents($path, $image);

        $project = Project::findOrFail($id);
        $project->image_path = $png_url;
        if ($project->save()) {
            $response["error"] = false;
            $response['message'] = "Image Upload Successfully";
            $response['status_code'] = 200;
            return $response;
        } else {
            $response["error"] = true;
            $response['message'] = "Could not upload image";
            $response['status_code'] = 500;
            return $response;
        }
    }

    public function destroy(Request $request, $id)
    {
        $project = Project::findOrFail($id);
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
}
