<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Office;

class AttendanceController extends Controller
{
    public function history(Request $request)
{
    $attendances = $request->user()->attendances()->orderBy('created_at', 'desc')->get();

    return response()->json([
        'message' => 'Riwayat absensi',
        'data' => $attendances
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:masuk,pulang',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:2048'
        ]);

        // Cek lokasi kantor pertama (dummy dulu)
        $office = Office::first();
        if (!$office) {
            return response()->json(['message' => 'Tidak ada kantor terdaftar'], 400);
        }

        $distance = $this->calculateDistance(
            $office->latitude,
            $office->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > 100) { // lebih dari 100 meter
            return response()->json(['message' => 'Anda berada di luar area kantor!'], 403);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendances', 'public');
        }

        $attendance = Attendance::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $photoPath,
        ]);

        return response()->json([
            'message' => 'Absensi berhasil',
            'data' => $attendance
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
