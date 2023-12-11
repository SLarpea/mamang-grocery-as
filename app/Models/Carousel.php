<?php

namespace App\Models;

use App\Events\CarouselEvents;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Carousel extends Model
{
    use HasFactory;

    protected $table = 'carousels';
    protected $dispatchesEvents = [
        'created' => CarouselEvents::class,
        'updated' => CarouselEvents::class,
        'deleted' => CarouselEvents::class,
    ];
    protected $fillable = [
        'name',
        'data',
        'type',
        'dots',
        'arrows',
        'accessibility',
        'rows',
        'slides_to_show',
        'slides_per_row',
        'autoplay_speed',
        'center_padding',
        'pause_on_hover',
        'min_slide',
        'max_slide',
        'slider_headers'
    ];
    protected $casts = [
        'data' => AsArrayObject::class,
    ];

    public function carousels($filter)
    {
        return $this->when(isset($filter['current']) && !empty($filter['current']), function ($query) use ($filter) {
                $query->where('name', 'like', '%'. $filter['current'] .'%');
            })
            ->orderBy('updated_at', 'desc')->get();
    }

    public function create(array $data)
    {
        foreach ($data as $key => $value) {
            if ($key === 'data' || $key === 'slider_headers') {
                $this[$key] = json_encode($value);
                continue;
            }
            $this[$key] = $value;
        }

        $this->save();
    }

    public function createCarouselSlideData(array $data, $id)
    {
        $carousel = $this->getSingleCarousel($id);
        $carouselDataArray = is_null($carousel->data) ? [] : $carousel->data->getArrayCopy();
        array_push($carouselDataArray, $data);
        $carousel->data = $carouselDataArray;
        $carousel->save();
    }

    public function edit(array $data, $id)
    {
        $carousel = $this->getSingleCarousel($id);

        foreach ($data as $key => $value) {
            if ($key === 'data' || $key === 'slider_headers') {
                $carousel[$key] = json_encode($value);
                continue;
            }
            $carousel[$key] = $value;
        }

        $carousel->save();
    }

    public function editCarouselData(array $data, $id)
    {
        $carousel = $this->getSingleCarousel($id);
        $carouselData = $carousel->data->getArrayCopy() ?? [];
        $searchKey = ($carousel->type === "promo") ? "text" : "image_path";

        $slideIdx = array_search($data['previous_path'], array_column($carouselData, $searchKey));

        foreach ($data as $key => $value) {
            if ($key === 'image_path') $carouselData[$slideIdx][$key] = $value;
        }

        $carousel->data = $carouselData;
        $carousel->save();
    }

    public function remove($id)
    {
        $this->where('id', $id)->first()->delete();
    }

    public function removeCarouselSlide(array $data, $id)
    {
        $carousel = $this->getSingleCarousel($id);
        $carouselData = $carousel->data->getArrayCopy() ?? [];
        $searchKey = ($carousel->type === "promo") ? "text" : "image_path";

        $slideIdx = array_search($data[$searchKey], array_column($carouselData, $searchKey));

        array_splice($carouselData, $slideIdx, 1);
        if (count($carouselData) !== 0) $carouselData = $this->adjustSortOrder($carouselData);

        $carousel->data = $carouselData;
        $carousel->save();
    }

    public function getSingleCarousel($id)
    {
        return $this->where('id', $id)->first();
    }

    private function adjustSortOrder(array $data) {
        foreach ($data as $key => &$value) {
            $value['sorted_order'] = $key;
        }

        return $data;
    }
}
