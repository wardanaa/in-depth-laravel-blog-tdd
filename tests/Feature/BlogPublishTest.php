<?php

namespace Tests\Feature;

use App\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogPublishTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();
        $this->createAuthUser();
    }

    /** @test */
    public function only_authenticated_user_can_publish_a_blog()
    {
        $blog = $this->createBlog(['user_id' => auth()->id()]);

        $res = $this->patch(route('blog.update', $blog->slug), ['published_at' => now()]);

        $res->assertRedirect('/blog');
        $this->assertNotNull($blog->fresh()->published_at);
    }

    /** @test */
    public function only_authenticated_user_can_un_publish_a_blog()
    {
        $blog = $this->createBlog(['published_at' => now(), 'user_id' => auth()->id()]);

        $res = $this->patch(route('blog.update', $blog->slug), ['published_at' => null]);

        $res->assertRedirect('/blog');
        $this->assertNull($blog->fresh()->published_at);
    }
}
