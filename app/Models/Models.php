<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    use HasFactory;

    public $table = "models";

    public $fillable = [
        'brand_id',
        'title',
        'status'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
