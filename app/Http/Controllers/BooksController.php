<?php

namespace App\Http\Controllers;

use App\Models\Book;
use OpenTelemetry\API\Trace\Span;

class BooksController extends Controller
{
    public function index()
    {
        $books = Book::with('authors')->get();
        return view('books', compact('books'));
    }

    public function book(int $id)
    {
        $book = Book::with('authors')->find($id);
        Span::getCurrent()->setAttribute('book.id', $book->id);
        Span::getCurrent()->setAttribute('book.name', $book->name);
        return view('book', compact('book'));
    }
}
