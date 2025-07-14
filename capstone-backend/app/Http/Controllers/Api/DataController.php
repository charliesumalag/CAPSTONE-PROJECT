<?php

namespace App\Http\Controllers\Api;

use App\Services\SMSService;

use App\Models\Data;
use Illuminate\Http\Request;
use App\Events\NewSensorReading;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Data::all();
    }

    public function latest()
    {
        $reading = Data::latest()->first();

        return response()->json($reading);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'ph' => 'required|numeric',
            'turbidity' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'water_level' => 'nullable|numeric',
        ]);

        $reading = Data::create($validated);

        broadcast(new NewSensorReading($reading))->toOthers();

        return response()->json([
            'message' => 'Sensor data saved successfully',
            'data' => $reading
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function checkTwilioEnv()
    {
        $sms = new \App\Services\SMSService();
        $sms->testEnv();
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
