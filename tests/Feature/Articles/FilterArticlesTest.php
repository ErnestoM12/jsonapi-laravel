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
      factory(Article::class)->create([
          'title' => 'Aprende Laravel desde cero'
      ]);
    
      factory(Article::class)->create([
          'title' => 'other Article'
      ]);

      $url = route('api.v1.articles.index',['filter[title]' => 'Laravel']);

      $this->getJson($url)
           ->assertJsonCount(1,'data')
           ->assertSee('Aprende Laravel desde cero')
           ->assertDontSee('other Article');
    }
    

    public function can_filter_articles_by_content()
    {
      factory(Article::class)->create([
          'content' => '<div>Aprende Laravel desde cero</div>'
      ]);
    
      factory(Article::class)->create([
          'content' => '<div>other Article</div>'
      ]);

      $url = route('api.v1.articles.index',['filter[content]' => 'Laravel','filter[title]' => 'otro']);

      $this->getJson($url)
      ->assertJsonCount(1,'data')
      ->assertSee('Aprende Laravel desde cero')
      ->assertDontSee('other Article');
    }


    public function can_filter_articles_by_year()
    {
       factory(Article::class)->create([
          'title' => 'Article form 2020',
          'created_at' => now()->year(2020)
       ]);
    
       factory(Article::class)->create([
        'title' => 'Article form 2021',
        'created_at' => now()->year(2021)
       ]);

      $url = route('api.v1.articles.index',['filter[year]' => 2020]);

      $this->getJson($url)
           ->assertJsonCount(1,'data')
           ->assertSee('Article form 2020')
           ->assertDontSee('Article form 2021');
    }

    public function can_filter_articles_by_month()
    {
       factory(Article::class)->create([
          'title' => 'Article form febrary 2020',
          'created_at' => now()->month(2)
       ]);

      factory(Article::class)->create([
        'title' => 'Article form febrary 2021',
        'created_at' => now()->month(2)
     ]);
    
       factory(Article::class)->create([
        'title' => 'Article form January 2021',
        'created_at' => now()->month(1)
       ]);

      $url = route('api.v1.articles.index',['filter[month]' => 2 ]);

      $this->getJson($url)
           ->assertJsonCount(2,'data')
           ->assertSee('Article form febrary 2020')
           ->assertSee('Article form febrary 2021')
           ->assertDontSee('Article form January 2021');
    }



  public function can_filter_articles_by_unknow_filters()
    {
  

      $url = route('api.v1.articles.index',['filter[unknow]' => 2 ]);

      $this->getJson($url)-assertStatus(400); //bad request
    }


  public function can_search_articles_title_and_content()
    {
       factory(Article::class)->create([
          'title' => 'Article form aprendible',
          'content' => 'Content'
       ]);

      factory(Article::class)->create([
        'title' => 'Another Article',
        'content' => 'Content aprendible---'
     ]);
    
       factory(Article::class)->create([
        'title' => 'Article',
        'content' => 'content 5'
       ]);

      $url = route('api.v1.articles.index',['filter[search]' => 'aprendible' ]);

      $this->getJson($url)
           ->assertJsonCount(2,'data')
           ->assertSee('Article form aprendible')
           ->assertSee('Another Article')
           ->assertDontSee('Article');
    }
 

  public function can_search_articles_title_and_content_whit_multiple_terms()
    {
       factory(Article::class)->create([
          'title' => 'Article form aprendible',
          'content' => 'Content'
       ]);

       factory(Article::class)->create([
        'title' => 'Another Article',
        'content' => 'Content aprendible---'
     ]); 

      factory(Article::class)->create([
        'title' => 'Another Laravel Article',
        'content' => 'Content'
     ]);
    
       factory(Article::class)->create([
        'title' => 'Article',
        'content' => 'content 5'
       ]);

      $url = route('api.v1.articles.index',['filter[search]' => 'aprendible Laravel' ]);

      $this->getJson($url)
           ->assertJsonCount(3,'data')
           ->assertSee('Article form aprendible')
           ->assertSee('Another Article')
           ->assertSee('Another Laravel Article')
           ->assertDontSee('Article');
    }   



}
