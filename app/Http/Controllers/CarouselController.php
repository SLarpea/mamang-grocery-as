<?php

namespace App\Http\Controllers;

use App\Events\CarouselEvents;
use App\Models\Carousel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CarouselController extends Controller
{
    private $carouselModel;

    public function __construct()
    {
        $this->carouselModel = new Carousel();
    }

    public function index(Request $request)
    {
        CarouselEvents::dispatchIf(!cache()->has("carousels"));

        return Inertia::render("Admin/Carousels", [
            "carousels" => cache()->get("carousels"),
            "message" => "success"
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has("name") && !empty($request->name)) {
            $data = $request->except('id');
            $this->carouselModel->create($data);
        }

        return redirect()->route("carousels.index");
    }

    public function storeCarouselSlide(Request $request)
    {
        $this->createCarouselMainImagesDirectory();

        if ($request->has("id")) {
            $data = $request->except('id', 'file');

            if ($request->type === "main" && $request->has("file") && !empty($request->file)) {
                $filename = $request->file("file")->getClientOriginalName();
                $data['img_path'] = $request->file('file')->storeAs(storage_path('app/carousels') . '/main', $filename);
            }

            $this->carouselModel->createCarouselSlideData($data, $request->id);
        }

        return redirect()->route('carousels.index');
    }

    public function update(Request $request)
    {
        if ($request->has("id")) {
            $data = $request->except('id');
            $this->carouselModel->edit($data, $request->id);
        }

        return redirect()->route("carousels.index");
    }

    public function updateCarouselSlide(Request $request)
    {
        if ($request->has("id")) {
            $data = $request->except('id', 'file');

            if ($request->type === "main" && $request->has("file") && !empty($request->file)) {
                $this->removeCurrImgFile($request->img_path);
                $filename = $request->file("file")->getClientOriginalName();
                $data['img_path'] = $request->file('file')->storeAs(storage_path('app/carousels') . '/main', $filename);
            }

            $this->carouselModel->updateCarouselSlideData($data, $request->id);
        }

        return redirect()->route("carousels.index");
    }

    public function remove(Request $request)
    {
        if ($request->has("id")) {
            $this->carouselModel->remove($request->id);
        }

        return redirect()->route("carousels.index");
    }

    public function removeCarouselSlide(Request $request)
    {
        if ($request->has("id")) {
            $data = $request->except('file');
            $this->removeCurrImgFile($request->img_path);
            $this->carouselModel->removeCarouselSlide($data, $request->id);
        }

        return redirect()->route("carousels.index");
    }

    private function createCarouselMainImagesDirectory()
    {
        if (!Storage::disk("carousels")->exists("/main")) Storage::disk("carousels")->makeDirectory("/main");
    }

    private function removeCurrImgFile($path)
    {
        $directories = ["desk", "m"];
        $filename = explode("/", $path);
        $filename = $filename[count($filename) - 1];

        foreach ($directories as $directory) {
            if (Storage::disk("carousels")->exists('/main/' . $directory . '/' . $filename)) Storage::disk("carousels")->delete('/main/' . $directory . '/' . $filename);
        }
    }
}
