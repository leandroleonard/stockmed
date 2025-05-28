<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class StorageController extends BaseController
{
    public function index()
    {
        return view('dashboard/storage/index');
    }
}
