<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class BookController extends Controller
{


    public function index()
    {
        //$books = Book::all();
        $books = Book::orderBy('title', 'asc')->get();
        return $this->getResponse200($books);
    }

    public function response()
    {
        return [
            "error" => true,
            "message" => "Wrong action!",
            "data" => []
        ];
    }

    public function store(Request $request)
    {
        $reponse = $this->response();
        $isbn = trim($request->isbn);
        $existIsbn = Book::where("isbn", trim($request))->exists();
        if (!$existIsbn) {
            $book = new Book();
            $book->isbn = $isbn;
            $book->title = $request->title;
            $book->description = $request->description;
            $book->published_date = Carbon::now();
            $book->category_id = $request->category["id"];
            $book->editorial_id = $request->editorial["id"];
            $book->save();
            foreach ($request->authors as $item) {
                $book->authors()->attach($item);
            }
            $response["error"] = false;
            $response["message"] = "Your book has been created!";
            $response["data"] = $book;
        } else {
            $response["message"] = "ISBN duplicated!";
        }
        return $response;
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try{
        $reponse = $this->response();
        $book = Book::find($id);
        if ($book) {
            $isbn = trim($request->isbn);
            $isbnOwner = Book::where("isbn", $isbn)->first();

            if ( !$isbnOwner || $isbnOwner->id == $book->id) {
                $book->isbn = $isbn;
                $book->title = $request->title;
                $book->description = $request->description;
                $book->published_date = Carbon::now();
                $book->category_id = $request->category["id"];
                $book->editorial_id = $request->editorial["id"];
                $book->update();
                //Delete
                foreach ($book->authors as $item) {
                    $book->authors()->detach($item->id);
                }
                foreach ($request->authors as $item) {
                    $book->authors()->attach($item);
                }
                $book = Book::with('category', 'editorial', 'authors')->where("id",$id)->get();
                $response["error"] = false;
                $response["message"] = "Your book has been updated!";
                $response["data"] = $book;
            } else {
                $reponse["message"] = "ISBN duplicated!";
            }
        } else {
            $reponse["message"] = "Not found";
        }
        DB::commit();
    }catch(Exception $e){
        DB::rollBack();
    }
        return $response;
    }

    public function show($id){
        $book = Book::find($id);
        return [
            "error" => false,
            "message" => "This is the book!",
            "data" => $book
        ];
    }


}
