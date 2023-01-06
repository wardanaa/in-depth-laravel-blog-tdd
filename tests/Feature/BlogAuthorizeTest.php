<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogAuthorizeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_not_update_blog_of_other_user()
    {
        // prepare
        $this->withExceptionHandling();
        $user = $this->createAuthUser();
        $blog = $this->createBlog();

        // act
        $res = $this->patch(route('blog.update', $blog->slug));

        // assert
        $res->assertStatus(403);
    }

    /** @test */
    public function user_can_not_delete_blog_of_other_user()
    {
        // prepare
        $this->withExceptionHandling();
        $user = $this->createAuthUser();
        $blog = $this->createBlog();

        // act
        $res = $this->delete(route('blog.update', $blog->slug));

        // assert
        $res->assertStatus(403);
    }
}
