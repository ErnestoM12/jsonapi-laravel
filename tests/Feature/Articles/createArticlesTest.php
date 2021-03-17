<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class createArticlesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function can_create_articles()
    {
        $article = factory(Article::class)->raw();

        //verify if don't  exists any article on database

        $this->assertDatabaseMissing('articles', $article);

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' =>  $article
            ]
        ])->post(route('api.v1.articles.create'))->assertCreated();

        //verify if article exists on database

        $this->assertDatabaseHas('articles', $article);
    }

    public function title_is_required()
    {
        $article = factory(Article::class)->raw(['title' => '']);

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' =>  $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/title');

        $this->assertDatabaseMissing('articles', $article);
    }

    public function content_is_required()
    {
        $article = factory(Article::class)->raw(['content' => '']);

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' =>  $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/content');

        $this->assertDatabaseMissing('articles', $article);
    }

    public function slug_is_required()
    {
        $article = factory(Article::class)->raw(['slug' => '']);

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' =>  $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }


    public function slug_must_be_unique()
    {
        factory(Article::class)->create(['slug' => 'same-slug']);

        $article = factory(Article::class)->raw(['slug' => 'same-slug']);

        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' =>  $article
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }
}
