<?php
namespace App\Http\Controllers\Admin;
use Yajra\DataTables\Facades\DataTables;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Mail\CustomerInvoice;
use App\Mail\DriverBooked;
use App\Mail\VehicleBooked;
use App\Model\VehicleContract;
use App\Model\Contractdb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Model\UserData;
use App\Model\User;
use App\Model\VehicleTypeModel; // Import VehicleTypeModel  
use Illuminate\Support\Facades\Validator;



class ContractAdd extends Controller
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
                $contracts = Contractdb::select(['id', 'shortCode', 'Vechiletype'])
                    ->orderBy('id', 'asc');

                return DataTables::eloquent($contracts)
                    ->addColumn('check', function ($contract) {
                        return '<input type="checkbox" class="checkbox" value="' . $contract->id . '">';
                    })
                    ->editColumn('Vechiletype', function ($contract) {
                        return $contract->Vechiletype ?? 'N/A'; // Just display the vehicle type
                    })
                    ->addColumn('action', function ($contract) {
                        /////////////////////////////////////////
                         $user = auth()->user();

                         if (!$user) {
                            // Handle the case where user is not authenticated
                            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
                        }

                        if ($user->user_type == 'S' ) {
                            // Super admin sees all timeslots
                            return '<a href="' . route('contracts_route.edit', $contract->id) . '" class="btn btn-primary btn-sm">Edit</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm delete-contract" data-id="' . route('contracts_route.delete', $contract->id) . '">Delete</a>';
                           
                        } elseif($user->user_type == 'S' || $contract->shortCode == 'StandBy'){
                            return  '<button class="btn btn-info btn-sm view-contract" data-id="' . $contract->id . '">View</button>';


                        }
                            return  '<button class="btn btn-info btn-sm view-contract" data-id="' . $contract->id . '">View</button>';



                            ////////////////////////////////////////////////////////////                 ///////////////////////////// 
                        // return '<a href="' . route('contracts_route.edit', $contract->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        //     <button class="btn btn-info btn-sm view-contract" data-id="' . $contract->id . '">View</button>
                        //         <a href="javascript:void(0)" class="btn btn-danger btn-sm delete-contract" data-id="' . route('contracts_route.delete', $contract->id) . '">Delete</a>';
                    })
                    ->rawColumns(['check', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('Error fetching contracts: ' . $e->getMessage());
                return response()->json(['error' => 'Error fetching contracts.'], 500);
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

        
        return view('contract-view.create', $data);
        // Pass the data to the view
    }
    



    public function store(Request $request)
    {
        Log::info('Incoming request to create vehicle contract', $request->all());
       
        $validatedData = $request->validate([
            'contractType' => 'required|string|max:255',
            'contractTypePackage' => 'required|string|max:255',
            'shortCode' => 'required|string|max:255',
            'numberOfDuties' => 'required|integer',
            'allottedKmPerMonth' => 'required|integer',
            'minHoursPerDay' => 'required|integer',
            'packageCostPerMonth' => 'required|numeric',
            'pricingForExtraDuty' => 'required|numeric',
            'costPerKmAfterMinKm' => 'required|numeric',
            'costPerHourAfterMinHours' => 'required|numeric',
            'garageKmOnReporting' => 'required|integer',
            'garageHoursPerDay' => 'required|integer',
            'baseDieselPrice' => 'required|numeric',
            'mileage' => 'required|numeric',
            'seatingCapacity' => 'required|integer',
            'acPriceAdjustmentPerKm' => 'required|numeric',
            'minTripsPerMonth' => 'required|integer',
            'Vechiletype' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
        ]);
     

        Contractdb::create($validatedData);

        return redirect()->route('contract-view.index')->with('success', 'Vehicle contract updated successfully');

    }
    
    
    /**
     * Display the specified vehicle contract.
     */
    public function show($id)
    {
        $contract = Contractdb::findOrFail($id);
        return response()->json(['data' => $contract]);
    }


      public function edit($id)
      {
          $contract = Contractdb::findOrFail($id); // Fetch the contract details
          $user = Auth::user();
          $data = [];
      
          if ($user->user_type === 'S') {
              $customerIds = UserData::where('key', 'client')
                  ->where('value', 1)
                  ->pluck('user_id')
                  ->toArray();
              $data['customers'] = User::whereIn('id', $customerIds)
                  ->pluck('name', 'id')
                  ->toArray();
          }
      
          $data['vehicleTypes'] = VehicleTypeModel::where('isenable', 1)->get();
          $data['contract'] = $contract; // Pass contract details to view
          return view('contract-view.edit', $data);
      }
      


public function update(Request $request, $id)
{
    Log::info('Incoming request to update vehicle contract', $request->all());
    
    
    $validatedData = $request->validate([
        'contractType' => 'required|string|max:255',
        'contractTypePackage' => 'required|string|max:255',
        'shortCode' => 'required|string|max:255',
        'numberOfDuties' => 'required|integer',
        'allottedKmPerMonth' => 'required|integer',
        'minHoursPerDay' => 'required|integer',
        'packageCostPerMonth' => 'required|numeric',
        'pricingForExtraDuty' => 'required|numeric',
        'costPerKmAfterMinKm' => 'required|numeric',
        'costPerHourAfterMinHours' => 'required|numeric',
        'garageKmOnReporting' => 'required|integer',
        'garageHoursPerDay' => 'required|integer',
        'baseDieselPrice' => 'required|numeric',
        'mileage' => 'required|numeric',
        'seatingCapacity' => 'required|integer',
        'acPriceAdjustmentPerKm' => 'required|numeric',
        'minTripsPerMonth' => 'required|integer',
        'Vechiletype' => 'required|string|max:255', // Correct the naming consistency
        'company_name' => 'required|string|max:255',
    ]);
    
    
    $contract = Contractdb::findOrFail($id); // Fetch contract by ID
    $contract->update($validatedData); // Update the contract record
    
    // die("helleo");
    return redirect()->route('contract-view.index')->with('success', 'Vehicle contract updated successfully');
}


public function destroy($id)
{
    try {
        $contract = Contractdb::findOrFail($id);
        $contract->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Contract deleted successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error("Delete Error: {$e->getMessage()}");
        return response()->json([
            'success' => false,
            'message' => 'Error deleting contract. Details in logs.'
        ], 500);
    }
}

    public function bulkDelete(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:contractdb,id', // Validate each ID exists
    ]);

    try {
        Contractdb::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true, 'message' => 'Selected contracts deleted successfully!']);
    } catch (\Exception $e) {
        Log::error('Error deleting contracts: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'An error occurred while deleting contracts.'], 500);
    }
}   


         public function viewButton($id)
            {
                $contract = Contractdb::findOrFail($id);
                return view('contract-view.view', ['contract' => $contract]);
            }

            /// view 
        public function showView($id)
        {
            
            $contract = Contractdb::find($id);

    if (!$contract) {
        return response()->json(['success' => false, 'message' => 'Contract not found.'], 404);
    }

    return response()->json(['success' => true, 'contract' => $contract]);
        }    
}

