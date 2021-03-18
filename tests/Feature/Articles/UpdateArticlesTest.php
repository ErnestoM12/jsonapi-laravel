<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;


class UpdateArticlesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function guests_users_cannot_update_articles()
    {
        $article = factory(Article::class)->create();

        $this->jsonApi()
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(401);
    }

    public function authenticade_users_can_update_their_articles()
    {
        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);


        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attributes' => [
                        'title' => 'Title Changed',
                        'slug' => ' title-changed',
                        'content' => 'Content changed',
                    ]
                ]

            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);
    }


    public function authenticade_users_cannot_update_others_articles()
    {
        $article = factory(Article::class)->create();

        Sanctum::actingAs($user = factory(User::class)->create());


        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attributes' => [
                        'title' => 'Title Changed',
                        'slug' => ' title-changed',
                        'content' => 'Content changed',
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(403);
    }





    public function can_update_the_title_only()
    {
        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);


        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attributes' => [
                        'title' => 'Title Changed',
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);
    }

    public function can_update_the_slug_only()
    {
        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);


        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attributes' => [
                        'slug' => 'slug-Changed',
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200);
    }
}
