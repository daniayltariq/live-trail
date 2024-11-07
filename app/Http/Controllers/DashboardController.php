<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Attendance;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with attendance data based on the user role.
     *
     * @return Response
     */
    public function index(): Response
    {
        // Check if the authenticated user is not an owner
        if(!auth()->user()->owner){

            // Fetch the attendance record for today's check-in for the current user
            $checkedInToday = Attendance::where('user_id', auth()->user()->id)
                ->whereNotNull('time_in')
                ->whereDate('date', now()->format('Y-m-d'))
                ->first();

            // Fetch the attendance record for today's check-out for the current user
            $checkedOutToday = Attendance::where('user_id', auth()->user()->id)
                ->whereNotNull('time_out')
                ->whereDate('date', now()->format('Y-m-d'))
                ->first();

            // Retrieve all attendance records for the current user
            // Transform each record to include only necessary data
            $attendance = Attendance::where('user_id', auth()->user()->id)
                ->get()
                ->transform(function($attendance){
                    return [
                        'id' => $attendance->id,
                        'name' => $attendance->user->name,
                        'date' => $attendance->date,
                        // Format check-in time to 12-hour format
                        'time_in' => $attendance->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_in)->format('h:i A') : null,
                        // Format check-out time to 12-hour format
                        'time_out' => $attendance->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_out)->format('h:i A') : null,
                        'status' => $attendance->status,
                    ];
                });

            // Render the User dashboard with today's check-in, check-out, and overall attendance data
            return Inertia::render('Dashboard/User/Index', [
                'checkedInToday' => $checkedInToday,
                'checkedOutToday' => $checkedOutToday,
                'attendance' => $attendance,
            ]);
        }

        // Count the number of non-owner users who have not checked in today
        $users = User::where('owner', false)
            ->whereDoesntHave('attendance', function($query){
                $query->where('date', now()->format('Y-m-d'));
            })
            ->count();

        // Retrieve today's attendance records with check-in times, ordered by latest check-in
        $attendanceTimeIn = Attendance::with('user')
            ->where('date', now()->format('Y-m-d'))
            ->whereNotNull('time_in')
            ->orderBy('time_in', 'desc')
            ->latest()
            ->get()
            ->transform(function($attendance){
                return [
                    'id' => $attendance->id,
                    'name' => $attendance->user->name,
                    // Format time-in to show date and 12-hour time format
                    'time_in' => $attendance->date . ' ' . ($attendance->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_in)->format('h:i A') : null),
                    // Format time-out to show date and 12-hour time format if available
                    'time_out' => $attendance->time_out ? $attendance->date . ' ' . \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_out)->format('h:i A') : null,
                    'status' => $attendance->status,
                ];
            });

        // Retrieve today's attendance records with check-out times, ordered by latest check-out
        $attendanceTimeOut = Attendance::with('user')
            ->where('date', now()->format('Y-m-d'))
            ->whereNotNull('time_out')
            ->orderBy('time_out', 'desc')
            ->latest()
            ->get()
            ->transform(function($attendance){
                return [
                    'id' => $attendance->id,
                    'name' => $attendance->user->name,
                    // Format time-in with date and 12-hour time format
                    'time_in' => $attendance->date . ' ' . \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_in)->format('h:i A'),
                    // Format time-out with date and 12-hour time format
                    'time_out' => $attendance->date . ' ' . \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_out)->format('h:i A'),
                    'status' => $attendance->status,
                ];
            });

        // Render the Admin dashboard with attendance data and the count of users who have not checked in
        return Inertia::render('Dashboard/Index', [
            'attendanceTimeIn' => $attendanceTimeIn,
            'attendanceTimeOut' => $attendanceTimeOut,
            'notCheckedInCount' => $users,
        ]);
    }
}
