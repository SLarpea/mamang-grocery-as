<?php

namespace App\Models;

use App\Events\ProductEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $dispatchesEvents = [
        'created' => ProductEvents::class,
        'updated' => ProductEvents::class,
        'deleted' => ProductEvents::class,
    ];
    protected $fillable = [
        "name",
        "img_link",
        "price",
        "categories"
    ];

    public function products()
    {
        return $this->orderBy('updated_at', 'desc')->get();
    }

    public function create(array $data) 
    {
        foreach ($data as $key => $value) {

            if ($key === 'selectedCategories') {
                $this['categories'] = json_encode($value);
                continue;
            }

            $this[$key] = $value;
        }

        $this->save();
    }

    public function edit(array $data, $id) 
    {
        $product = $this->getSingleProduct($id);

        foreach ($data as $key => $value) {

            if ($key === 'selectedCategories') {
                $product['categories'] = json_encode($value);
                continue;
            }

            $product[$key] = $value;
        }

        $product->save();
    }

    public function remove($id)
    {
        $this->where("id", $id)->first()->delete();
    }

    public function activateSale($data)
    {
        $product = $this->getSingleProduct($data["id"]);
        $product->on_sale = true;
        $product->percent_sale = $data["sale_percent"];
        $product->save();
    }

    public function deactivateSale($data)
    {
        $product = $this->getSingleProduct($data["id"]);
        $product->on_sale = false;
        $product->percent_sale = 0;
        $product->save();
    }

    private function getSingleProduct($id)
    {
        return $this->where("id", $id)->first();
    }
}
