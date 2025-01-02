<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers as CustomerRequest;
use App\Http\Requests\ImportRequest;
use App\Imports\CustomerImport;
use Auth;
use App\Model\User;
use App\Model\CompanyTeam;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CompanyTeamController extends Controller
{
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("company_team.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return view("company_team.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // dd($request->all());
    // exit;
    $data = $request->validate([
        'team_name' => 'required|string|max:255',
        'manager_name' => 'required|string|max:255',
        // 'team_id' => 'required|string|unique:companyteams',
        // 'employee_count' => 'nullable|integer',
    ]);

    // Assuming employees are being counted dynamically (use your logic to count employees)
    // $employeeCount = 0; // Example; get the real count from your employee model if needed

    // Save to database
    $team = new CompanyTeam();
    $team->name = $request->team_name;
    $team->team_manager = $request->manager_name;
    // $team->team_id = $data['team_id'];
    // $team->employee_count = $employeeCount;
    $team->save();
Log::info($team);

return view("company_team.index ");

// return redirect()->route('company_team.index')->with('success', 'Team created successfully');
    // return redirect()->route('teams.index')->with('success', 'Team created successfully!');
}

public function fetch_data(Request $request)
{
    if ($request->ajax()) {
        // Fetch all teams
        $teams = CompanyTeam::select('id', 'name', 'team_manager', 'employee_count');

        return DataTables::of($teams)
            ->addColumn('check', function ($team) {
                return '<input type="checkbox" name="ids[]" value="' . $team->id . '" class="checkbox">';
            })
            ->addColumn('action', function ($team) {
                return '<a href="' . route('company_team.edit', $team->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <form action="' . route('company_team.destroy', $team->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</button>
                        </form>';
            })
            ->rawColumns(['action', 'check'])
            ->make(true);
    }
}




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
