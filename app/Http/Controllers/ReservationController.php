<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Http\Requests\ReservationRequest;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::id();
        $reservations = Reservation::Where('user_id', $user_id)
                                    ->orderBy('reserved_datetime', 'desc')
                                    ->paginate(config('view.page'));
        
        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        return view('reservations.create', compact('restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request, Restaurant $restaurant): RedirectResponse
    {
        Reservation::create([
            'reserved_datetime' => $request->input('reservation_date') . ' ' . $request->input('reservation_time'),
            'number_of_people' => $request->input('number_of_people'),
            'restaurant_id' => $restaurant->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('reservations.index', $restaurant)->with('flash_message', '予約が完了しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $auth_id = Auth::id();
        if ($auth_id == $reservation->user_id) {
            $reservation->delete();
            return redirect()->route('reservations.index')->with('flash_message', '予約をキャンセルしました。');
        } else {
            return redirect()->route('reservations.index')->with('error_message', '不正なアクセスです。');
        }
    }
}
