<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = []; // Tercera opción

    // protected $fillable = [
    //     'title',
    //     'excerpt',
    //     'body'
    // ];
}
