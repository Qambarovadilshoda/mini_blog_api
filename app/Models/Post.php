<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

class Post extends Model
{

    #[ObservedBy([PostObserver::class])]
    protected $fillable = [
        'user_id',
        'title',
        'slug'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
