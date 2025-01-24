<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Mail\CustomerInvoice;
use App\Mail\DriverBooked;
use App\Mail\VehicleBooked;
use App\Model\VehicleContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Model\UserData;
use App\Model\User;
use App\Model\VehicleTypeModel; // Import VehicleTypeModel  
use Illuminate\Support\Facades\Validator;



class contractcontroller extends Controller
{
    
/**
     * Display the list of vehicle contracts.
     */
    public function index()
    {   

        $user = auth()->user();

        if (!$user) {
            // Handle the case where user is not authenticated
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }



        if ($user->user_type == 'S') {
            // Super admin sees all timeslots
            $timeslots = VehicleContract::with(['user', 'company'])->get();
        } else if ($user->user_type == 'O') {
            // Owner sees only their created timeslots
            $timeslots = VehicleContract::with(['user', 'company'])
                ->where('user_id', $user->id)
                ->get();
        }
        return view('vehicleContract.index', compact('timeslots'));
    }

    /**
     * Fetch vehicle contracts data for DataTables.
     */
 /**
 * Fetch vehicle contracts data for DataTables.
 */
public function fetchData(Request $request)
{
    if ($request->ajax()) {
        try {
            $contracts = VehicleContract::with('company') // Load the 'company' relationship
                ->select(['vec_id', 'Name', 'Vechiletype', 'company_name']) // Select fields from VehicleContract
                ->orderBy('vec_id', 'desc');

            return \Yajra\DataTables\Facades\DataTables::eloquent($contracts)
                ->addColumn('check', function ($contract) {
                    return '<input type="checkbox" class="checkbox" value="' . $contract->vec_id . '">';
                })
                ->addColumn('company_name', function ($contract) {
                    return $contract->company ? $contract->company->name : 'N/A'; // Fetch the company name
                })
                ->addColumn('action', function ($contract) {
                    return '<a href="' . route('vehiclecontracts.edit', $contract->vec_id) . '" class="btn btn-primary btn-sm">Edit</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm delete-contract" data-id="' . $contract->vec_id . '">Delete</a>';
                })
                ->rawColumns(['check', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching data: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching data.'], 500);
        }
    }

    return response()->json(['error' => 'Invalid request'], 400);
}


    /**
     * Show the form for creating a new vehicle contract.
     */
    public function create()
    {
        $user = Auth::user(); // Get the authenticated user
        $data = [];
    
        // Check if the user is a super admin
        if ($user->user_type === 'S') {
            // Fetch companies for super admin
            $customerIds = UserData::where('key', 'client')
                                   ->where('value', 1)
                                   ->pluck('user_id')
                                   ->toArray();
            // $data['customers'] = User::whereIn('id', $customerIds)->get();
            $data['customers'] = User::whereIn('id', $customerIds)
            ->pluck('name', 'id')
            ->toArray();
        }
    
        $data['user_id'] = $user->id;
    
        // Fetch vehicle types
        $data['vehicleTypes'] = VehicleTypeModel::where('isenable', 1)->get();
    
        // Pass the data to the view
        return view('vehicleContract.create', $data);
    }
    



    public function store(Request $request)
    {
        Log::info('Incoming request to create vehicle contract', $request->all());
    
        $request->validate([
            'Name' => 'required|string|max:255',
            'Vechiletype' => 'required|exists:vehicle_types,vehicletype', // Validate the vehicletype field
            'company_name' => 'required|string|max:255',
        ]);
    
        try {
            // Save the vehicle type directly
            VehicleContract::create($request->only(['Name', 'Vechiletype', 'company_name']));
            return redirect()->route('vehiclecontracts.index')->with('success', 'Vehicle contract created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating vehicle contract: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the vehicle contract.');
        }
    }
    
    
    /**
     * Display the specified vehicle contract.
     */
    public function show($id)
    {
        $contract = VehicleContract::findOrFail($id);
        return response()->json(['data' => $contract]);
    }

       
/**
     * Show the form for editing the specified vehicle contract.
     */
    public function edit($id)
    {
        $contract = VehicleContract::findOrFail($id); // Fetch the contract by ID
        $user = Auth::user(); // Get the authenticated user
        $data = [
            'contract' => $contract,
            'vehicleTypes' => VehicleTypeModel::where('isenable', 1)->get(),
        ];
    
        // Fetch customers for super admin
        if ($user->user_type === 'S') {
            $customerIds = UserData::where('key', 'client')
                                   ->where('value', 1)
                                   ->pluck('user_id')
                                   ->toArray();
            // $data['customers'] = User::whereIn('id', $customerIds)->get();
             $data['customers'] = User::whereIn('id', $customerIds)
                                 ->pluck('name', 'id') // Key: ID, Value: Name
                                 ->toArray();
        }
    
        return view('vehicleContract.edit', $data);
    }

    /**
     * Update the specified vehicle contract in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Vechiletype' => 'required|exists:vehicle_types,vehicletype',
            'company_id' => 'required|exists:users,id', // Validate company_id instead of company_name
        ]);
    
        try {
            $contract = VehicleContract::findOrFail($id);
            $contract->update([
                'Name' => $request->input('Name'),
                'Vechiletype' => $request->input('Vechiletype'),
                'company_name' => $request->input('company_id'), // Corrected this line
            ]);
    
            return redirect()->route('vehiclecontracts.index')->with('success', 'Vehicle contract updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the vehicle contract.');
        }
    }
    

    /**
     * Remove the specified vehicle contract from storage.
     */
    public function destroy($id)
    {
        try {
            $contract = VehicleContract::findOrFail($id);
            $contract->delete();
            return response()->json(['success' => true, 'message' => 'Vehicle contract deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle contract: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the vehicle contract.'], 500);
        }
    }

    /**
     * Bulk delete the selected vehicle contracts.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:vechilecontract,vec_id', // Ensure each ID exists
        ]);
    
        try {
            VehicleContract::whereIn('vec_id', $request->ids)->delete();
            return response()->json(['success' => true, 'message' => 'Selected contracts deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle contracts: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the selected contracts.'], 500);
        }
    }
    
}