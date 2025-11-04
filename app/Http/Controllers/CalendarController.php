<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Services\YandexCalendarService;
use Inertia\Inertia;


class CalendarController extends Controller
{
    protected $calendarService;

    public function __construct(YandexCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

   
    public function index()
    {
        return Inertia::render('Calendar');
    }

    
    public function getEvents(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->input('start_date'))->startOfDay()->toRfc3339String();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay()->toRfc3339String();

        $eventsData = $this->calendarService->getEvents($startDate, $endDate);

        if ($eventsData === null) {
            return response()->json([
                'error' => 'Не удалось получить события из Яндекс.Календаря. Проверьте токен и логи.'
            ], 500);
        }

        return response()->json($eventsData);
    }
}
