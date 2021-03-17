<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class createArticlesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */


    public function guests_cannot_create_articles()
    {

        $article = array_filter(factory(Article::class)->raw(['user_id' => null]));


        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' =>  $article
            ]
        ])->post(route('api.v1.articles.create'))->assertStatus(401);


        $this->assertDatabaseMissing('articles', $article);
    }




    public function Authenticated_users_can_create_articles()
    {
        //create user
        $user = factory(User::class)->create();


        $article = array_filter(factory(Article::class)->raw(['user_id' => null]));

        //verify if don't  exists any article on database
        $this->assertDatabaseMissing('articles', $article);

        Sanctum::actingAs($user);


        $this->jsonApi()->content([
            'data' => [
                'type' => 'articles',
                'attributes' =>  $article
            ]
        ])->post(route('api.v1.articles.create'))->assertCreated();

        //verify if article exists on database
        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'title' => $article['title'],
            'slug' => $article['slug'],
            'content' => $article['content'],
        ]);
    }

    public function title_is_required()
    {
        $article = factory(Article::class)->raw(['title' => '']);



        Sanctum::actingAs(factory(User::class)->create());

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

        Sanctum::actingAs(factory(User::class)->create());

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

        Sanctum::actingAs(factory(User::class)->create());

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

        Sanctum::actingAs(factory(User::class)->create());

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