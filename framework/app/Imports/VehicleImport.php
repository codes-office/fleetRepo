<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

*/

namespace App\Imports;

use App\Model\VehicleModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

class VehicleImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $vehicle)
    {
        $id = VehicleModel::create([
            'make' => $vehicle['vehicle_maker'],
            'model' => $vehicle['vehicle_model'],
            'year' => $vehicle['vehicle_year'],
            'int_mileage' => $vehicle['initial_mileage'],
            'reg_exp_date' => date('Y-m-d', strtotime($vehicle['registration_expiry_date'])),
            'engine_type' => $vehicle['vehicle_engine_type'],
            'horse_power' => $vehicle['vehicle_horse_power'],
            'color' => $vehicle['color'],
            'vin' => $vehicle['vin'],
            'license_plate' => $vehicle['license_plate'],
            'lic_exp_date' => date('Y-m-d', strtotime($vehicle['license_expiry_date'])),
            'user_id' => Auth::id(),
            'group_id' => Auth::user()->group_id,
        ])->id;

        $meta = VehicleModel::find($id);
        $meta->setMeta([
            'ins_number' => (isset($vehicle['insurance_number'])) ? $vehicle['insurance_number'] : "",
            'ins_exp_date' => (isset($vehicle['insurance_expiration_date']) && $vehicle['insurance_expiration_date'] != null) ? date('Y-m-d', strtotime($vehicle['insurance_expiration_date'])) : "",
            'documents' => "",
        ]);
        $meta->average = $vehicle['averagempg'];
        $meta->save();
    }
}
