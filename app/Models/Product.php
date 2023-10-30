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
        "price"
    ];

    public function getAllProducts() 
    {
        return $this->paginate(10);
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
        $product = $this->where("id", $id)->first();

        foreach ($data as $key => $value) {
            $product[$key] = $value;
        }

        $product->save();
    }

    public function remove($id)
    {
        $this->where("id", $id)->delete();
    }
}
