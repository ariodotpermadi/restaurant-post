<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;

class TableController extends Controller
{
    public function index()
    {
        // Mengambil meja beserta order aktifnya (Eager Loading untuk menghindari N+1)
        return Table::with(['orders' => function($q) {
            $q->where('status', 'open');
        }])->get();
    }
}
