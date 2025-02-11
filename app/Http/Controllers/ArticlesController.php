<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
        // Get the 'per_page' query parameter or set a default value
        $perPage = $request->get('per_page', 10);
        
        // Fetch articles with pagination
        $articles = Article::paginate($perPage);

        return response()->json($articles);
    }
}