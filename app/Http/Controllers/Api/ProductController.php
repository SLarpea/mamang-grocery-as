<?php

namespace App\Http\Controllers\Api;

use App\Events\ProductEvents;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        ProductEvents::dispatchIf(!cache()->get("products"));

        $data = array(
            'code' => 200,
            'success' => true,
            'data' => cache()->get("products")
        );

        return $this->response(200, $data);
    }
}
