<?php
/**
 * Created by PhpStorm.
 * User: Bintang
 * Date: 1/12/2017
 * Time: 8:22 AM
 */
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

class MainController extends Controller
{
    use Helpers;

    public function index()
    {
        $response['error'] = false;
        $response['message'] = "Token Accepted";
        $response['status_code'] = 200;
        return $response;
    }
}
