<?php

namespace App\Http\Controllers;

use App\Models\Temperature;
use Illuminate\Http\Request;

use League\Fractal\Serializer\JsonApiSerializer;
use App\Transformers\TemperatureTransformer;
use SebastianBergmann\Template\Template;

class TemperatureController extends Controller
{
    public function show()
    {
        $temp = Temperature::where('id',  '>', 0)
            ->orderBy('id', 'desc')
            ->limit(25)
            ->get();
        $result = fractal($temp, new TemperatureTransformer())->serializeWith(new JsonApiSerializer())->toArray();
        return response()->json($result, 200);
    }
}
