<?php

namespace App\Http\Controllers;

use App\Events\CategoryEvents;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Imagick;

class CategoryController extends Controller
{
    private $imagick;
    private $categoryModel;

    public function __construct()
    {
        $this->imagick = new Imagick();
        $this->categoryModel = new Category();
    }
    
    public function index()
    {
        CategoryEvents::dispatchIf(!cache()->has("categories"));
        return Inertia::render("Admin/Categories", [
            "categories"=> cache()->get("categories"),
            "message" => "success"
        ]);
    }

    public function store(Request $request)
    {
        $this->createCategoryLogosDirectory();

        if ($request->has("file")) {
            // dd($request->all());
            $filename = $request->file('file')->getClientOriginalName();

            $data = $request->except(['file', 'id', 'logo_path']);
            $this->saveFileToStorage($request->file('file'));

            $data['logo_path'] = 'categories/m/' . $filename;
            $this->categoryModel->create($data);
        }

        return redirect()->route('categories.index');
    }

    public function update(Request $request)
    {
        if ($request->has('id')) {
            $data = $request->except(['file', 'id']);

            if (!empty($request->file('file'))) {
                $filename = $request->file('file')->getClientOriginalName();
                $this->removeCurrImgFile($request->logo_path);
                $this->saveFileToStorage($request->file('file'));
                $data['logo_path'] = 'categories/m/' . $filename;
            }

            $this->categoryModel->edit($data, $request->id);
        }

        return redirect()->route('categories.index');
    }

    public function remove(Request $request)
    {
        if ($request->has('id')) {
            $this->removeCurrImgFile($request->logo_path);
            $this->categoryModel->remove($request->id);
        }

        return redirect()->route('categories.index');
    }

    public function editStatus(Request $request)
    {
        if ($request->has('id')) {
            $this->categoryModel->updateStatus($request->all());
        }

        return redirect()->route('categories.index');
    }

    private function saveFileToStorage($file)
    {
        $image = $this->imagick;

        $image->readImage($file->getRealPath());

        // save original file
        $image->writeImage(storage_path('app/categories') . '/desk/' . $file->getClientOriginalName());

        // create thumbnail image
        $image->resizeImage(250, 250, Imagick::FILTER_CATROM, 1);
        $image->setCompressionQuality(60);
        $image->writeImage(storage_path('app/categories') . '/m/' . $file->getClientOriginalName());

        $image->clear();
        $image->destroy();
    }

    private function removeCurrImgFile($path)
    {
        $directories = ["desk", "m"];
        $filename = explode("/", $path);
        $filename = $filename[count($filename) - 1];

        foreach ($directories as $directory) {
            if (Storage::disk("categories")->exists('/' . $directory . '/' . $filename)) Storage::disk("categories")->delete('/' . $directory . '/' . $filename);
        }
    }

    private function createCategoryLogosDirectory()
    {
        if (!Storage::disk('local')->exists('/categories')) Storage::disk('local')->makeDirectory('/categories');
        if (!Storage::disk('categories')->exists('/desk')) Storage::disk('categories')->makeDirectory('/desk');
        if (!Storage::disk('categories')->exists('/m')) Storage::disk('categories')->makeDirectory('/m');
    }
}
