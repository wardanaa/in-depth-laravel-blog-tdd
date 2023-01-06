@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">My Blogs
                    <a class="float-right btn btn-sm btn-success" href="{{route('blog.create')}}">Create</a>
                </div>

                <div class="card-body row">
                    @foreach($blogs as $blog)
                    <a href="{{asset('/blog/'.$blog->slug)}}" class="d-flex pb-2 col-10">
                        <img src={{"/storage/{$blog->image}"}} width="50" />
                        <p class="ml-4">
                            {{$blog->title}}
                        </p>
                    </a>
                    <div class="float-right">
                        <a class="btn btn-sm btn-warning" href="{{route('blog.edit',$blog->slug)}}">Edit</a>
                        <a class="btn btn-sm btn-danger" href="{{route('tag.edit',$blog->slug)}}" onclick="event.preventDefault();
                            document.getElementById('delete-form-{{$blog->id}}').submit();">Delete</a>
                    </div>
                    <form id="{{'delete-form-'.$blog->id}}" action="{{ route('blog.destroy',$blog->slug) }}" method="POST" style="display: none;">
                        @csrf
                        @method('delete')
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> @endsection