@extends('layouts.dashboard')
@section('title', 'Categories')
@section('content')
  @foreach ($categories as $categorie)
      <tr>
        <th scope="row">{{$categorie->id}}</th>
        <td colspan="2">{{$categorie->name}}</td>
        <td>{{$categorie->slug}}</td>
        <td>
          <a href="{{ route('admin.categories.edit', $categorie->id) }}" class="btn btn-primary">Edit</a>
          <form action="{{ route('admin.categories.destroy', $categorie->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
          </form>
      </tr>
  @endforeach
@endsection