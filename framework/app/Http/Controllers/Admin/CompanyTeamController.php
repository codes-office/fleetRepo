<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UserRequest;
use App\Model\Hyvikk;
use App\Model\User;
use App\Model\UserData;
use App\Model\VehicleGroupModel;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use App\Model\CompanyTeam;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Collection;


class CompanyTeamController extends Controller
{
    public function index(){
        return view('company_team.index');
    }

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

    return view('company_team.create', $data);
}


public function fetch_data(Request $request)
{
    // Log request data and endpoint
    // \Log::info('Endpoint Hit: ' . $request->url());
    // \Log::info('Request Data: ', $request->all());

    if ($request->ajax()) {
        // Fetch dynamic data
        $companyTeams = CompanyTeam::with('company') // Assuming a 'company' relationship in CompanyTeam model
            ->select('id', 'Company_id', 'Team_Name', 'Manager') // Adjust the columns as per your database schema
            ->get();

        // Log the fetched data
        // \Log::info('Fetched Company Teams Data:', $companyTeams->toArray());  

        return DataTables::of($companyTeams)
            ->addColumn('check', function ($team) {
                return '<input type="checkbox" name="ids[]" value="' . $team->id . '" class="checkbox" id="chk' . $team->id . '" onclick=\'checkcheckbox();\'>';
            })
            ->addColumn('Team', function ($team) {
                return $team->Team_Name; // Ensure 'Team_Name' matches your database field
            })
            
            ->addColumn('Company', function ($team) {
                return $team->company ? $team->company->name : 'No Company'; // Ensure you handle null relationships gracefully
            })
            ->addColumn('action', function ($team) {
                return '
                    <a href="' . url('admin/companyteam/edit/' . $team->id) . '" class="btn btn-sm btn-primary">Edit</a>
                    <a href="' . url('admin/companyteam/delete/' . $team->id) . '" class="btn btn-sm btn-danger">Delete</a>
                ';
            })
            ->rawColumns(['check', 'action']) // Ensure HTML rendering for 'check' and 'action' columns
            ->make(true);
    }
}

	

public function store(Request $request)
{
    $user = Auth::user(); // Get the authenticated user

    // Define the validation rules
    $rules = [
        'team_name' => 'string|max:125',
        'manager' => 'nullable|string|max:125',  // Ensure manager is optional, but can be provided
    ];

    // Apply conditional validation for company_id when user_type is 'S' (super admin)
    if ($user->user_type === 'S') {
        $rules['company_id'] = 'required|integer';  // Validate company_id for super admin only
    }

    // Validate the incoming data
    $validated = $request->validate($rules);

    // Determine the company_id based on user type and client value
    if ($user->user_type === 'O') {
        // Fetch the client status from UserData table
        $clientStatus = UserData::where('user_id', $user->id)
                                ->where('key', 'client')
                                ->first();

        if ($clientStatus) {
            // If client is 0, assign admin user_id as company_id
            if ($clientStatus->value == 0) {
                $companyId = $user->assigned_admin; // Use assigned_admin field from the User table
            }
            // If client is 1, assign the user's own id as company_id
            elseif ($clientStatus->value == 1) {
                $companyId = $user->id;
            }
        }
    } else {
        // Default behavior if user type is not 'O', use the provided company_id from the request
        $companyId = $validated['company_id'] ?? null;  // Assign company_id if it's provided
    }

    // Create a new CompanyTeam entry
    $companyTeam = new CompanyTeam();
    $companyTeam->Company_id = $companyId;  // Use the determined company_id
    $companyTeam->Team_Name = $validated['team_name'];    // Ensure correct casing: Team_Name
    $companyTeam->Manager = $validated['manager'] ?? '';  // Handle manager value (optional)
    $companyTeam->save();

    // Redirect to the teams index page with a success message
    return redirect('admin/companyteam');
}

public function edit($id)
{
    $user = Auth::user(); // Get the authenticated user

    // Initialize an array to store data for the view
    $data = [];

    // Fetch the specific team using the provided ID
    $team = CompanyTeam::findOrFail($id);
    $data['team'] = $team; // Add team data to the array

    // Check if the user is a super admin to fetch customers
    if ($user->user_type === 'S') {
        $customerIds = UserData::where('key', 'client')
                               ->where('value', 1)
                               ->pluck('user_id')
                               ->toArray();
        $data['customers'] = User::whereIn('id', $customerIds)->get();
    }

    $data['user_id'] = $user->id;

    return view('company_team.edit', $data);
}

public function show($id) {
    // Assign $id to a variable named teamId
    $teamId = $id;
log::info($teamId);
    // Pass the variable to the view
    return view('customers.index', [
        'teamId' => $teamId, // $teamId should be a valid PHP variable
    ]);
    
}



public function update(Request $request, $id)
{

    // Validate the incoming data
    $validated = $request->validate([
        'company_id' => 'required|integer',  // Ensure company_id exists in companies table
        'team_name' => 'nullable|string|max:125',
        'manager' => 'required|string|max:125',
    ]);

    // Find the team by ID
    $companyTeam = CompanyTeam::findOrFail($id);

    // Update the team with the validated data
    $companyTeam->Company_id = $validated['company_id'];
    $companyTeam->Team_Name = $validated['team_name'];
    $companyTeam->Manager = $validated['manager'];
    $companyTeam->save();

    // Redirect back to the index page with a success message
    return redirect('admin/companyteam')->with('success', 'Company Team updated successfully!');
}
public function destroy($id)
{
    try {
        // Find the team by ID
        $companyTeam = CompanyTeam::findOrFail($id);

        // Delete the team
        $companyTeam->delete();

        // Redirect back to the index view with a success message
        return redirect('admin/companyteam')->with('success', 'Company Team deleted successfully!');
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error deleting Company Team: ' . $e->getMessage());

        // Redirect back to the index view with an error message
        return redirect('admin/companyteam')->with('error', 'Failed to delete the Company Team. Please try again later.');
    }
}


}

