<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 13/01/2017
 * Time: 23:20
 */

namespace App\Api\V1\Controllers\Admin;

use App\Bulletin;
use Config;
use Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;

class BulletinController extends Controller
{
    use Helpers;

    public function store(Request $request)
    {
        $rules = array (
            'subject' => 'required',
            'content' => 'required',
            'image' => 'required');

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $response['error'] = true;
            $response['messages'] = "Data form not complete";
            $response['status_code'] = 500;
            return $response;
        }
        $user = Auth::User();
        $bulletin = new Bulletin($request->all());
        $bulletin->subject = $request->get('subject');
        $bulletin->content = $request->get('content');
        $bulletin->user_id = $user->id;
        $file = $request->get("image");
        $image = base64_decode($file);
        $png_url = "bulletin_" . uniqid() . ".jpg";
        $path = public_path('upload/' . $png_url);
        $bulletin->image_path = $png_url;
        if(!$bulletin->save()) {
            return response()->json([
                'error' => true,
                'messages' => 'failed create bulletin',
            ], 500);
        } else {
            file_put_contents($path, $image);
            return response()->json([
                'error' => false,
                'messages' => 'Data has been save',
            ], 201);
        }
    }

    public function update(Request $request,$id)
    {
        $rules = array (
            'subject' => 'required',
            'content' => 'required');

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $response['error'] = true;
            $response['messages'] = "Data form not complete";
            $response['status_code'] = 500;
            return $response;
        }
        $bulletin = Bulletin::find($id);

        $bulletin->subject = $request->get('subject');
        $bulletin->content = $request->get('content');
        $file = $request->get("image");
        if(!empty($file)) {
            $image = base64_decode($file);
            $png_url = "bulletin_" . uniqid() . ".jpg";
            $path = public_path('upload/' . $png_url);
            $bulletin->image_path = $png_url;
            file_put_contents($path, $image);
        }
        if(!$bulletin->save()) {
            return response()->json([
                'error' => true,
                'messages' => 'failed updating data',
            ], 500);
        }
        else {
            return response()->json([
                'error' => false,
                'messages' => 'updated',
            ], 201);
        }
    }

    public function destroy($id)
    {
        $bulletin = Bulletin::find($id);
        $bulletin->delete();
        if($bulletin != FALSE) {
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
        $bulletin['bulletins'] = DB::table('bulletins')
            ->Join('users', 'users.id', '=', 'bulletins.user_id' )
            ->select('bulletins.*', 'users.name as username')
            ->orderBy('bulletins.created_at', 'DESC')
            ->get()
            ->toArray() ;

        return $bulletin;
    }

    public function getById($id)
    {
        $response = array();

        $bulletin = Bulletin::findOrFail($id);

        $response['id'] = $bulletin->id;
        $response['subject'] = $bulletin->subject;
        $response['content']= $bulletin->content;
        $response['image_path'] = $bulletin->image_path;
        $response['user_id'] = $bulletin->user_id;

        return json_encode($response);

    }

    private function currentUser() {
        return JWTAuth::parseToken()->authenticate();
    }
}
