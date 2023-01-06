<?php

namespace Database\Factories;

use App\Blog;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title     = $this->faker->sentence();
        return [
            'title'   => $title,
            'body'    => $this->faker->text(),
            'image'   => UploadedFile::fake()->image('photo1.jpg'),
            'user_id' => 2,
        ];
    }
}
