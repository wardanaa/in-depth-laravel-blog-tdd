<?php

namespace Tests\Feature;

use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function user_can_create_a_tag()
    {
        // prepare

        // act
        $res = $this->post(route('tag.store'), ['name' => 'Laravel']);

        // assert
        $res->assertRedirect(route('tag.index'));
        $this->assertDatabaseHas('tags', ['name' => 'Laravel']);
    }

    /** @test */
    public function user_can_get_all_tags()
    {
        // prepare
        $tag   = $this->createTag();

        // act
        $res = $this->get(route('tag.index'));

        // assert
        $res->assertStatus(200);
        $res->assertSee($tag->name);
    }

    /** @test */
    public function user_can_delete_a_tag()
    {
        // prepare
        $tag   = $this->createTag();

        // act
        $res = $this->delete(route('tag.destroy', $tag->slug));

        // assert
        $res->assertRedirect(route('tag.index'));
        $this->assertDatabaseMissing('tags', ['name' => $tag->name]);
    }

    /** @test */
    public function user_can_delete_a_tag_and_blog_link_also_deleted()
    {
        // prepare
        $tag   = $this->createTag();
        $blog = $this->createBlog();
        $tag->blogs()->attach($blog->id);


        // act
        $res = $this->delete(route('tag.destroy', $tag->slug));

        // assert
        $this->assertDatabaseMissing('blog_tag', [
            'blog_id' => $blog->id,
            'tag_id' => $tag->id,
        ]);
        $this->assertDatabaseHas('blogs', ['id' => $blog->id]);
    }

    /** @test */
    public function user_can_update_a_tag()
    {
        // prepare
        $tag   = $this->createTag();
        // act
        $res = $this->patch(route('tag.update', $tag->slug), ['name' => 'sarthak']);
        // assert
        $res->assertRedirect(route('tag.index'));
        $this->assertDatabaseHas('tags', ['name' => 'sarthak']);
    }

    /** @test */
    public function user_can_visit_tag_create_page()
    {
        // prepare

        // act
        $res = $this->get(route('tag.create'));
        // assert
        $res->assertOk();
        $res->assertSee('Create New Tag');
    }

    /** @test */
    public function user_can_visit_tag_edit_page()
    {
        // 
        $tag   = $this->createTag();

        // act
        $res = $this->get(route('tag.edit', $tag->slug));
        // assert
        $res->assertOk();
        $res->assertSee('Update The Tag');
    }
}
