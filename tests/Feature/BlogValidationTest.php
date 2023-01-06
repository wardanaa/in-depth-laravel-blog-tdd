<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogValidationTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setup();
        // prepare
        $this->withExceptionHandling();
        $this->createAuthUser();

        // act
        $this->get('/blog/create');
    }

    /** @test */
    public function while_storing_blog_fields_is_required()
    {
        $res = $this->post(route('blog.store'))->assertRedirect(route('blog.create'));

        // assert
        $res->assertSessionHasErrors(['title', 'body', 'image']);
    }

    /** @test */
    public function while_storing_blog_the_image_field_must_be_an_image()
    {
        $res = $this->post('/blog', ['image' => 'test image'])->assertRedirect(route('blog.create'));

        // assert
        $res->assertSessionHasErrors('image');
    }
}
