<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SupplierController extends BaseController
{
    public function index()
    {
        return view('dashboard/supplier/index');
    }
}
