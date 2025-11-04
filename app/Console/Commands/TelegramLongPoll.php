<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;
use App\Models\User;
use App\Models\Task;
use App\Models\Habit;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Exceptions\TelegramOtherException;

class TelegramLongPoll extends Command
{
    protected $signature = 'telegram:poll {--limit=10} {--timeout=30}';
    protected $description = 'Запуск Telegram бота через Long Polling с уведомлениями и кнопками выполнения';

    private Api $telegram;
    private int $lastUpdateId = 0;
    private ?int $botId = null;

    public function handle(): int
    {
        $this->info('🚀 Старт Telegram Long Polling...');

        if (!$this->checkToken()) return 1;

        $this->initTelegram();
        $this->setBotId();
        $this->initLastUpdateId();

        $this->info('Для остановки бота нажмите Ctrl+C');

        while (true) {
            try {
                // Обработка уведомлений о задачах и привычках
                $this->processNotifications();
                // Обработка входящих сообщений и нажатий кнопок
                $this->processUpdates();
            } catch (\Exception $e) {
                $this->error("❌ Ошибка в цикле поллинга: " . $e->getMessage());
                Log::error('Polling Error', ['error' => $e->getMessage()]);
            }

            // Небольшая пауза между циклами уведомлений/обновлений
            sleep(1);
        }

        return 0;
    }

    private function checkToken(): bool
    {
        if (empty(env('TELEGRAM_BOT_TOKEN'))) {
            $this->error('❌ TELEGRAM_BOT_TOKEN не найден в .env');
            return false;
        }
        return true;
    }

    private function initTelegram(): void
    {
        $guzzle = new GuzzleClient(['verify' => (bool)env('TELEGRAM_SSL_VERIFY', true)]);
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'), false, new GuzzleHttpClient($guzzle));
        $this->info('Бот подключен к Telegram API');
    }

    private function setBotId(): void
    {
        try {
            $me = $this->telegram->getMe();
            $this->botId = $me->getId();
            $this->info("ID бота: {$this->botId}, ник: @{$me->getUsername()}");
        } catch (\Exception $e) {
            $this->error("Не удалось получить ID бота: " . $e->getMessage());
        }
    }

    private function initLastUpdateId(): void
    {
        try {
            $updates = $this->telegram->getUpdates(['limit' => 1]);
            if (!empty($updates)) {
                $this->lastUpdateId = $updates[0]->getUpdateId();
                $this->info("Начальный lastUpdateId: {$this->lastUpdateId}");
            } else {
                $this->info("Нет предыдущих апдейтов. Начинаем с нуля.");
            }
        } catch (\Exception $e) {
            $this->error("Ошибка получения начального Update ID: " . $e->getMessage());
        }
    }

    // -------------------- Уведомления --------------------

    private function processNotifications(): void
    {
        $this->processTasks();
        $this->processHabits();
    }

    private function processTasks(): void
    {
        $now = Carbon::now();
        // Находим задачи, время которых настало и по которым еще не было уведомления
        $tasks = Task::where('start_time', '<=', $now)
            ->where('notified', false)
            ->where(function ($q) {
                $q->where('completed', false)->orWhereNull('completed');
            })
            ->with('user')
            ->get();

        foreach ($tasks as $task) {
            $this->sendTaskNotification($task);
        }
    }

    /**
     * Отправляет уведомление о задаче с кнопкой "Выполнено".
     */
    private function sendTaskNotification(Task $task): void
    {
        if (!$task->user?->telegram_id) return;

        $chatId = $task->user->telegram_id;
        
        // Создание inline-клавиатуры
        $keyboard = [
            [
                [
                    'text' => '✅ ',
                    'callback_data' => 'task_done_' . $task->id, // Данные для обратного вызова
                ],
            ],
        ];

        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "🔔 **Напоминание о задаче!** 🔔\n\n" .
                    "**Задача:** `{$task->title}`\n" .
                    "**Начало:** " . $task->start_time->format('H:i d.m.Y') . "\n\n" .
                    "**Описание:** " . ($task->description ?? 'Нет описания'),
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]), // Добавляем клавиатуру
            ]);

            $task->update(['notified' => true]);
            $this->info("✅ Задача #{$task->id} уведомлена пользователю {$chatId}");
        } catch (\Exception $e) {
            $this->error("❌ Не удалось отправить уведомление о задаче #{$task->id}: " . $e->getMessage());
            Log::error('Task Notification Error', ['task_id' => $task->id, 'error' => $e->getMessage()]);
        }
    }

    private function processHabits(): void
    {
        $now = Carbon::now();
        // Находим привычки, для которых настало время следующего уведомления
        $habits = Habit::where('next_notification', '<=', $now)
            ->with('user')
            ->get();

        foreach ($habits as $habit) {
            $habit->checkAndResetCounter();
            // Отправляем уведомление только если привычка еще не выполнена за текущий период
            if (!$habit->isCompletedForCurrentPeriod()) {
                $this->sendHabitNotification($habit);
            } else {
                // Если выполнена, просто сдвигаем следующее уведомление
                $habit->update(['next_notification' => $habit->calculateNextNotificationTime()]);
            }
        }
    }

    /**
     * Отправляет уведомление о привычке с кнопкой "Сделано".
     */
    private function sendHabitNotification(Habit $habit): void
    {
        if (!$habit->user?->telegram_id) return;

        $chatId = $habit->user->telegram_id;
        
        // Создание inline-клавиатуры
        $keyboard = [
            [
                [
                    'text' => '✨ Я это сделал(а)!',
                    'callback_data' => 'habit_done_' . $habit->id, // Данные для обратного вызова
                ],
            ],
        ];


        try {
            $frequencyText = "{$habit->times_done_since_reset} из {$habit->frequency_value} раз в " .
                ($habit->frequency_type === Habit::FREQUENCY_DAY ? 'день' : 'неделю');

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "⭐ **Время для привычки!** ⭐\n\n" .
                    "**Привычка:** `{$habit->name}`\n" .
                    "**Выполнено:** {$frequencyText}",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]), // Добавляем клавиатуру
            ]);

            // Сдвигаем следующее уведомление на час, чтобы не спамить
            $habit->update(['next_notification' => now()->addHour()]);
            $this->info("✅ Привычка #{$habit->id} уведомлена пользователю {$chatId}");
        } catch (\Exception $e) {
            $this->error("❌ Не удалось отправить уведомление о привычке #{$habit->id}: " . $e->getMessage());
            Log::error('Habit Notification Error', ['habit_id' => $habit->id, 'error' => $e->getMessage()]);
        }
    }

    // -------------------- Long Polling --------------------

    private function processUpdates(): void
    {
        try {
            $updates = $this->telegram->getUpdates([
                'offset' => $this->lastUpdateId + 1,
                'limit' => (int)$this->option('limit'),
                'timeout' => (int)$this->option('timeout'),
            ]);

            if (empty($updates)) {
                $this->info("⏳ Нет новых сообщений...");
            }

            foreach ($updates as $update) {
                $this->lastUpdateId = $update->getUpdateId();

                if ($update->getMessage()) {
                    $this->handleMessage($update->getMessage());
                }

                if ($update->getCallbackQuery()) {
                    $this->handleCallback($update->getCallbackQuery());
                }
            }
        } catch (TelegramOtherException $e) {
            if (str_contains($e->getMessage(), 'Conflict')) {
                $this->warn('⚠️ Конфликт Long Polling: возможно бот уже запущен.');
            } else {
                $this->error('❌ Ошибка Telegram API: ' . $e->getMessage());
                Log::error('Telegram API Error', ['error' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            $this->error('❌ Ошибка при обработке апдейтов: ' . $e->getMessage());
            Log::error('Update Processing Error', ['error' => $e->getMessage()]);
        }
    }

    private function handleMessage(Message $message): void
    {
        $from = $message->getFrom();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();

        // Игнорируем сообщения, отправленные самим ботом
        if ($this->botId !== null && $from->getId() === $this->botId) return;

        $this->info("💬 {$from->getFirstName()} ({$chatId}): {$text}");

        if (str_starts_with($text, '/start')) {
            $this->handleStartCommand($chatId, $text, $from);
        } else {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Я не понимаю команду. Используй `/start <ID>` для связи с аккаунтом.",
                'parse_mode' => 'Markdown',
            ]);
        }
    }

    private function handleCallback(CallbackQuery $callback): void
    {
        $data = $callback->getData();
        $chatId = $callback->getMessage()->getChat()->getId();

        // Проверяем, что колбэк пришел с кнопки задачи
        if (str_starts_with($data, 'task_done_')) {
            $this->handleTaskDone($callback, $chatId, $data);
        } 
        // Проверяем, что колбэк пришел с кнопки привычки
        elseif (str_starts_with($data, 'habit_done_')) {
            $this->handleHabitDone($callback, $chatId, $data);
        }
    }

    private function handleStartCommand(int $chatId, string $text, $from): void
    {
        $parts = explode(' ', $text);
        $userId = $parts[1] ?? null; // ID пользователя, переданный после /start

        // Поиск пользователя по ID из команды или по telegram_id
        $user = $userId ? User::find($userId) : User::where('telegram_id', $chatId)->first();
        // Fallback: если не нашли, берем первого (возможно, для тестирования)
        if (!$user) $user = User::first(); 

        if ($user) {
            $user->telegram_id = $chatId;
            $user->save();

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "✅ Ваш Telegram привязан к пользователю ID {$user->id}",
            ]);

            $this->info("Пользователь {$user->id} привязан к Telegram {$chatId}");
        }
    }

    /**
     * Обрабатывает нажатие кнопки "Выполнено" для задачи.
     */
    private function handleTaskDone(CallbackQuery $callback, int $chatId, string $data): void
    {
        $taskId = (int)str_replace('task_done_', '', $data);
        $task = Task::find($taskId);
        
        if (!$task) {
            $this->telegram->answerCallbackQuery(['callback_query_id' => $callback->getId(), 'text' => 'Задача не найдена 🧐']);
            return;
        }

        $task->update(['completed' => true]);
        
        // Обновляем сообщение, убирая клавиатуру и добавляя статус
        $this->telegram->editMessageText([
            'chat_id' => $chatId,
            'message_id' => $callback->getMessage()->getMessageId(),
            'text' => $callback->getMessage()->getText() . "\n\n✅ **Отмечено как выполненное!**",
            'parse_mode' => 'Markdown',
            'reply_markup' => null, // Удаляем клавиатуру
        ]);
        
        $this->telegram->answerCallbackQuery(['callback_query_id' => $callback->getId(), 'text' => 'Задача выполнена! 🎉']);
        $this->info("Задача #{$taskId} выполнена пользователем {$chatId} через кнопку.");
    }

    /**
     * Обрабатывает нажатие кнопки "Сделано" для привычки.
     */
    private function handleHabitDone(CallbackQuery $callback, int $chatId, string $data): void
    {
        $habitId = (int)str_replace('habit_done_', '', $data);
        $habit = Habit::find($habitId);
        
        if (!$habit) {
            $this->telegram->answerCallbackQuery(['callback_query_id' => $callback->getId(), 'text' => 'Привычка не найдена 🧐']);
            return;
        }

        $habit->times_done_since_reset++;
        $habit->last_done_at = now();
        $habit->next_notification = $habit->calculateNextNotificationTime();
        $habit->save();
        
        // Обновляем текст сообщения, чтобы показать текущий прогресс
        $frequencyText = "{$habit->times_done_since_reset} из {$habit->frequency_value} раз в " .
            ($habit->frequency_type === Habit::FREQUENCY_DAY ? 'день' : 'неделю');

        $newText = "⭐ **Время для привычки!** ⭐\n\n" .
                   "**Привычка:** `{$habit->name}`\n" .
                   "**Выполнено:** {$frequencyText} (Отмечено)";

        // Обновляем сообщение, чтобы оно отображало новый счетчик (или убираем кнопку, если лимит достигнут)
        $newKeyboard = [];
        if (!$habit->isCompletedForCurrentPeriod()) {
            $newKeyboard = [
                [
                    [
                        'text' => '✨ Я это сделал(а)! (Повторно)',
                        'callback_data' => 'habit_done_' . $habit->id,
                    ],
                ],
            ];
        } else {
             $newText .= "\n\n🎉 **Цель достигнута!**";
        }

        $this->telegram->editMessageText([
            'chat_id' => $chatId,
            'message_id' => $callback->getMessage()->getMessageId(),
            'text' => $newText,
            'parse_mode' => 'Markdown',
            'reply_markup' => empty($newKeyboard) ? null : json_encode(['inline_keyboard' => $newKeyboard]),
        ]);

        $this->telegram->answerCallbackQuery(['callback_query_id' => $callback->getId(), 'text' => 'Отлично! Продолжай в том же духе! ✨']);
        $this->info("Привычка #{$habitId} отмечена пользователем {$chatId} через кнопку.");
    }
}
