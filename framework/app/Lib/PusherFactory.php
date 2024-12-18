<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

*/

namespace App\Lib;

use Pusher\Pusher;

class PusherFactory
{
    public static function make()
    {
        return new Pusher(
            env("PUSHER_APP_KEY"), // public key
            env("PUSHER_APP_SECRET"), // Secret
            env("PUSHER_APP_ID"), // App_id
            array(
                'cluster' => env("PUSHER_APP_CLUSTER"), // Cluster
                'encrypted' => true,
            )
        );
    }
}
