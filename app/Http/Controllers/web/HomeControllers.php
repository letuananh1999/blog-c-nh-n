<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Category;

class HomeControllers extends Controller
{
  public function index()
  {
    // $cats = SubCategory::all()->select('name', 'id_main_category');
    $cats = Category::orderBy('id', 'asc')->paginate(5);
    // dd($cats);
    return view('home', compact('cats'));
  }
  public function about()
  {
    return view('web.about');
  }
}
