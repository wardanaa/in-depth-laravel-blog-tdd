<?php

namespace Tests\Feature;

use App\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogImageUploadTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function only_authenticated_user_can_upload_image_along_with_blog_details()
    {
        // prepare
        Storage::fake();
        $this->createAuthUser();
        $blog  = factory(Blog::class)->raw();

        // act
        $res = $this->post(route('blog.store'), $blog);

        // assert
        $this->assertDatabaseHas('blogs', ['image' => $blog['image']->name]);
        Storage::disk('public')->assertExists('photo1.jpg');
    }

    /** @test */
    public function only_authenticated_user_can_change_image_when_updating_blog()
    {
        // prepare
        Storage::fake();
        $this->createAuthUser();
        $blog  = $this->createBlog(['image' => 'photo1.jpg', 'user_id' => auth()->id()]);
        $newImage = UploadedFile::fake()->image('photo2.jpg');

        // act
        $res = $this->patch(route('blog.update', $blog->slug), ['image' => $newImage]);

        // assert
        $this->assertDatabaseHas('blogs', ['image' => $newImage->name]);
        Storage::disk('public')->assertExists('photo2.jpg');
        Storage::disk('public')->assertMissing('photo1.jpg');
    }

    /** @test */
    public function while_deleting_blog_it_image_is_also_deleted_from_storage()
    {
        // prepare
        Storage::fake();
        $this->createAuthUser();
        $blog  = $this->createBlog(['user_id' => auth()->id()]);
        Storage::disk('public')->put('photo1.jpg', file_get_contents($blog->image));

        $blog->update(['image' => 'photo1.jpg']);
        Storage::disk('public')->assertExists('photo1.jpg');

        // act
        $res = $this->delete(route('blog.destroy', $blog->slug));

        // assert
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
        Storage::disk('public')->assertMissing('photo1.jpg');
    }
}
