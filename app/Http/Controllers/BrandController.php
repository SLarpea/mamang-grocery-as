<?php

namespace App\Http\Controllers;

use App\Events\BrandEvents;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Imagick;

class BrandController extends Controller
{
    private $imagick;
    private $brandModel;

    public function __construct()
    {
        $this->imagick = new Imagick();
        $this->brandModel = new Brand();
    }

    public function index()
    {
        BrandEvents::dispatchIf(!cache()->has("brands"));

        return Inertia::render("Admin/Brands", [
            'brands' => cache()->get('brands'),
            'message' => 'success'
        ]);
    }

    public function store(Request $request)
    {
        $this->createBrandLogosDirectory();

        if ($request->has('file')) {
            $filename = $request->file('file')->getClientOriginalName();

            $data = $request->except(['file', 'id', 'img_path']);
            $this->saveFileToStorage($request->file('file'));

            $data['img_path'] = 'brands/m/' . $filename;
            $this->brandModel->create($data);
        }

        return redirect()->route('brands.index');
    }

    public function update(Request $request)
    {
        if ($request->has('id')) {
            $data = $request->except(['file', 'id']);

            if (!empty($request->file('file'))) {
                $filename = $request->file('file')->getClientOriginalName();
                $this->removeCurrImgFile($request->img_path);
                $this->saveFileToStorage($request->file('file'));
                $data['img_path'] = 'brands/m/' . $filename;
            }

            $this->brandModel->edit($data, $request->id);
        }

        return redirect()->route('brands.index');
    }

    public function remove(Request $request)
    {
        if ($request->has('id')) {
            $this->removeCurrImgFile($request->img_path);
            $this->brandModel->remove($request->id);
        }

        return redirect()->route('brands.index');
    }

    private function saveFileToStorage($file)
    {
        $image = $this->imagick;

        $image->readImage($file->getRealPath());

        // save original file
        $image->writeImage(storage_path('app/brands') . '/desk/' . $file->getClientOriginalName());

        // create thumbnail image
        $image->resizeImage(250, 250, Imagick::FILTER_CATROM, 1);
        $image->setCompressionQuality(60);
        $image->writeImage(storage_path('app/brands') . '/m/' . $file->getClientOriginalName());

        $image->clear();
        $image->destroy();
    }

    private function removeCurrImgFile($path)
    {
        $directories = ["desk", "m"];
        $filename = explode("/", $path);
        $filename = $filename[count($filename) - 1];

        foreach ($directories as $directory) {
            if (Storage::disk("brands")->exists('/' . $directory . '/' . $filename)) Storage::disk("brands")->delete('/' . $directory . '/' . $filename);
        }
    }

    private function createBrandLogosDirectory()
    {
        if (!Storage::disk('brands')->exists('/desk')) Storage::disk('brands')->makeDirectory('/desk');
        if (!Storage::disk('brands')->exists('/m')) Storage::disk('brands')->makeDirectory('/m');
    }
}
