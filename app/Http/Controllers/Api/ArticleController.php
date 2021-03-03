<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;

class ArticleController extends Controller
{

    function index(Article $article){

        return ArticleCollection::make(Article::all());
   
       }


    function show(Article $article){

     return ArticleResource::make($article);  

    }
}
