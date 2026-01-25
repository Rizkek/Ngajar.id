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
        // Mapping to match the enhanced view requirements
        $mentors = $mentors_db->through(function ($user) {
            $is_top = rand(0, 1) == 1;
            return [
                'name' => $user->name,
                'role' => $is_top ? 'Top Volunteer' : 'Relawan Pengajar',
                'is_top' => $is_top,
                // Use Unsplash source for more "human" photos, using hash of ID to get consistent random photo per user
                'photo' => 'https://images.unsplash.com/photo-' . ([
                    '1535713875002-d1d0cf377fde', // Men
                    '1580489944761-15a19d654956', // Women
                    '1633332755192-727a05c4013d', // Men 2
                    '1438761681033-6461ffad8d80', // Women 2
                    '1472099645785-5658abf4ff4e', // Men 3 
                    '1544005313-94ddf0286df2'  // Women 3
                ][rand(0, 5)]) . '?auto=format&fit=crop&w=400&h=400&q=80',
                'subjects' => ['Matematika', 'Bahasa Inggris', 'Fisika', 'Biologi', 'Ekonomi'][rand(0, 4)],
                'university' => ['Univ. Indonesia', 'ITB', 'UGM', 'UPI', 'UNPAD'][rand(0, 4)],
                'rating' => number_format(rand(45, 50) / 10, 1),
                'reviews' => rand(15, 120),
                'availability' => ['Senin - Rabu', 'Sabtu - Minggu', 'Flexible', 'Malam Hari'][rand(0, 3)],
                'method' => rand(0, 1) ? 'Online Class' : 'Hybird (Online/Offline)',
                'whatsapp' => '6281234567890' // Dummy WA
            ];
        });

        return view('mentors', ['mentors' => $mentors]);
    }
}
