<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvUpload extends Model
{
    public $timestamps = true; 
    
    protected $fillable = [
        'unique_key', 
        'product_title',
        'product_description',
        'style',
        'sanmar_mainframe_color',
        'size',
        'color_name',
        'piece_price',
        'status',
        'created_at',
        'updated_at',
    ];

    use HasFactory;
}
