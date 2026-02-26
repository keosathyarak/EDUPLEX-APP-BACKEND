<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
    'title',
    'teacher',
    'lessons',
    'price',
    'image'

];




public function lessons()
{
    return $this->hasMany(\App\Models\VideoCourse::class);


}



}
