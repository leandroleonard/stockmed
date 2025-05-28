<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BuyController extends BaseController
{
    public function index()
    {
        return view('dashboard/buy/index');
    }
}
