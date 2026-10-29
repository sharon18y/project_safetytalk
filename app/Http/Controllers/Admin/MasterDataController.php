<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index()
    {
        // Mengambil semua data unit dan mengurutkannya berdasarkan no_urut
        $units = Unit::orderBy('no_urut', 'asc')->get();
        
        return view('admin.master_data.index', compact('units'));
    }
}