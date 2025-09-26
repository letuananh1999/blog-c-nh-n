<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;

class HomeControllers extends Controller
{
  public function index()
  {
    // $cats = SubCategory::all()->select('name', 'id_main_category');
    $cats = Category::orderBy('id', 'asc')->paginate(5);
    // dd($cats);
    return view('admin.index', compact('cats'));
  }
}
