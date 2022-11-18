<?php

namespace App\Jobs;
use App\Models\Temperature;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use League\Fractal\Serializer\JsonApiSerializer;
use App\Transformers\TemperatureTransformer;
use Illuminate\Support\Carbon;

class FetchWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function get_html()
    {
        $data = file_get_contents('https://www.gismeteo.ru/weather-kazan-4364/now');
        return $data;
    }

    public function parser_temperature() {

        //создаем объект domDocument
        $dom = new \DOMDocument;
        //мы загрузили html в объектную модель документа
        $dom->loadHTML($this->get_html(), LIBXML_NOERROR);
        // Создает новый объект класса DOMXPath
        $xpath = new \DOMXPath($dom);
       
        $weather0 = $xpath->query('/html/body/section[2]/div[1]/section[2]/div/a[1]/div/div[1]/div[3]/div[1]/span[1]/span[1]/text()');
        $weather = $xpath->query('/html/body/section[2]/div[1]/section[2]/div/a[1]/div/div[1]/div[3]/div[1]/span[1]/text()');

        //делаем текстовое представление контента
        $z0 = $weather0[0]->wholeText;
        $z = $weather[0]->wholeText;
        $t0 = intval($z0);
        $t = intval($z);
        $result = $z0 . $z;
        $result2 = intval($result);
        return $result2;
    }

    // public function get_time() {
    //     $dom = new \DOMDocument;
    //     $dom->loadHTML($this->get_html(), LIBXML_NOERROR);
    //     $xpath = new \DOMXPath($dom);
    //     $weather = $xpath->query('/html/body/section[2]/div[1]/section[2]/div/a[1]/div/div[1]/div[2]/text()');
    //     $time = $weather[0]->wholeText;
    //     return $time;
    // }

    public function get_time() {
        $time = Carbon::now()->toDateTimeString();
        return $time;
    }

    public function handle()
    {
        $temperature = Temperature::create(['value' => $this->parser_temperature(), 'time' => $this->get_time()]);
        $temperatures = fractal($temperature, new TemperatureTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json($temperatures, 200);
    }
}