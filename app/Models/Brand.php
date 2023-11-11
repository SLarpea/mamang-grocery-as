<?php

namespace App\Models;

use App\Events\BrandEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';
    protected $dispatchesEvents = [
        'created' => BrandEvents::class,
        'updated' => BrandEvents::class,
        'deleted' => BrandEvents::class,
    ];
    protected $fillable = [
        'name'
    ];

    public function brands()
    {
        return $this->orderBy('updated_at','desc')->get();
    }

    public function create(array $data)
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }

        $this->save();
    }

    public function edit(array $data, $id)
    {
        $brand = $this->where('id', $id)->first();

        foreach ($data as $key => $value) {
            $brand[$key] = $value;
        }

        $brand->save();
    }

    public function remove($id)
    {
        $this->where("id", $id)->first()->delete();
    }
}
