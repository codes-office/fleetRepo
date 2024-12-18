<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers;

use App\Helpers\DatabaseManager;
use App\Helpers\EnvironmentManager;
use App\Helpers\InstalledFileManager;
use App\Helpers\PermissionsChecker;
use App\Helpers\RequirementsChecker;
// use App\Http\Controllers\Controller;
use App\Http\Requests\LaravelWebInstallerRequest;
use DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaravelWebInstaller extends Controller {

	public function __construct(PermissionsChecker $checker, RequirementsChecker $checker2, EnvironmentManager $environmentManager, DatabaseManager $databaseManager) {
		$this->permissions = $checker;
		$this->requirements = $checker2;
		$this->environmentManager = $environmentManager;
		$this->databaseManager = $databaseManager;
		$this->middleware("canInstall")->except("installed");
	}

	public function index() {
		if (file_exists("storage/installed")) {
			\File::copy("storage/installed", storage_path('installed'));
		}
		if (file_exists('public/uploads')) {
			$files = Storage::disk('public_uploads')->files('');
			foreach ($files as $file) {
				if (file_exists("public/uploads/" . $file)) {
					\File::copy("public/uploads/" . $file, "uploads/" . $file);
				}
			}
		}
		Artisan::call('cache:clear');
		Artisan::call('view:clear');
		Artisan::call('config:clear');
		$permissions = $this->permissions->check(
			config('installer.permissions')
		);
		$requirements = $this->requirements->check(
			config('installer.requirements')
		);

		$envConfig = $this->environmentManager->getEnvContent();
		if ($permissions['errors'] == null || $requirements['errors'] == null) {
			return view('laravel_web_installer.laravel_web_installer', compact('envConfig', 'permissions', 'requirements'));
		} else {
			abort(404);
		}

	}

	private function check_status($code) {
		$data = array("pcode" => $code, 'domain' => $_SERVER['SERVER_NAME']);
		$data_string = json_encode($data);

		$ch = curl_init('https://3xy2s8y7c9.execute-api.ap-south-1.amazonaws.com/prod');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);

		$result = curl_exec($ch);
		return $result;
	}

	public function install(LaravelWebInstallerRequest $request, InstalledFileManager $fileManager) {
		$code = $request->purchase_code;
		$xx = $this->check_status($code);
		if ($xx != "1") {
			$response = [
				'status' => 'fail',
				'errors' => ['purchase_code' => $xx],
			];
		} else {
			$response = [
				'status' => 'success',
				'message' => "Verified Code",
			];
		}

		if ($response['status'] == 'success') {
			$message = $this->environmentManager->saveFile($request);
			// dd($message['status']);
			if ($message['status'] == 'success') {
				return redirect('migration');

			} else {

				$msg = $message['message'];
				return redirect()->back()->with(['message' => $msg]);

			}
		} else {
			$p_code = $response['errors'];
			$msg = $p_code['purchase_code'];
			return redirect()->back()->with(['message' => $msg]);
		}

	}

	public function db_migration(InstalledFileManager $fileManager) {
		//$database = $this->databaseManager->migrateAndSeed();
		$database = DB::unprepared(File::get(storage_path('fleet6_db.sql')));

		// dd($database['status']);
		//if ($database['status'] == 'success') {
		if ($database == 'true') {

			$fileManager->update();
			return view('laravel_web_installer.finished');

		} else {
			abort(404);
		}
	}

	public function migration() {
		// count number of tables in database
		$tables = DB::select('SHOW TABLES');

		// dd($tables[0]->total); //total number of tables
		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "") {
			Artisan::call('backup:run', ['--only-db' => 'true']);

			// existing database of fleet2 having tables | update fleet2 => fleet3 with old database records remains same

			return redirect('upgrade');
		}
		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && (file_get_contents(storage_path('installed')) == "version3" || file_get_contents(storage_path('installed')) == "version3.1")) {

			Artisan::call('backup:run', ['--only-db' => 'true']);

			// existing database of fleet3 having tables | update fleet3 new features with old database records remains same

			return redirect('upgrade3');
		}
		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4") {
			Artisan::call('backup:run', ['--only-db' => 'true']);

			// existing database of fleet4 having tables | update fleet4 => fleet4.0.1 with old database records remains same

			return redirect('upgrade4');
		}
		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4.0.1") {
			Artisan::call('backup:run', ['--only-db' => 'true']);

			// existing database of fleet4 having tables | update fleet4 => fleet4.0.1 with old database records remains same

			return redirect('upgrade4.0.2');
		}

		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4.0.2") {
			Artisan::call('backup:run', ['--only-db' => 'true']);

			// existing database of fleet4 having tables | update fleet4.0.2 => fleet4.0.3 with old database records remains same

			return redirect('upgrade4.0.3');
		}
		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4.0.3") {
			Artisan::call('backup:run', ['--only-db' => 'true']);

			// existing database of fleet4 having tables | update fleet4.0.3 => fleet5.0 with old database records remains same

			return redirect('upgrade5');
		}

		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version5") {
			Artisan::call('backup:run', ['--only-db' => 'true']);

			// existing database of fleet4 having tables | update fleet4.0.3 => fleet5.0 with old database records remains same

			return redirect('upgrade6');
		}

		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version6") {
			Artisan::call('backup:run', ['--only-db' => 'true']);
			// existing database of fleet4 having tables | update fleet4.0.3 => fleet5.0 with old database records remains same

			return redirect('upgrade6.0.1');
		}

		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version6.0.1") {
			Artisan::call('backup:run', ['--only-db' => 'true']);
			// existing database of fleet4 having tables | update fleet4.0.3 => fleet5.0 with old database records remains same

			return redirect('upgrade6.0.2');
		}

		if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version6.0.2") {
			Artisan::call('backup:run', ['--only-db' => 'true']);
			// existing database of fleet4 having tables | update fleet4.0.3 => fleet5.0 with old database records remains same

			return redirect('upgrade6.0.3');
		}

		if (sizeof($tables) == 0 && !file_exists(storage_path('installed'))) {
			// empty database/ new installation

			return redirect('migrate');
		} else {
			return redirect()->back()->with(['message' => "Incorrect Database Name"]);
		}

		// return view('laravel_web_installer.migrate');

	}
}
