<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','title','thumbnail','file_name','tags', 'pricing' ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function files(){
        return $this->hasMany(File::class, 'post_id');
    }

    public function comments(){
        return $this->hasMany(Comment::class, 'post_id');
    }

}
