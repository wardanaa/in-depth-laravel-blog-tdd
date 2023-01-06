<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($tag) {
            $tag->slug = Str::slug($tag->name);
        });
    }

    public function blogs()
    {
        return $this->belongsToMany(Blog::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
