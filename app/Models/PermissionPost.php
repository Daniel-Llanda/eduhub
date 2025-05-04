<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionPost extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','teacher_id','post_id','permission_post_status','permission_note'];
    public function teacher(){
        return $this->belongsTo(Teacher::class, 'teacher_id'); 
        // or Teacher::class if you have a Teacher model
    }

}
