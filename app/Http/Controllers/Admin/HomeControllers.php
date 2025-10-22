<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Models\Category;
use App\Models\Tag;

class HomeControllers extends Controller
{
  public function index()
  {
    // $cats = SubCategory::all()->select('name', 'id_main_category');
    // dd($cats);
    $tags = Tag::all();
    return view('admin.index', compact('tags'));
  }
}
