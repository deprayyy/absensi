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

        $office = Office::first(); // ambil kantor pertama (atau sesuai user jika multi kantor)
        if (!$office) {
            return response()->json(['message' => 'Office location not found'], 404);
        }

        $userLat = $request->input('latitude');
        $userLon = $request->input('longitude');

        if (!$userLat || !$userLon) {
            return response()->json(['message' => 'Location is required'], 400);
        }

        // Hitung jarak
        $distance = $this->getDistance($userLat, $userLon, $office->latitude, $office->longitude);
        if ($distance > 100) { // 100 meter radius
            return response()->json([
                'message' => 'You are too far from the office location to clock in',
                'distance' => round($distance, 2)
            ], 403);
        }

        // Cek apakah user sudah absen masuk hari ini
        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already clocked in today'], 409);
        }

        // Simpan absen masuk
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'office_id' => $office->id,
            'date' => $today,
            'clock_in' => Carbon::now(),
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

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'You have not clocked in yet'], 404);
        }

        if ($attendance->clock_out) {
            return response()->json(['message' => 'You have already clocked out today'], 409);
        }

        $office = Office::first();
        $userLat = $request->input('latitude');
        $userLon = $request->input('longitude');

        // Validasi lokasi saat clock-out (opsional, bisa dihapus kalau tidak perlu)
        if ($userLat && $userLon) {
            $distance = $this->getDistance($userLat, $userLon, $office->latitude, $office->longitude);
            if ($distance > 100) {
                return response()->json([
                    'message' => 'You are too far from the office location to clock out',
                    'distance' => round($distance, 2)
                ], 403);
            }
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
        ]);

        $workDuration = null;
        if ($attendance->clock_in) {
            $workDuration = Carbon::parse($attendance->clock_in)
                ->diff(Carbon::parse($attendance->clock_out))
                ->format('%H:%I:%S');
        }

        return response()->json([
            'message' => 'Clock-out successful',
            'work_duration' => $workDuration,
            'data' => $attendance,
        ]);
    }
}
