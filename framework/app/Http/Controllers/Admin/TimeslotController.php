<?php

namespace App\Http\Controllers\Admin;

use App\Model\Timeslot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Model\User;
use App\Model\UserData;
use Auth;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Schema;

class TimeslotController extends Controller
{
    /** 
     * Display a listing of the resource.
     */
    public function index()
{

    // Schema::table('timeslots', function (Blueprint $table) {
    //     $table->foreign('company_id')->references('id')->on('users')->onDelete('cascade');
    // });
    
    $user = auth()->user();

    if ($user->user_type == 'S') {
        // Super admin sees all timeslots
        $timeslots = Timeslot::with(['user', 'company'])->get();
    } else {
        // Other users see only their company's timeslots
        $timeslots = Timeslot::with(['user', 'company'])
            ->where('company_id', $user->company_id)
            ->get();
    }
    dd($timeslots->toArray());

    return view('timeslots.index', compact('timeslots'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
    
        // Initialize an empty array to store data to pass to the view
        $data = [];
    
        // Check the user_type and fetch customers only for super admin
        if ($user->user_type === 'S') {
            // Fetch customers for super admin
            $customerIds = UserData::where('key', 'client')
                                   ->where('value', 1)
                                   ->pluck('user_id')
                                   ->toArray();
            $data['customers'] = User::whereIn('id', $customerIds)->get();
        }
        $data['user_id'] = $user->id;
    
        return view('timeslots.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //  dd($request->all());
        // exit;

        // Validate incoming request
        $request->validate([
            'company_id' => function ($attribute, $value, $fail) {
                // Check if the authenticated user is of type 's'
                if (auth()->user() && auth()->user()->user_type === 's') {
                    if (empty($value)) {
                        $fail('The company_id field is required when user_type is "s".');
                    }
                }
            },
            'from_time' => 'required|date_format:H:i',
            'to_time' => 'required|date_format:H:i|after:from_time',
            'days_available' => 'required|array', // Ensure days_available is an array
            'log' => 'required|in:Login,Logout',  // Ensure the log value is either 'Login' or 'Logout'
            'Active' => 'required|in:0,1',       // Ensure Active is either true or false
        ]);
    
        // Set company_id based on user type
        $companyId = $request->company_id;
    
        if (empty($companyId) && auth()->user()->user_type === 'O') {
            // If company_id is not provided and user_type is 'o', save user_id as company_id
            $companyId = auth()->id();
        }
    
        // Convert the days_available array to JSON format
        $daysAvailableJson = json_encode($request->days_available);
    
        // Create a new timeslot record
        Timeslot::create([
            'user_id' => auth()->id(),      // User ID from the request
            'company_id' => $companyId,     // Company ID from the request
            'active' => $request->Active,   // Active status from the request
            'log' => $request->log,         // Log type (login/logout)
            'from_time' => $request->from_time, // From time from the request
            'to_time' => $request->to_time,     // To time from the request
            'days_available' => $daysAvailableJson, // Store days_available as JSON
        ]);
    
        Log::info('Timeslot Created:', [
            'Request Data' => $request->all(),
        ]);
    
        // Redirect back with a success message
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
        $user = Auth::user();
    
        // Initialize an empty array to store data to pass to the view
        $data = [];
    
        // Check the user_type and fetch customers only for super admin
        if ($user->user_type === 'S') {
            // Fetch customers for super admin
            $customerIds = UserData::where('key', 'client')
                                   ->where('value', 1)
                                   ->pluck('user_id')
                                   ->toArray();
            $data['customers'] = User::whereIn('id', $customerIds)->get();
        }
        $data['user_id'] = $user->id;
        $timeslot = Timeslot::findOrFail($id);
   
// Decode the days_available field if it's a JSON string
if (is_string($timeslot->days_available)) {
    $timeslot->days_available = json_decode($timeslot->days_available, true);
}

// Log the timeslot data for debugging
Log::info('Editing Timeslot:', [
    'Timeslot Data' => $timeslot->toArray(),
]);

        return view('timeslots.edit', compact('timeslot'),$data);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
        // dd($request->all());
        // exit;   
    $validated = $request->validate([
        'from_time' => 'required|date_format:H:i',
        'to_time' => 'required|date_format:H:i|after:from_time',
        'Active' => 'required|in:0,1', 
        'days_available' => 'array|nullable',
        'days_available.*' => 'string',
    ]);

    $timeslot = Timeslot::findOrFail($id);
    $timeslot->from_time = $validated['from_time'];
    $timeslot->Active=$validated['Active'];
    $timeslot->to_time = $validated['to_time'];
    $timeslot->days_available = $validated['days_available'];
    $timeslot->save();

    return redirect()->route('timeslots.index')->with('success', 'Timeslot updated successfully.');
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
