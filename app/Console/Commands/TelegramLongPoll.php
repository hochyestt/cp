<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use Telegram\Bot\HttpClients\GuzzleHttpClient;
use Telegram\Bot\Api;
use App\Models\Task;
use App\Models\Habit;

class TelegramLongPoll extends Command
{
    protected $signature = 'telegram:poll {--limit=10} {--timeout=0}';
    protected $description = 'Listen for Telegram updates using Long Polling';

    public function handle()
    {
        $this->info('Starting Telegram Long Polling...');

        // Настройка тг апи
        $guzzle = new GuzzleClient(['verify' => false]);
        $httpClient = new GuzzleHttpClient($guzzle);
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'), false, $httpClient);

        $lastUpdateId = 0;

        while (true) {
            try {
                $updates = $telegram->getUpdates([
                    'offset' => $lastUpdateId + 1,
                    'limit' => (int)$this->option('limit'),
                    'timeout' => (int)$this->option('timeout'),
                ]);

                foreach ($updates as $update) {
                    $lastUpdateId = $update->getUpdateId();
                    Log::info('Telegram Update Received (Polling):', $update->toArray());
                    $this->info('New update received: ' . $update->getUpdateId());

                    // текстовое соо
                    if ($update->getMessage()) {
                        $chatId = $update->getMessage()->getChat()->getId();
                        $text = $update->getMessage()->getText();

                        // Простейшее эхо
                        $telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => "Привет! Ты сказал: \"" . $text . "\". Я получил твоё сообщение! (Long Polling)"
                        ]);
                    }

                    // Обработка кнопок «Выполнено» 
                    if ($update->getCallbackQuery()) {
                        $callback = $update->getCallbackQuery();
                        $data = $callback->getData();
                        $chatId = $callback->getMessage()->getChat()->getId();

                        // Задачи
                        if (str_starts_with($data, 'task_done_')) {
                            $taskId = str_replace('task_done_', '', $data);
                            $task = Task::find($taskId);
                            if ($task) {
                                $task->update(['completed' => true]);

                                // Если есть интеграция с Yandex Calendar, здесь можно удалить событие:
                                // $this->deleteYandexEvent($task->yandex_event_id);

                                $telegram->answerCallbackQuery([
                                    'callback_query_id' => $callback->getId(),
                                    'text' => 'Задача отмечена как выполнена ✅'
                                ]);
                            }
                        }

                        // Привычки
                        if (str_starts_with($data, 'habit_done_')) {
                            $habitId = str_replace('habit_done_', '', $data);
                            $habit = Habit::find($habitId);
                            if ($habit) {
                                $habit->update(['last_done_at' => now()]);

                                $telegram->answerCallbackQuery([
                                    'callback_query_id' => $callback->getId(),
                                    'text' => 'Привычка отмечена как выполнена ✅'
                                ]);
                            }
                        }
                    }
                }

            } catch (\Exception $e) {
                $this->error('Error during Telegram polling: ' . $e->getMessage());
                Log::error('Telegram Polling Error:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                sleep(5);
            }
        }
    }

    /**
     * Пример функции для удаления события Yandex Calendar.
     * Пока оставлено как заглушка, подключаем при интеграции.
     */
    private function deleteYandexEvent($eventId)
    {
        if (!$eventId) return;

        $client = new GuzzleClient();
        $client->delete("https://calendar.yandex.ru/api/v2/events/{$eventId}", [
            'headers' => [
                'Authorization' => 'OAuth ' . env('YANDEX_TOKEN')
            ]
        ]);
    }
}
