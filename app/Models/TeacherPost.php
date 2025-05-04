<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherPost extends Model
{
    use HasFactory;
    protected $fillable = ['teacher_id','title', 'description','thumbnail','file_name','tags',];
    public function teacher(){
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
