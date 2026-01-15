<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use App\Services\UserService;

class HomeControllers extends Controller
{
  protected UserService $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }


  public function index()
  {
            $users = $this->userService->getUsers('all', 5);

    return view('admin.index', compact('users'));
  }

}
