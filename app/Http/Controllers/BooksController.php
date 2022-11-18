<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

use League\Fractal\Serializer\JsonApiSerializer;
use App\Transformers\BookTransformer;


class BooksController extends Controller
{
    use SoftDeletes;

    public function create(Request $request)
    {
        $authors = Author::pluck('id');
        $validator = Validator::make($request->all(), [
            'author_id' => ['required', 'numeric', Rule::In($authors)],
            'name' => 'required|between:2,50',
            'year' => 'required|integer|between:1,3000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }
        $book = Book::create(array_merge(
            $validator->validated(),
        ));
        $book = fractal($book, new BookTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json($book, 201);
    }

    public function index(Request $request, $id) {

        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'year' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 401);
        }

        $books = Book::find($id);
        if ($books == null)
            return response()->json([],404);

        $book = fractal($books, new BookTransformer())->serializeWith(new JsonApiSerializer());
        return $book;
    }

    public function show() {

        //table - вызываем на исполнение данный метод
        //в качестве первого аргумента вызываем таблицу авторы
        //метод гет выбирает всю информацию из данной таблицы и возращает массив объектов
        $query = Book::query('books')->get();

        $books = fractal($query, new BookTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json($books);
    }

    public function filter(Request $request) {

        $query = Book::query();

        if (isset($request['name'])) {
            $query->where('books.name', 'like', "%{$request['name']}%");
        }
        
        if (isset($request['year_from'])) {
            $query->where('books.year', '>=', "{$request['year_from']}%");
        }

        if (isset($request['year_to'])) {
            $query->where('books.year', '<=', "{$request['year_to']}%");
        }

        if (isset($request['author_id'])) {
            $query->where('books.author_id', $request['author_id']);
        }

        if (isset($request['order_by']) && isset($request['order_direct'])) {
            $order_by=$request['order_by'];
            $order_direct=$request['order_direct'];

            if ($order_by != 'id' && $order_by != 'name' && $order_by != 'author_id' && $order_by != 'year') {
                return response()->json(['message' => 'Возможные значения для order_by = name , id, author_id, year'], 400);
            }
            if ($order_direct != 'asc' && $order_direct != 'desc') {
                return response()->json(['message' => 'Возможные значения для order_direct = desc или asc'], 400);
            }
            $query = Book::query('books')
            ->orderBy($order_by, $order_direct);
        }

        $query = $query->first();
        $query = fractal($query, new BookTransformer())
            ->parseIncludes($request->get('author'))
            ->serializeWith(new JsonApiSerializer());
        return response()->json([$query], 201);
    }


    public function update(Request $request, int $id)
    {  
        $authors = Author::pluck('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,255',
            'author_id' => ['required', 'numeric', Rule::In($authors)],
            'year' => 'required|integer|between:1,3000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $books = Book::find($id);
        if ($books == null)
            return response()->json([],404);
        $books->update(array_merge($validator->validated(),));

        $book = fractal($books, new BookTransformer())->serializeWith(new JsonApiSerializer());
        return $book;
    }

    public function destroy(int $id) {
        $book  = Book::find($id);
        if ($book == null)
            return response()->json(['Error' => "Book does not exist"], 404);
        $book->delete();
        return response()->json([], 204);
    }

    public function delete_many(Request $request) {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
            'ids.*' => 'required|integer'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }
    
        $books = request()->all();
        Book::destroy($books['ids']);
    
        return response()->json([], 204);
    }
}
