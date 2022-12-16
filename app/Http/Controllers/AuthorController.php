<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        //$books = Book::all();
        $authors = Author::orderBy('name', 'asc')->get();
        if($authors){
            return $this->getResponse200($authors);
        }else{
            return $this->getResponse500();
        }

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
        try{
        $reponse = $this->response();
            $author = new Author();
            $author->name = $request->name;
            $author->first_surname = $request->first_surname;
            $author->second_surname = $request->second_surname;

            $author->save();

            return $this->getResponse201('user acciunt', 'create',$author);

    }catch(Exception $e){
        DB::rollBack();
        return $this->getResponse500();
    }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try{
        $reponse = $this->response();
        $author = Author::find($id);


                $author->name = $request->name;
                $author->first_surname = $request->first_surname;
                $author->second_surname = $request->second_surname;
                $author->update();

        DB::commit();
        return $this->getResponse201('user acciunt', 'update',$author);

    }catch(Exception $e){
        DB::rollBack();
        return $this->getResponse500();
    }

    }

    public function destroy(Request $request,$id)
    {
        $response = $this->response();
        $autor = Author::Find($id);
        if($autor){
            $autor->delete();
            return $this->getResponse200("autor","deleted",$autor);
        }else{
            return $this->getResponse404();
        }

    }

    public function show($id){
        $author = Author::find($id);
        return [
            "error" => false,
            "message" => "This is the Author!",
            "data" => $author
        ];
    }


}
