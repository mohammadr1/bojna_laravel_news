<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function show(Category $category)
    {
        $news_category = $category->news()
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

    //   dd($news_category[0]);
    //   $categories = Category::where('status', 1)->withCount('news')->get();
        

        return view('customer.news.category', compact('category', 'news_category'));
    }


}
