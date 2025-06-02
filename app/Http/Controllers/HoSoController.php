<?php

namespace App\Http\Controllers;

use App\Models\HoSo;
use Illuminate\Http\Request;

class HoSoController extends Controller
{
    
     // GET /api/hoso/counths
        public function countHS()
        {
            $countHs = HoSo::count();
            return response()->json([
                'status' => 'success',
                'total_hs' => $countHs]
            );
        }

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
