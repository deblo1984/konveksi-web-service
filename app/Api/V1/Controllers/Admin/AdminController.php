<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 1/12/2017
 * Time: 6:58 AM
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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use Helpers;

    public function signUp(LoginRequest $request, JWTAuth $JWTAuth)
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
        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = $request->get('password');
        $user->role = 'admin';
        if (!$user->save()) {
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

    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $JWTAuth->attempt($credentials);

            if(!$token) {
                return response()->json([
                    'error' => false,
                    'messages' => 'access denied',
                    'token' => $token
                ], 201);
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        return response()
            ->json([
                'error' => false,
                'status' => 'ok',
                'token' => $token
            ]);
    }

    public function update(Request $request, JWTAuth $JWTAuth, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if (!$user->save()) {
            throw new HttpException(500);
        }

        if (!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'status' => 'ok',
            'token' => $token
        ], 201);
    }

    public function upload(Request $request, $id)
    {
        $response['error'] = array();
        $file = $request->get("image");
        $image = base64_decode($file);
        $png_url = "user_admin_" . uniqid() . ".jpg";
        $path = public_path('upload/' . $png_url);
        file_put_contents($path, $image);

        $user = User::findOrFail($id);
        $user->image_path = $png_url;
        if ($user->save()) {
            $response["error"] = false;
            $response['message'] = "profile picture set";
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
        $user = User::findOrFail($id);
        $user->delete();
        if ($user != FALSE) {
            $response["error"] = FALSE;
            $response['message'] = 'User deleted';
            echo json_encode($response);
        } else {
            $response["error"] = TRUE;
            $response["message"] = "Failed to delete project";
            echo json_encode($response);
        }
    }

    public function show() {
        $users['users'] = DB::table('users')
            ->select('id', 'name as username', 'email', 'address', 'city', 'country', 'phone', 'image_path', 'created_at')
            ->where('role','admin')
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

    private function currentUser()
    {
        return JWTAuth::parseToken()->authenticate();
    }
}