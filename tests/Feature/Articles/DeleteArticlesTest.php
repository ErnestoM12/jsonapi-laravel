<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteArticlesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function guests_users_cannot_delete_articles()
    {

        $article = factory(Article::class)->create();

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
            ->assertStatus(401);
    }

    public function authenticated_users_can_delete_their_articles()
    {

        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
            ->assertStatus(204);
    }


    public function authenticated_users_cannot_delete_other_articles()
    {

        $article = factory(Article::class)->create();

        Sanctum::actingAs($user = factory(User::class)->create());

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
            ->assertStatus(403);
    }
}
