<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartController extends Model
{
    use HasFactory;

    public function store(array $data)
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }

        $this->save();
    }

    public function editQuantity(array $data, $id)
    {
        $cart = $this->where("id", $id)->first();
        $cart->quantity = $data['quantity'];
        $cart->save();
    }

    public function remove($id)
    {
        $this->where("id", $id)->delete();
    }
}
