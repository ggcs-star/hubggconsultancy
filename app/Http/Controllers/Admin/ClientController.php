<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = User::where('role', 'user')->latest()->paginate(10);

        return view('admin.clients', compact('clients'));
    }
}
