<?php

namespace Tests\Feature;

use App\Jobs\FetchWeatherJob;
use App\Jobs\Weather;

use App\Models\Temperature;
use Tests\TestCase;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use League\Fractal\Serializer\JsonApiSerializer;
use App\Transformers\TemperatureTransformer;

use App\Jobs\FetcWeatherJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeatherTest extends TestCase
{ 
    use RefreshDatabase;
    
    public function get_value() {
        $data = file_get_contents('https://www.gismeteo.ru/weather-kazan-4364/now');
        $dom = new \DOMDocument;
        $dom->loadHTML($data, LIBXML_NOERROR);
        $xpath = new \DOMXPath($dom);
        $weather0 = $xpath->query('/html/body/section[2]/div[1]/section[2]/div/a[1]/div/div[1]/div[3]/div[1]/span[1]/span[1]/text()');
        $weather = $xpath->query('/html/body/section[2]/div[1]/section[2]/div/a[1]/div/div[1]/div[3]/div[1]/span[1]/text()');

        $z0 = $weather0[0]->wholeText;
        $z = $weather[0]->wholeText;
        $t = intval($z);
        $result = $z0 . $t;
        return $result;
    }

    // public function get_time() {
    //     $data = file_get_contents('https://www.gismeteo.ru/weather-kazan-4364/now');
    //     $dom = new \DOMDocument;
    //     $dom->loadHTML($data, LIBXML_NOERROR);
    //     $xpath = new \DOMXPath($dom);
    //     $weather = $xpath->query('/html/body/section[2]/div[1]/section[2]/div/a[1]/div/div[1]/div[2]/text()');
    //     $time = $weather[0]->wholeText;
    //     return $time;
    // }
    
    // public function test_ability_temperature_value() {
    //     $response = $this->getJson('/api/weather', [
    //         //'time' => $this->get_time(),
    //         'value' => $this->get_value(),
    //     ]);
    //     $value = $response->json()['data']['attributes']['value'];
    //     if ($value > '-50')
    //         $response->assertStatus(200);
    // }

    // public function test_ability_temperature_time() {
    //     $response = $this->getJson('/api/temperature', [
    //         'time' => $this->get_time(),
    //         'value' => $this->get_value(),
    //     ]);
    //     dump($response->json());
    //     $time = $response->json()['Current temperature']['data']['attributes']['time'];
    //     if ($time == '00:00')
    //         $response->assertStatus(200);
    //}


    public function test_example()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_not_pushed_Temperature_Get()
    {
        Queue::fake();
        Queue::assertNotPushed(AnotherWeatherParsing::class);
    }

    // public function test_Temperature_Get()
    // {
    //     $result = (new Weather())->handle();
    //     $this->assertInstanceOf('App\Models\Temperature', $result);
    // }

    // public function test_Temperature_parser()
    // {
    //     $result = (new Weather())->parser_temperature();
    //     $this->assertIsNumeric($result);
    // }

    public function test_Temperature_get_html()
    {
        $result = (new FetchWeatherJob())->get_html();
        $this->assertIsString($result);
    }
}