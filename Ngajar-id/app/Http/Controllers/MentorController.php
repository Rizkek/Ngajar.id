<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    /**
     * Display a listing of the mentors (volunteers).
     */
    public function index()
    {
        // Fetch users with 'pengajar' role, paginate/get all
        // In real app, you might want to filter by subjects, but current DB doesn't have 'subjects' column on user
        // We will assume 'name' and 'email' are available. 
        // For the view, we'll map necessary fields. 

        $mentors_db = User::pengajar()
            ->aktif()
            ->latest()
            ->paginate(12);

        // Transform/Map if necessary to match view expectations if view expects specific array keys
        // or just pass the paginated object. 
        // The view 'mentors' currently expects an array array of data. 
        // Let's adjust the view later to handle the paginated collection object, 
        // OR map it here to match the current view structure for minimal breakage.

        // Let's refactor the View to accept the collection ideally, but for now let's pass it as is 
        // and let the view handle it or we map it. 
        // Since I can't see the mentors.blade.php right now, I'll pass the collection 
        // and we might need to adjust the view slightly or map here.

        // Mapping to match the mock data structure previously used in routes/web.php
        $mentors = $mentors_db->through(function ($user) {
            return [
                'name' => $user->name,
                'role' => 'Relawan Pengajar', // Default
                'photo' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=200',
                'subjects' => 'Umum', // Placeholder as DB doesn't have it yet
                'university' => 'Indonesia', // Placeholder
                'rating' => '5.0', // Placeholder
                'reviews' => rand(10, 100), // Random placeholder
            ];
        });

        return view('mentors', ['mentors' => $mentors]);
    }
}
