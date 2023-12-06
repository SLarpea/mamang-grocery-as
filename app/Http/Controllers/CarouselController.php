<?php

namespace App\Http\Controllers;

use App\Events\CarouselEvents;
use App\Models\Carousel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Log;
use Imagick;

class CarouselController extends Controller
{
    private $carouselModel;
    private $imagick;

    public function __construct()
    {
        $this->carouselModel = new Carousel();
        $this->imagick = new Imagick();
    }

    public function index()
    {
        CarouselEvents::dispatchIf(!cache()->has("carousels" || "carousel"), request('current'));

        if (!is_null(request('current'))) {
            return Inertia::render("Admin/Carousel", [
                "carousel" => cache()->get("carousel"),
                "message" => "success"
            ]);
        }
        
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
        $carousel = $this->carouselModel->getSingleCarousel($request->carousel_id);

        if ($request->has("carousel_id")) {
            $data = $request->except('carousel_id', 'file', 'image_path');
            $data['sorted_order'] = count($carousel->data ?? []);

            if ($request->has("file") && !empty($request->file)) {
                $this->saveFileToStorage($request->file('file'));
                $filename = $request->file("file")->getClientOriginalName();
                $data['image_path'] = 'carousels/main/m/' . $filename;
            }

            $this->carouselModel->createCarouselSlideData($data, $request->carousel_id);
        }

        return to_route('carousels.index', ['current' => $carousel->type]);
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
        $carousel = $this->carouselModel->getSingleCarousel($request->carousel_id);

        if ($request->has("carousel_id")) {
            $data = $request->except('carousel_id', 'file', 'type');

            if ($request->has("file") && !empty($request->file)) {
                $filename = $request->file('file')->getClientOriginalName();
                $this->removeCurrImgFile($request->image_path);
                $this->saveFileToStorage($request->file('file'));
                $data['previous_path'] = $request->image_path;
                $data['image_path'] = 'carousels/main/m/' . $filename;
            }

            $this->carouselModel->editCarouselData($data, $request->carousel_id);
        }

        return to_route('carousels.index', ['current' => $carousel->type]);
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
        $carousel = $this->carouselModel->getSingleCarousel($request->carousel_id);

        if ($request->has("carousel_id")) {
            $data = $request->except('file');
            if ($request->has("image_path")) $this->removeCurrImgFile($request->image_path);
            $this->carouselModel->removeCarouselSlide($data, $request->carousel_id);
        }

        return to_route('carousels.index', ['current' => $carousel->type]);
    }

    private function createCarouselMainImagesDirectory()
    {
        $directories = array("desk", "m");
        foreach ($directories as $directory) {
            if (!Storage::disk("carousels")->exists('/main/'. $directory)) Storage::disk("carousels")->makeDirectory('/main/'. $directory);
        }
    }

    private function saveFileToStorage($file)
    {
        $image = $this->imagick;

        $image->readImage($file->getRealPath());

        // save original file
        $image->writeImage(storage_path('app/carousels') . '/main/' . 'desk/' . $file->getClientOriginalName());

        // create thumbnail image
        $image->resizeImage(250, 250, Imagick::FILTER_CATROM, 1);
        $image->setCompressionQuality(60);
        $image->writeImage(storage_path('app/carousels') . '/main/' . 'm/' . $file->getClientOriginalName());

        $image->clear();
        $image->destroy();
    }

    private function removeCurrImgFile($path)
    {
        $directories = array("desk", "m");
        $filename = explode("/", $path);
        $filename = $filename[count($filename) - 1];

        foreach ($directories as $directory) {
            if (Storage::disk("carousels")->exists('/main/' . $directory . '/' . $filename)) Storage::disk("carousels")->delete('/main/' . $directory . '/' . $filename);
        }
    }
}
