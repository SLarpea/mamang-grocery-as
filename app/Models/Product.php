<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        "name",
        "img_link",
        "price",
        "categories"
    ];

    public function getAllProducts() 
    {
        return $this->all();
    }

    public function create(array $data) 
    {
        foreach ($data as $key => $value) {

            if ($key === 'categories') {
                $this[$key] = json_encode($value);
                continue;
            }

            $this[$key] = $value;
        }

        $this->save();
    }

    public function edit(array $data, $id) 
    {
        $product = $this->where("id", $id)->first();

        foreach ($data as $key => $value) {

            if ($key === 'categories') {
                $product[$key] = json_encode($value);
                continue;
            }

            $product[$key] = $value;
        }

        $product->save();
    }

    public function remove($id)
    {
        $this->where("id", $id)->delete();
    }
}
