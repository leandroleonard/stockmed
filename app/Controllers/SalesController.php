<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SalesController extends BaseController
{
    public function index()
    {
        return view('dashboard/sales/index');
    }
}
