<?php

namespace Tests\Unit;

use App\Tag;
use App\Blog;
use App\User;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function blog_can_upload_its_image()
    {
        // preare
        Storage::fake();
        $blog  = new Blog();
        $image = UploadedFile::fake()->image('photo1.jpg');

        // act
        $blog->uploadImage($image);

        // assert
        Storage::disk('public')->assertExists('photo1.jpg');
    }

    /** @test */
    public function blog_belongs_to_a_user()
    {
        $user = $this->createUser();
        $blog = $this->createBlog(['user_id' => $user->id]);

        // assert
        $this->assertInstanceOf(User::class, $blog->user);
    }

    /** @test */
    public function blog_has_many_tags()
    {
        $tag  = $this->createTag();
        $blog = $this->createBlog();
        $blog->tags()->attach($tag->id);

        // assert
        $this->assertInstanceOf(Tag::class, $blog->tags[0]);
    }

    /** @test */
    public function blog_can_get_all_its_tag_ids_in_an_array()
    {
        // prepare
        $blog = $this->createBlog();
        $tags = $this->createTag([], 4);
        $blog->tags()->attach($tags->pluck('id'));

        // assert
        $this->assertIsArray($blog->tagIds());
        $this->assertEquals(4, count($blog->tagIds()));
        $this->assertEquals($tags[3]->id, $blog->tagIds()[3]);
    }

    /** @test */
    public function blog_has_published_at_fied_formated()
    {
        $time  = now();
        $blog  = $this->createBlog(['published_at' => $time]);
        $this->assertEquals($time->format('Y-m-d\TH:m'), $blog->published_at);
    }
}
