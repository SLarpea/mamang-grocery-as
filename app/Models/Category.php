<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = [
        "name",
        "description",
        "logo_path",
        "is_active"
    ];

    public function categories()
    {
        return $this->orderBy('created_at', 'desc')->get();
    }

    public function create($data)
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }

        $this->save();
    }

    public function edit(array $data, $id) 
    {
        $category = $this->where("id", $id)->first();

        foreach ($data as $key => $value) {

            $category[$key] = $value;
        }

        $category->save();
    }

    public function remove($id)
    {
        $this->where("id", $id)->delete();
    }

    public function updateStatus(array $data)
    {
        $this->where("id", $data['id'])->update(["is_active"=> $data['is_active']]);
    }
}
