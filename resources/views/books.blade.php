@extends('layouts.master')

@section('content')
    <h1>Books</h1>
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
        @foreach ($books as $book)
            <div
                style="border: solid 1px #ccc; margin-bottom: 20px; padding: 20px; background-color: #eee; flex: 0 0 calc(50% - 50px); margin-left: 0px;">
                <h2><a href="{{ route('book', $book->id) }}">{{ $book->name }}</a></h2>
                <p>{{ $book->summary }}</p>
                <div>
                    <h3>Authors</h3>
                    <ul>
                        @foreach ($book->authors as $author)
                            <li>{{ $author->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@endsection
