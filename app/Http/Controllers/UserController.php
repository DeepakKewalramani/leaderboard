<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function users(Request $request, $id = null)
    {
        try {

            $filter = $request->query('filter'); // day | month | year

            if (in_array($filter, ['day', 'month', 'year'])) {
                return $this->filteredUsers($filter, $id);
            }
            $query = UserPoint::select('user_id', 'rank', 'total_points')
                ->with('user:id,full_name')->orderBy('rank', 'asc');

            if ($id) {
                $query->where('user_id', $id);
            }

            $user = $query->get();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            if ($user->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No users found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User fetch successfully',
                'users' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recalculate()
    {
        try {
            Artisan::call('app:recalculate');
            return response()->json([
                'status' => 'success',
                'message' => 'Recalculation successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generate()
    {
        try {

            Artisan::call('db:seed');
            return response()->json([
                'status' => 'success',
                'message' => 'Database seeded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function addPoint(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
            ]);

            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $activityTypes = UserActivity::ACTIVITY_TYPES;

            UserActivity::create([
                'user_id' => $request->user_id,
                'name' => $activityTypes[array_rand($activityTypes)],
                'date' => now()->format('Y-m-d'),
                'points' => 20
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Points added successfully. Please recalculate to update rankings."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    protected function filteredUsers(string $filter, $id = null)
    {
        $query = UserActivity::select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id');

        // Apply filter based on time
        switch ($filter) {
            case 'day':
                $query->whereDate('date', now()->toDateString());
                break;
            case 'month':
                $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
                break;
            case 'year':
                $query->whereYear('date', now()->year);
                break;
        }

        if ($id) {
            $query->where('user_id', $id);
        }

        $users = $query->get();

        // Add user info and rank
        $ranked = $users->sortByDesc('total_points')->values();
        foreach ($ranked as $index => $user) {
            $user->rank = $index + 1;
            $user->user = User::select('id', 'full_name')->find($user->user_id);
        }

        return response()->json([
            'status' => 'success',
            'message' => $users->isEmpty() ? 'No users found for filter' : 'Filtered data fetched',
            'users' => $ranked
        ]);
    }
}
