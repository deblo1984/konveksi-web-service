<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 11/01/2017
 * Time: 21:18
 */

namespace App\Api\V1\Controllers;

use Config;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    use Helpers;

    public function register(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $user = new User($request->all());
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = $request->get('password');
        $user->address = $request->get('address');
        $user->city = $request->get('city');
        $user->phone = $request->get('phone');
        $user->role = 'member';
        if(!$user->save()) {
            throw new HttpException(500);
        }

        if(!Config::get('boilerplate.sign_up.release_token')) {
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

    public function update(Request $request, JWTAuth $JWTAuth, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->address = $request->get('address');
        $user->city = $request->get('city');
        $user->phone = $request->get('phone');

        if(!$user->save()) {
            throw new HttpException(500);
        }

        if(!Config::get('boilerplate.sign_up.release_token')) {
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
        $image = base64_encode($file);
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

    private function currentUser() {
        return JWTAuth::parseToken()->authenticate();
    }

    public function getAngka() {
        /*$hasil = Number::getNumber('proj', 2016);
        if(!empty($hasil)) {
            $hasil->number+1;
            $hasil->save();
        }
        else {
            $number = new Number();
            $number->type = 'proj';
            $number->year = 2016;
            $number->number = 1;
            $number->save();
            echo "proj201600001";
        }*/
        /*$carbon = Carbon::today();
        $year = $carbon->year;
        $q= Number::where('type','proj')
            ->where('year',$year)
            ->first();
        if(!empty($q)) {
            $q->number = $q->number+1;
            $q->save();
        } else {
            $number = new Number();
            $number->type = 'proj';
            $number->year = $year;
            $number->number = 1;
            $number->save();
        }*/

    }
}
