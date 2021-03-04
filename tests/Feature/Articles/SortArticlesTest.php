<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;

class SortArticlesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    // public function it_can_articles_by_title_asc()
    // {
    //     factory(Article::class)->create(['title' =>  'C Title']); 
    //     factory(Article::class)->create(['title' =>  'A Title']); 
    //     factory(Article::class)->create(['title' =>  'B Title']); 
       
    //     $url = route('api.v1.articles.index', ['sort'=>'title']);
    //     $this->getJson($url)->assertSeeInOrder([
    //         'A Title',
    //         'B Title',
    //         'C Title',
    //     ]);
    // }

    //  /**
    //  * @test
    //  */
    // public function it_can_articles_by_title_desc()
    // {
    //     factory(Article::class)->create(['title' =>  'C Title']); 
    //     factory(Article::class)->create(['title' =>  'A Title']); 
    //     factory(Article::class)->create(['title' =>  'B Title']); 
       
    //     $url = route('api.v1.articles.index', ['sort'=>'-title']);
    //     $this->getJson($url)->assertSeeInOrder([
    //         'C Title',
    //         'B Title',
    //         'A Title',
    //     ]);
    // }


    public function it_can_articles_by_title_and_content()
    {
        factory(Article::class)->create([
            'title' =>  'C Title',
            'content' => 'B Content'
            ]); 
        factory(Article::class)->create([
            'title' =>  'A Title',
            'content' => 'A Content'
            ]); 
        factory(Article::class)->create([
            'title' =>  'B Title',
            'content' => 'C Content'
            ]); 


       $url = route('api.v1.articles.index').'?sort=title,-content';
      
        $this->getJson($url)->assertSeeInOrder([
            'A Title',
            'B Title',
            'C Title',
        ]);



        $url = route('api.v1.articles.index').'?sort=-content,title';
      
        $this->getJson($url)->assertSeeInOrder([
            'C Content',
            'B Content',
            'A Content',
        ]);

    }
}
