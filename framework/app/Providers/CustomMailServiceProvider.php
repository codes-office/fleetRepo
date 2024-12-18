<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Providers;

use App\Hyvikk\CustomTransportManager;
use Illuminate\Mail\MailServiceProvider;

class CustomMailServiceProvider extends MailServiceProvider {

	protected function registerSwiftTransport() {
		$this->app['swift.transport'] = $this->app->share(function ($app) {
			return new CustomTransportManager($app);
		});
	}
}
