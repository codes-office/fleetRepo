<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Model\User;

class PermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = array(
            'Users',
            'Drivers',
            'Customer',
            'VehicleType',
            'VehicleMaker',
            'VehicleModels',
            'VehicleColors',
            'VehicleGroup',
            'VehicleInspection',
            'BookingQuotations',
            'PartsCategory',
            'Mechanics',
            'Vehicles',
            'Transactions',
            'Bookings',
            'Reports',
            'Fuel',
            'Vendors',
            'Parts',
            'WorkOrders',
            'Notes',
            'ServiceReminders',
            'ServiceItems',
            'Testimonials',
            'Team',
            'Settings',
            'Inquiries',
        );
        foreach ($modules as $row) {

            Permission::create(['name' => $row . " add"]);
            Permission::create(['name' => $row . " edit"]);
            Permission::create(['name' => $row . " delete"]);
            Permission::create(['name' => $row . " list"]);
            Permission::create(['name' => $row . " import"]);
        }
        $all = Permission::all();
        $role = Role::create(['name' => 'Super Admin']);
        $role->givePermissionTo($all);
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(['Bookings list','Bookings add','Bookings edit','Bookings delete','Drivers list','Drivers add','Drivers edit','Drivers delete','Customer list','Customer add','Customer edit','Customer delete']);
        $users = User::where('user_type', 'S')->get();
        foreach ($users as $user) {
            $u = User::find($user->id);
            $u->assignRole('Super Admin');
        }
        $drivers = User::where('user_type', 'D')->get();
        foreach ($drivers as $driver) {
            $d = User::find($driver->id);
            $d->givePermissionTo(['Notes add','Notes edit','Notes delete','Notes list','Drivers list','Fuel add','Fuel edit','Fuel delete','Fuel list','VehicleInspection add','Transactions list','Transactions add','Transactions edit','Transactions delete']);
        }
        $customers = User::where('user_type', 'C')->get();
        foreach ($customers as $customer) {
            $c = User::find($customer->id);
            $c->givePermissionTo(['Bookings add','Bookings edit','Bookings list','Bookings delete']);
        }
        $others = User::where('user_type', 'O')->get();
        foreach ($others as $other) {
            $o = User::find($other->id);
            $o->assignRole('Admin');
        }   
    }
}
