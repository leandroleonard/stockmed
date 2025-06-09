<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerController extends BaseController
{
    public function index()
    {
        $customerModel = new CustomerModel();
        $customers = $customerModel->findAll();

        return view('dashboard/customer/index', ['customers' => $customers]);
    }

    public function create()
    {
        return view('dashboard/customer/form');
    }
}
