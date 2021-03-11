<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use Illuminate\Support\Str;

class ArticleController extends Controller
{

    function index(){

   
        $articles = Article::applyFilters()->applySorts()->jsonPaginate();
        

        return ArticleCollection::make($articles);
   
       }


    function show(Article $article){

     return ArticleResource::make($article);  

    }
}
