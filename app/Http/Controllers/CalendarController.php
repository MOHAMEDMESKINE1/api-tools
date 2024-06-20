<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use Exception;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $calendars = Calendar::where('title', 'LIKE', "%{$query}%")
                ->orWhere('content', 'LIKE', "%{$query}%")
                ->orWhere('date_time', 'LIKE', "%{$query}%")
                ->get();
        } else {
            $calendars = Calendar::all();
        }

        return CalendarResource::collection($calendars);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CalendarRequest $request)
    {
        try {
            $calendar = Calendar::create($request->validated());
            return new CalendarResource($calendar);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        return new CalendarResource($calendar);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CalendarRequest $request, Calendar $calendar)
    {
        try {
            $calendar->update($request->validated());

            return new CalendarResource($calendar);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        try {
            $calendar->delete();

            return response()->noContent();
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }
}
