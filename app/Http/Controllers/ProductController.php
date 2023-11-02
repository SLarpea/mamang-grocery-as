<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Imagick;

class ProductController extends Controller
{
    private $imagick;
    private $productModel;

    public function __construct()
    {
        $this->imagick = new Imagick();
        $this->productModel = new Product();
    }

    public function index(Request $request)
    {
        return Inertia::render("Admin/Products", [
            'products' => $this->productModel->getAllProducts(),
            'message' => 'success'
        ]);
    }

    public function store(Request $request)
    {
        $this->createProductImagesDirectory();

        if ($request->has('file')) {
            $filename = $request->file('file')->getClientOriginalName();

            $data = $request->except(['file', 'id', 'img_link']);
            $this->saveFileToStorage($request->file('file'));

            $data['img_link'] = 'products/m/' . $filename;
            $this->productModel->create($data);
        }

        return redirect()->route('products.index');
    }

    public function update(Request $request)
    {
        if ($request->has('id')) {
            $data = $request->except(['file', 'id']);

            if (!empty($request->file('file'))) {
                $filename = $request->file('file')->getClientOriginalName();
                $this->removeCurrImgFile($request->img_link);
                $this->saveFileToStorage($request->file('file'));
                $data['img_link'] = 'products/m/' . $filename;
            }

            $this->productModel->edit($data, $request->id);
        }

        return redirect()->route('products.index');
    }

    public function remove(Request $request)
    {
        if ($request->has('id')) {
            $this->removeCurrImgFile($request->img_link);
            $this->productModel->remove($request->id);
        }

        return redirect()->route('products.index');
    }

    private function saveFileToStorage($file)
    {
        $image = $this->imagick;

        $image->readImage($file->getRealPath());

        // save original file
        $image->writeImage(storage_path('app/products') . '/desk/' . $file->getClientOriginalName());

        // create thumbnail image
        $image->resizeImage(250, 250, Imagick::FILTER_CATROM, 1);
        $image->setCompressionQuality(60);
        $image->writeImage(storage_path('app/products') . '/m/' . $file->getClientOriginalName());

        $image->clear();
        $image->destroy();
    }

    private function removeCurrImgFile($path)
    {
        $directories = ["desk", "m"];
        $filename = explode("/", $path);
        $filename = $filename[count($filename) - 1];

        foreach ($directories as $directory) {
            if (Storage::disk("products")->exists('/' . $directory . '/' . $filename)) Storage::disk("products")->delete('/' . $directory . '/' . $filename);
        }
    }

    private function createProductImagesDirectory()
    {
        if (!Storage::disk('local')->exists('/products')) Storage::disk('local')->makeDirectory('/products');
        if (!Storage::disk('products')->exists('/desk')) Storage::disk('products')->makeDirectory('/desk');
        if (!Storage::disk('products')->exists('/m')) Storage::disk('products')->makeDirectory('/m');
    }
}
