<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FirebaseController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$database = app('firebase.database');
		// $database->getReference('User_Locations')
		//         ->set([
		//             'user_type' => 'D',
		//             ]);
		$data = $database
			->getReference('/')
			->orderByValue('user_type')
			->equalTo('D')
			->limitToFirst(5)
			->getSnapshot();
		dd($data);

		$data = $database
			->getReference('/')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();

		dd($data);

		foreach ($data as $d) {
			if ($d['user_id'] == "74") {
				if ($d['availability'] == "1") {
					echo $d['availability'] . "<br>";
					echo $d['last_updated'] . "<br>";
					echo $d['latitude'] . "<br>";
					echo $d['longitude'] . "<br>";
					echo $d['user_id'] . "<br>";
					echo $d['user_name'] . "<br>";
					echo $d['user_type'] . "<br>";
				}
			}
		}
		dd($data);

		// $factory = (new Factory())
		// ->withDatabaseUri('https://decent-atlas-303605-defaultrtdb.firebaseio.com/');
		// $database = $factory->createDatabase();

		// $newPost = $database
		// ->getReference('testing')
		// ->set([
		//         'name' => 'Fleet Testing',
		//     ]);

		// $data = $newPost->getvalue();

		// $factory = (new Factory())
		//             ->withDatabaseUri('https://my-project-6280-303512-default-rtdb.firebaseio.com/');
		// $database = $factory->createDatabase();

		// $newPost = $database
		// ->getReference('testing')
		// ->push([
		// 'title' => 'Laravel FireBase Tutorial' ,
		// 'category' => 'Laravel'
		// ]);
		// echo '<pre>';
		// print_r($data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
