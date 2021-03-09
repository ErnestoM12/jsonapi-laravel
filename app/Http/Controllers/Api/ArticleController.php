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

    function index(Article $article){
       
      $sortFields = Str::of(request('sort'))->explode(',');
      $articleQuery = Article::query();

        foreach($sortFields as $sortField){
            $direction = 'asc';

            if(Str::of($sortField)->startsWith('-')){
                $direction = 'desc';
                $sortField = Str::of($sortField)->substr(1);
            } 
            $articleQuery->orderBy($sortField,$direction);  
        }


      
       $artices =  Article::applySorts('sort')->get();

        return ArticleCollection::make($artices);
   
       }


    function show(Article $article){

     return ArticleResource::make($article);  

    }
}
