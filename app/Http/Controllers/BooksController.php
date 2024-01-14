<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Exception;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\Context\Context;

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
        Span::getCurrent()->setAttribute('id', $book->id);
        Span::getCurrent()->setAttribute('name', $book->name);
        return view('book', compact('book'));
    }
}
