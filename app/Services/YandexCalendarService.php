<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log; 

class YandexCalendarService
{
    protected const BASE_URI = 'https://calendar-api.yandex.ru/api/v1/'; 

    protected $token;
    protected $client;

    public function __construct()
    {
        $this->token = env('YANDEX_TOKEN'); 

        if (!$this->token) {
            Log::error('YandexCalendarService: Отсутствует YANDEX_TOKEN в .env');
        }

        $this->client = new GuzzleClient([
            'base_uri' => self::BASE_URI,
            'headers' => [
                'Authorization' => 'OAuth ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            // 'verify' => false, // ❌ Убрать 'verify' => false на продакшене!
        ]);
    }
    
   
    public function getEvents(string $start, string $end): ?array
    {
        $data = [
            'type' => 'event', 
            'from' => $start,
            'to' => $end,
            'limit' => 500, 
        ];

        try {
            $response = $this->client->post('events', [
                'json' => $data,
            ]);

            $content = $response->getBody()->getContents();
            $result = json_decode($content, true);

            if (isset($result['events']) && is_array($result['events'])) {
                return [
                    'events' => $result['events']
                ];
            }
            
            Log::error('Yandex Calendar fetch events error: Неправильный формат ответа.', ['response' => $result]);
            return null;

        } catch (RequestException $e) {
            $responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'Нет ответа';
            Log::error('Yandex Calendar fetch events error: ' . $e->getMessage(), ['body' => $responseBody]);
            return null;
        } catch (\Exception $e) {
             Log::critical('Yandex Calendar critical error: ' . $e->getMessage());
             return null;
        }
    }
 
    public function createEvent($title, $description, $start, $end = null)
    {
        $data = [
            'title' => $title,
            'description' => $description,
            'start_time' => $start,
        ];

        if ($end) {
            $data['end_time'] = $end;
        }

        try {
            $response = $this->client->post('events', [
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Yandex Calendar create error: ' . $e->getMessage());
            return null;
        }
    }

  
    public function deleteEvent($eventId)
    {
        if (!$eventId) return;

        try {
            $this->client->delete("events/{$eventId}");
        } catch (RequestException $e) {
            Log::error('Yandex Calendar delete error: ' . $e->getMessage());
        }
    }
}
