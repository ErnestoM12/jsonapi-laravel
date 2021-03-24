<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
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
    public function guests_users_cannot_create_articles()
    {

        $article = array_filter(Article::factory()->raw(['user_id' => null]));


        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))->assertStatus(401);


        $this->assertDatabaseMissing('articles', $article);
    }
    /**
     * @test
     */
    public function Authenticated_users_can_create_articles()
    {
        //create user
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = array_filter(Article::factory()->raw([
            'category_id' => null,
        ]));

        //verify if don't  exists any article on database
        $this->assertDatabaseMissing('articles', $article);

        Sanctum::actingAs($user, ['articles:create']);


        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article,
            'relationships' => [
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]


        ])->post(route('api.v1.articles.create'))
            ->assertCreated();

        //verify if article exists on database
        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'title' => $article['title'],
            'slug' => $article['slug'],
            'content' => $article['content'],
        ]);
    }



    /**
     * @test
     */
    public function Authenticated_users_cannot_create_articles_without_permissions()
    {
        //create user
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = Article::factory()->raw();


        Sanctum::actingAs($user);


        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article,
            'relationships' => [
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]


        ])->post(route('api.v1.articles.create'))
            ->assertStatus(403);
    }

    /**
     * @test
     */
    public function Authenticated_users_cannot_create_articles_on_behalf_of_anothe_users()
    {
        //create user
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $article = array_filter(Article::factory()->raw([
            'category_id' => null,
            'user_id' => null
        ]));

        //verify if don't  exists any article on database
        $this->assertDatabaseMissing('articles', $article);

        Sanctum::actingAs($user);


        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article,
            'relationships' => [
                'authors' => [
                    'data' => [
                        'id' => User::factory()->create()->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]


        ])->post(route('api.v1.articles.create'))
            ->assertStatus(403);
    }


    /**
     * @test
     */
    public function authors_must_be_a_relationships_object()
    {
        $article = Article::factory()->raw();
        $category = Category::factory()->create();

        $article['authors'] = "string";

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/authors');

        //$this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function categories_must_be_a_relationships_object()
    {
        $article = Article::factory()->raw(['category_id' => null]);

        $article['categories'] = "string";

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/categories');

        //$this->assertDatabaseMissing('articles', $article);
    }


    /**
     * @test
     */
    public function authors_is_required()
    {
        $article = Article::factory()->raw(['category_id' => null]);

        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => "/data"]]);

        $this->assertDatabaseMissing('articles', $article);
    }
    /**
     * @test
     */
    public function categories_is_required()
    {
        $article = Article::factory()->raw(['category_id' => null]);



        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => "/data"]]);

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function title_is_required()
    {
        $article = Article::factory()->raw(['title' => '']);



        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/title');

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function content_is_required()
    {
        $article = Article::factory()->raw(['content' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/content');

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_is_required()
    {
        $article = Article::factory()->raw(['slug' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_be_unique()
    {
        Article::factory()->create(['slug' => 'same-slug']);

        $article = Article::factory()->raw(['slug' => 'same-slug']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {


        $article = Article::factory()->raw(['slug' => '·$%%&%"!']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' =>  $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_not_contain_underscores()
    {


        $article = Article::factory()->raw(['slug' => 'with_underscore']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' =>  $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']))
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->raw(['slug' => '-starts-with-dashes']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' =>  $article

        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /**
     * @test
     */
    public function slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->raw(['slug' => 'end-with-dashes-']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([

            'type' => 'articles',
            'attributes' =>  $article

        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }
}
