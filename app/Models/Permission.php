<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','post_id','receiver_id','permission_status'];
    public function requester() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
    public function post() {
        return $this->belongsTo(Post::class, 'post_id');
    }
    
}
