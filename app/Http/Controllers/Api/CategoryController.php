<?php

namespace App\Http\Controllers\Api;

use App\Events\CategoryEvents;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        CategoryEvents::dispatchIf(!cache()->get("categories"));

        $data = array(
            'code' => 200,
            'success' => true,
            'data' => cache()->get("categories")
        );

        $this->response(200, $data);
    }
}
