<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 12/01/2017
 * Time: 19:16
 */
namespace App\Api\V1\Controllers\Admin;

use Config;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminUserController extends Controller
{
    use Helpers;

    public function register(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $rules = array (
            'name' => 'required|max:50',
            'email' => 'required|unique:users',
            'password' => 'required');

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $response['error'] = true;
            $response['messages'] = "Data form not complete";
            $response['status_code'] = 500;
            return $response;
        }

        $user = new User($request->all());
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = $request->get('password');
        $user->address = $request->get('address');
        $user->city = $request->get('city');
        $user->phone = $request->get('phone');
        $user->role = 'member';
        if(!$user->save()) {
            return response()->json([
                'error' => true,
                'messages' => 'failed create user',
            ], 500);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'error' => false,
            'messages' => 'created',
            'token' => $token
        ], 201);
    }

    public function update(Request $request, JWTAuth $JWTAuth, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->address = $request->get('address');
        $user->city = $request->get('city');
        $user->phone = $request->get('phone');

        if(!$user->save()) {
            return response()->json([
                'error' => true,
                'messages' => 'failed update user',
            ], 500);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'error' => false,
            'messages' => 'updated',
            'token' => $token
        ], 201);
    }

    public function upload(Request $request, $id)
    {
        $response['error'] = array();
        $file = $request->get("image");
        $image = base64_decode($file);
        $png_url = "user_".uniqid().".jpg";
        $path = public_path('upload/' . $png_url);
        file_put_contents($path, $image);

        $user = User::findOrFail($id);
        $user->image_path = $png_url;
        if($user->save()) {
            $response["error"] = false;
            $response['message'] = "profile picture set";
            $response['status_code'] = 200;
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
        $user = User::findOrFail($id);
        $user->delete();
        if($user != FALSE) {
            $response["error"] = FALSE;
            $response['message'] = 'User deleted';
            echo json_encode($response);
        }
        else {
            $response["error"] = TRUE;
            $response["message"] = "Failed to delete project";
            echo json_encode($response);
        }
    }

    public function show() {
        $users['customers'] = DB::table('users')
            ->select('id', 'name', 'email', 'address', 'city', 'country', 'phone', 'created_at', 'image_path')
            ->where('role','member')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray() ;

        return $users;
    }

    public function getById($id)
    {
        $users['users'] = User::findOrFail($id);

        return $users;

    }

    public function userDropDown() {
        $users['customers'] = DB::table('users')
            ->select('id', 'name')
            ->where('role','member')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray() ;

        return $users;
    }

    private function currentUser() {
        return JWTAuth::parseToken()->authenticate();
    }
}
