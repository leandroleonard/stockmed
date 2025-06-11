<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerController extends BaseController
{
    private CustomerModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();   
    }
    public function index()
    {
        $customers = $this->customerModel->findAll();

        return view('dashboard/customer/index', ['customers' => $customers]);
    }

    public function create()
    {
        return view('dashboard/customer/form');
    }

    public function update($customerCode)
    {
        $customer = $this->customerModel->where('customer_code', $customerCode)->first();

        if(!$customer) return redirect()->to(base_url('dashboard/clients/create'))->withInput()->with('error', 'Cliente não encontrado. Poderia criar um.');

        // echo "<pre>";
        // exit(var_dump($customer));
        
        return view('dashboard/customer/update', ['customer' => $customer]);
    }
}
