<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCourse extends Model
{
    //
    protected $table = 'lessons';


    protected $fillable = [
    'course_id',
    'title',
    'video'
];

public function course()
{
    return $this->belongsTo(\App\Models\Course::class);
}

}
