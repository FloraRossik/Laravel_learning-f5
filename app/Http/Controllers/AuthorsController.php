<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use League\Fractal\Serializer\JsonApiSerializer;
use App\Transformers\AuthorTransformer;


class AuthorsController extends Controller
{ 
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,50',
            'year' => 'required|integer|between:1,3000,'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $Author = Author::create(array_merge(
            $validator->validated(),
        ));
        $Authors = fractal($Author, new AuthorTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json($Authors, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    public function index($author_id)  {

        $author = Author::join('books', 'authors.id', '=', 'books.author_id')
            ->groupBy('books.author_id')
            ->selectRaw('count(books.id) as books_count, authors.*')
            ->where('author_id', '=', $author_id)->first();
        
        if ($author_id == 0)
            return response()->json([],404);
        
        $authors = fractal($author, new AuthorTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json($authors);
    }

    public function show() {

        //table - вызываем на исполнение данный метод
        //в качестве первого аргумента вызываем таблицу авторы
        //метод гет выбирает всю информацию из данной таблицы и возращает массив объектов
            $author = Author::join('books', 'authors.id', '=', 'books.author_id')   // DB::table('authors')
                ->groupBy('books.author_id')
                ->selectRaw('count(books.id) as books_count, authors.*')
                ->get();
        
            $authors = fractal($author, new AuthorTransformer())->serializeWith(new JsonApiSerializer());
            return response()->json($authors);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request) {

        $query = Author::query('authors');

        if (isset($request['name'])) {
            $query->where('authors.name', 'like', "%{$request['name']}%");
        }

        if (isset($request['year_from'])) {
            $query->where('authors.year', '>=', "{$request['year_from']}%");
        }

        if (isset($request['year_to'])) {
            $query->where('authors.year', '<=', "{$request['year_to']}%");
        }

        if (isset($request['book_count_from'])) {
            $query = Author::query()
            ->selectRaw('count(books.id) as books_count, authors.*')
            ->join('books', 'authors.id', '=', 'books.author_id')
            ->groupBy('books.author_id');
            $query->havingRaw('count_books >= ?', [$request['book_count_from']]);
        }

        if (isset($request['book_count_to'])) {
            $query = Author::query()
            ->selectRaw('count(books.id) as count_books, authors.*')
            ->join('books', 'authors.id', '=', 'books.author_id')
            ->groupBy('books.author_id');
            $query->havingRaw('count_books <= ?', [$request['book_count_to']]);
        }

        // sort
        if (isset($request['order_by']) && isset($request['order_direct'])) {
            $order_by=$request['order_by'];
            $order_direct=$request['order_direct'];

            if ($order_by != 'id' && $order_by != 'name') {
                return response()->json(['message' => 'Возможные значения для order_by = name или id'], 400);
            }
            if ($order_direct != 'asc' && $order_direct != 'desc') {
                return response()->json(['message' => 'Возможные значения для order_direct = desc или asc'], 400);
            }
            $query = Author::query('authors')
            ->orderBy($order_by, $order_direct);
        }

        $query = $query->first();
        $query = fractal($query, new AuthorTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json(['data'=> $query], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,50',
            'year' => 'required|integer|between:1,3000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $authors = Author::find($id);
        if ($authors == null)
            return response()->json([],404);
        $authors->update(array_merge($validator->validated(),));

        $author = fractal($authors, new AuthorTransformer())->serializeWith(new JsonApiSerializer());
        return $author; 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request){

        $author = Author::findOrFail($request->id);

        if ($author->delete() == false) {
            return response()->json([
                "Can't delete the author with id"
            ], 404);
        }
        return response()->json([
            "id" => $request->id,
            "deleted" => true,
        ], 204);
        }
}
