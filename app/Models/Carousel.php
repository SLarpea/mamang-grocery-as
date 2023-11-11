<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;

    protected $table = 'carousels';
    protected $fillable = [
        'name',
        'data',
        'type',
        'min_slide',
        'max_slide'
    ];
    protected $casts = [
        'data' => AsArrayObject::class,
    ];

    public function carousels()
    {
        return $this->all();
    }

    public function create(array $data)
    {
        foreach ($data as $key => $value) {
            if ($key === 'data') {
                $this['data'] = json_encode($value);
                continue;
            }
            $this[$key] = $value;
        }

        $this->save();
    }

    public function createCarouselSlideData(array $data, $id)
    {
        $carousel = $this->getSingleCarousel($id);
        $carouselData = $carousel->data;
        array_push($carouselData, $data);
        $carousel->data = $carouselData;
        $carousel->save();
    }

    public function edit(array $data, $id)
    {
        $carousel = $this->getSingleCarousel($id);

        foreach ($data as $key => $value) {
            if ($key === 'data') {
                $carousel['data'] = json_encode($value);
                continue;
            }
            $carousel[$key] = $value;
        }

        $carousel->save();
    }

    public function editCarouselData(array $data, $id)
    {
        $carousel = $this->getSingleCarousel($id);
        $carouselData = $carousel->data;

        $slideIdx = array_search($data['name'], array_column($carouselData, 'name'));

        foreach ($data as $key => $value) {
            $carouselData[$slideIdx][$key] = $value;
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
        $carouselData = $carousel->data;

        $slideIdx = array_search($data['name'], array_column($carouselData, 'name'));

        unset($carouselData[$slideIdx]);

        $carousel->data = $carouselData;
        $carousel->save();
    }

    private function getSingleCarousel($id)
    {
        return $this->where('id', $id)->first();
    }
}
