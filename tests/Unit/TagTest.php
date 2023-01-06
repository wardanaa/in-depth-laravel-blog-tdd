<?php

namespace Tests\Unit;

use App\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tag_belongs_to_many_blogs()
    {
        // prepare
        $blog = $this->createBlog();
        $tag = $this->createTag();
        $tag->blogs()->attach($blog->id);

        // assert
        $this->assertInstanceOf(Blog::class, $tag->blogs->first());
    }
}
