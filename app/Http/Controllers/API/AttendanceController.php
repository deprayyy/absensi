<?php

namespace App\Http\Controllers\API;

use App\Models\Attendance;
use App\Models\Office;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController 
{
    /**
     * Hitung jarak (dalam meter) antara 2 titik koordinat.
     */
    private function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // radius bumi dalam meter

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLat = $lat2 - $lat1;
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Absen Masuk (Clock In)
     */
    public function clockIn(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
    
        $office = Office::first();
        if (!$office) {
            return response()->json(['message' => 'Office location not found'], 404);
        }
    
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|max:2048',
        ]);
    
        // Upload photo
        $photoPath = $request->file('photo')->store('clock_in_photos', 'public');
    
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'clock_in' => Carbon::now(),
            'clock_in_photo' => $photoPath,
        ]);
    
        return response()->json([
            'message' => 'Clock-in successful',
            'data' => $attendance,
        ]);
    }
    
    /**
     * Absen Pulang (Clock Out)
     */
    public function clockOut(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
    
        $request->validate([
            'photo' => 'required|image|max:2048',
            'activity_note' => 'required|string|max:1000',
        ]);
    
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
    
        if (!$attendance) {
            return response()->json(['message' => 'You have not clocked in yet'], 404);
        }
    
        if ($attendance->clock_out) {
            return response()->json(['message' => 'You have already clocked out today'], 409);
        }
    
        // Upload photo
        $photoPath = $request->file('photo')->store('clock_out_photos', 'public');
    
        $attendance->update([
            'clock_out' => Carbon::now(),
            'clock_out_photo' => $photoPath,
            'activity_note' => $request->activity_note,
        ]);
    
        return response()->json([
            'message' => 'Clock-out successful',
            'data' => $attendance,
        ]);
    }    
}
