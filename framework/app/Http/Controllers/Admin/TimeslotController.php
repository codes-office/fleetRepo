<?php

namespace App\Http\Controllers\Admin;

use App\Model\Timeslot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class TimeslotController extends Controller
{
    /** 
     * Display a listing of the resource.
     */
    public function index()
    {
        $timeslots = Timeslot::with('user')->get(); // Eager load user relationship
        return view('timeslots.index', compact('timeslots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('timeslots.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'pickup_time' => 'required|date_format:H:i',
        'drop_time' => 'required|date_format:H:i|after:pickup_time',
    ]);
Log::info($request->all());
    Timeslot::create([
        'user_id' => auth()->id(),
        'pickup_time' => $request->pickup_time,
        'drop_time' => $request->drop_time,
    ]);

    return redirect()->route('timeslots.index')->with('success', 'Timeslot created successfully.');
}   
    /**
     * Display the specified resource.
     */
    public function show(Timeslot $timeslot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $timeslot = Timeslot::findOrFail($id);
        return view('timeslots.edit', compact('timeslot'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'pickup_time' => 'required|date_format:H:i',
            'drop_time' => 'required|date_format:H:i|after:pickup_time',
		]);
        Log::info($request->all());
        if ($validation->fails()) {
            Log::info('Validation failed');
            return redirect()->back()->withErrors($validation)->withInput();
        }else{
            Log::info('Validation passed');
        $timeslot = Timeslot::findOrFail($id);
        $timeslot->update([
            'pickup_time' => $request->pickup_time,
            'drop_time' => $request->drop_time,
        ]);
        return redirect()->route('timeslots.index')->with('success', 'Timeslot updated successfully.');}
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $timeslot = Timeslot::findOrFail($id);
        $timeslot->delete();
    
        return redirect()->route('timeslots.index')->with('success', 'Timeslot deleted successfully.');
    }
    
}
