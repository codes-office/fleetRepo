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
use Illuminate\Support\Str;
use Redirect;
use Spatie\Permission\Models\Role;

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
    
        return view('company_team.create',$data);
}
    
	public function fetch_data(Request $request) {
		if ($request->ajax()) {
			// $users = User::with(['metas'])
			// 	->where(function ($query) {
			// 		$query->where('user_type', 'O');
			// 	});
			$admins = User::with(['metas'])
			->where(function ($query) {
				$query->where('user_type', 'O');
			})
			->whereHas('metas', function ($query) {
				$query->where('key', 'client')->where('value', 1);
			})->select('id', 'name')->get();


			$users = User::with(['metas'])
			->where(function ($query) {
				$query->where('user_type', 'O');
			})
			->whereHas('metas', function ($query) {
				$query->where('key', 'client')->where('value', 0);
			});
		

			$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
	
			return DataTables::eloquent($users)
				->addColumn('check', function ($user) {
					$tag = '';
					if ($user->user_type == "S") {
						$tag = '<i class="fa fa-ban" style="color:#767676;"></i>';
					} else {
						$tag = '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
					}
					return $tag;
				})
				->addColumn('profile_image', function ($user) {
					$src = ($user->profile_image != null) ? asset('uploads/' . $user->profile_image) : asset('assets/images/no-user.jpg');
					return '<img src="' . $src . '" height="70px" width="70px">';
				})
				->editColumn('created_at', function ($user) use ($date_format_setting) {
					return date($date_format_setting . ' g:i A', strtotime($user->created_at));
				})
				->addColumn('action', function ($user) {
					return view('users.list-actions', ['row' => $user]);
				})
		 		
			
			->rawColumns(['profile_image', 'action', 'check', 'assign_admin']) // Mark columns with HTML as raw
				->make(true);
		}
	}	

}
