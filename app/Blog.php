<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'body', 'published_at', 'image', 'user_id'];

    protected $casts = ['published_at' => 'datetime'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($blog) {
            $blog->slug = Str::slug($blog->title);
        });
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function uploadImage($image)
    {
        $name = $image->getClientOriginalName();
        Storage::disk('public')->put($name, file_get_contents($image));
        $this->update(['image' => $name]);
    }

    public static function store($request)
    {
        $blog = auth()->user()->blogs()
            ->create($request->except('image'));
        $blog->uploadImage($request->image);
        $blog->tags()->attach($request->tag_ids);
    }

    public function edit($request)
    {
        $this->update($request->except('image'));
        if ($request->has('image')) {
            $this->deleteImage($this->image);
            $this->uploadImage($request->image);
        }
        $this->tags()->sync($request->tag_ids);
    }

    public function deleteImage($imageName)
    {
        Storage::disk('public')->delete($imageName);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function tagIds()
    {
        return $this->tags->pluck('id')->toArray();
    }

    public function getPublishedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d\TH:m') : null;
    }
}
