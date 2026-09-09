<?php

namespace App\Http\Controllers\Backend;

use App\Models\WithdrawSchedule;
use Illuminate\Http\Request;

class WithdrawScheduleController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index|update' => 'withdraw-schedule',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $withdrawSchedules = WithdrawSchedule::all();

        return view('backend.withdraw.schedule.index', compact('withdrawSchedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Get the submitted statuses
        $statuses = $request->input('status', []); // Retrieve only the toggled "Active" statuses

        // Retrieve all existing schedules from the database
        $withdrawSchedules = WithdrawSchedule::all();

        foreach ($withdrawSchedules as $schedule) {
            // Determine the boolean status: true for Active, false for Inactive
            $isActive = isset($statuses[$schedule->day]);
            // Update the status for the existing schedule
            $schedule->update(['status' => $isActive]);
        }

        // Display a success message
        notifyEvs('success', __('Withdraw schedule updated successfully!'));

        return redirect()->back();
    }
}
