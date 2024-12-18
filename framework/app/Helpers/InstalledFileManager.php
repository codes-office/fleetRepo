<?php

namespace App\Helpers;

class InstalledFileManager
{

    public function create()
    {
        file_put_contents(storage_path('installed'), 'version6.5');
    }

    public function update()
    {
        return $this->create();
    }
}
