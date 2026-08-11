<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsProduct;
use Illuminate\View\View;

class LmsProductClientController extends Controller
{
    public function index(LmsProduct $lmsProduct): View
    {
        $assignedClients = $lmsProduct->product
            ? $lmsProduct->product->clients()->wherePivot('status', true)->orderBy('company_name')->get()
            : collect();

        return view('admin.lms.products.clients', [
            'lmsProduct' => $lmsProduct,
            'assignedClients' => $assignedClients,
        ]);
    }
}
