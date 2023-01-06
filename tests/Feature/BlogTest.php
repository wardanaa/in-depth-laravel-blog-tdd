<?php

namespace Tests\Feature;

use App\Blog;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();
        $this->createAuthUser();
    }

    /** @test */
    public function user_can_see_all_published_the_blogs()
    {
        $blog = $this->createBlog(['published_at' => now()], 2);
        $unPublishedBlog  = $this->createBlog();

        $response = $this->get(route('blog.index'));

        $response->assertStatus(200);
        $response->assertSee($blog[0]->title);
        $response->assertSee($blog[1]->title);
        $response->assertDontSee($unPublishedBlog->title);
    }

    /** @test */
    public function user_can_see_all_his_unpublished_or_published_blogs()
    {
        $pubblog = $this->createBlog(['published_at' => now(), 'user_id' => auth()->id()], 2);
        $unPubBlog = $this->createBlog(['user_id' => auth()->id()], 2);
        $user2Blog = $this->createBlog(['user_id' => 22]);

        $response = $this->get(route('blog.all'));

        $response->assertStatus(200);
        $response->assertSee($pubblog[0]->title);
        $response->assertSee($pubblog[1]->title);
        $response->assertSee($unPubBlog[0]->title);
        $response->assertSee($unPubBlog[1]->title);
        $response->assertDontSee($user2Blog->title);
    }

    /** @test */
    public function user_can_visit_a_published_single_blog()
    {
        $user = $this->createUser();
        $blog = $this->createBlog(['published_at' => now(), 'user_id' => $user->id]);
        $tag = $this->createTag();
        $blog->tags()->attach($tag->id);

        $res = $this->get(route('blog.show', $blog->slug));

        $res->assertStatus(200);
        $res->assertSee($blog->title);
        $res->assertSee($blog->body);
        $res->assertSee($blog->user->name);
        $res->assertSee($blog->tags[0]->name);
    }

    /** @test */
    public function user_can_not_visit_a_unpublished_single_blog()
    {
        $this->withExceptionHandling();
        $blog = $this->createBlog();

        $res = $this->get(route('blog.show', $blog->slug));

        $res->assertStatus(404);
        $res->assertDontSee($blog->title);
    }

    /** @test */
    public function only_authenticated_user_can_store_a_blog()
    {
        $blog  = factory(Blog::class)->raw();
        $tags = $this->createTag([], 2);

        unset($blog['user_id']);

        $data  = array_merge(
            ['tag_ids' => $tags->pluck('id')->toArray()],
            $blog
        );

        $res = $this->post(route('blog.store'), $data);

        $res->assertRedirect('/blog');
        $this->assertDatabaseHas('blogs', [
            'image' => $blog['image']->name,
            'user_id' => auth()->id()
        ]);
        $this->assertDatabaseHas('blog_tag', [
            'tag_id' => $tags[0]->id
        ]);
    }

    /** @test */
    public function only_authenticated_user_can_delete_a_blog()
    {
        $blog = $this->createBlog(['user_id' => auth()->id()]);
        $tag = $this->createTag();
        $blog->tags()->attach($tag->id);

        $res = $this->delete(route('blog.destroy', $blog->slug));

        $res->assertRedirect('/blog');
        $this->assertDatabaseMissing('blogs', ['title' => $blog->title]);
        $this->assertDatabaseMissing('blog_tag', ['blog_id' => $blog->id]);
    }

    /** @test */
    public function only_authenticated_user_can_update_blog_details()
    {
        // prepare
        $blog = $this->createBlog(['user_id' => auth()->id()]);
        $tags = $this->createTag([], 2);
        $blog->tags()->attach($tags->pluck('id'));

        // act
        $res = $this->patch(route('blog.update', $blog->slug), ['title' => 'updated title', 'tag_ids' => $tags[0]->id]);

        // assert
        $res->assertRedirect('/blog');
        $this->assertDatabaseHas('blogs', ['id' => $blog->id, 'title' => 'updated title']);
        $this->assertDatabaseMissing('blog_tag', [
            'blog_id' => $blog->id,
            'tag_id' => $tags[1]->id,
        ]);
    }

    /** @test */
    public function user_can_visit_a_form_to_store_a_blog()
    {
        $res = $this->get(route('blog.create'));

        $res->assertStatus(200);
        $res->assertSee('Create New Blog');
    }

    /** @test */
    public function user_can_visit_a_blog_update_form()
    {
        $blog = $this->createBlog();

        $res = $this->get(route('blog.edit', $blog->slug));

        $res->assertStatus(200);
        $res->assertSee('Update The Blog');
        $res->assertSee($blog->title);
    }
}
