<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerController extends BaseController
{
    public function index()
    {
        return view('dashboard/customer/index');
    }

    public function create()
    {
        return view('dashboard/customer/form');
    }
}
