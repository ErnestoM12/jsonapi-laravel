<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;

class FilterArticlesTest extends TestCase
{
  use RefreshDatabase;
  /**
   * @test
   */
  public function can_filter_articles_by_title()
  {
    Article::factory()->create([
      'title' => 'Aprende Laravel desde cero'
    ]);

    Article::factory()->create([
      'title' => 'other Article'
    ]);

    $url = route('api.v1.articles.index', ['filter[title]' => 'Laravel']);

    $this->jsonApi()->get($url)
      ->assertJsonCount(1, "data")
      ->assertSee('Aprende Laravel desde cero')
      ->assertDontSee('other Article');
  }


  public function can_filter_articles_by_content()
  {
    Article::factory()->create([
      'content' => '<div>Aprende Laravel desde cero</div>'
    ]);

    Article::factory()->create([
      'content' => '<div>other Article</div>'
    ]);

    $url = route('api.v1.articles.index', ['filter[content]' => 'Laravel', 'filter[title]' => 'otro']);

    $this->jsonApi()->get($url)
      ->assertJsonCount(1, "data")
      ->assertSee('Aprende Laravel desde cero')
      ->assertDontSee('other Article');
  }


  public function can_filter_articles_by_year()
  {
    Article::factory()->create([
      'title' => 'Article form 2020',
      'created_at' => now()->year(2020)
    ]);

    Article::factory()->create([
      'title' => 'Article form 2021',
      'created_at' => now()->year(2021)
    ]);

    $url = route('api.v1.articles.index', ['filter[year]' => 2020]);

    $this->jsonApi()->get($url)
      ->assertJsonCount(1, "data")
      ->assertSee('Article form 2020')
      ->assertDontSee('Article form 2021');
  }

  public function can_filter_articles_by_month()
  {
    Article::factory()->create([
      'title' => 'Article form febrary 2020',
      'created_at' => now()->month(3)
    ]);

    Article::factory()->create([
      'title' => 'Article form febrary 2021',
      'created_at' => now()->month(3)
    ]);

    Article::factory()->create([
      'title' => 'Article form January 2021',
      'created_at' => now()->month(1)
    ]);

    $url = route('api.v1.articles.index', ['filter[month]' => 2]);

    $this->jsonApi()->get($url)
      ->assertJsonCount(2, "data")
      ->assertSee('Article form febrary 2020')
      ->assertSee('Article form febrary 2021')
      ->assertDontSee('Article form January 2021');
  }



  public function can_filter_articles_by_unknow_filters()
  {


    $url = route('api.v1.articles.index', ['filter[unknow]' => 2]);

    $this->jsonApi()->get($url)->assertStatus(400); //bad request
  }


  public function can_search_articles_title_and_content()
  {
    Article::factory()->create([
      'title' => 'Article form aprendible',
      'content' => 'Content'
    ]);

    Article::factory()->create([
      'title' => 'Another Article',
      'content' => 'Content aprendible---'
    ]);

    Article::factory()->create([
      'title' => 'Article',
      'content' => 'content 5'
    ]);

    $url = route('api.v1.articles.index', ['filter[search]' => 'aprendible']);

    $this->jsonApi()->get($url)
      ->assertJsonCount(2, "data")
      ->assertSee('Article form aprendible')
      ->assertSee('Another Article')
      ->assertDontSee('Article');
  }


  public function can_search_articles_title_and_content_whit_multiple_terms()
  {
    Article::factory()->create([
      'title' => 'Article form aprendible',
      'content' => 'Content'
    ]);

    Article::factory()->create([
      'title' => 'Another Article',
      'content' => 'Content aprendible---'
    ]);

    Article::factory()->create([
      'title' => 'Another Laravel Article',
      'content' => 'Content'
    ]);

    Article::factory()->create([
      'title' => 'Article',
      'content' => 'content 5'
    ]);

    $url = route('api.v1.articles.index', ['filter[search]' => 'aprendible Laravel']);

    $this->jsonApi()->get($url)
      ->assertJsonCount(3, "data")
      ->assertSee('Article form aprendible')
      ->assertSee('Another Article')
      ->assertSee('Another Laravel Article')
      ->assertDontSee('Article');
  }
}
