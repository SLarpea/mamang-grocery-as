<?php

namespace App\Http\Controllers\Api;

use App\Events\ProductEvents;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $productsModel;

    public function __construct()
    {
        $this->productsModel = new Product();
    }
    public function index()
    {
        // ProductEvents::dispatchIf(!cache()->get("products"));

        $searchQuery = array(
           'current' => request('current')
        );

        $data = array(
            'code' => 200,
            'success' => true,
            'data' => $this->productsModel->products($searchQuery)
        );

        return $this->response(200, $data);
    }
}
