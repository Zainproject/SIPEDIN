<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Ambil list aktivitas (untuk navbar).
     * Default: 5 terakhir.
     */
    public function navbar(Request $request)
    {
        $limit = (int) ($request->get('limit', 5));
        $limit = $limit > 0 ? min($limit, 20) : 5;

        $userId = Auth::id(); // ✅ aman (tidak bikin error intelephense)

        $activities = Activity::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->take($limit)
            ->get();

        // Kalau kamu mau dipakai AJAX/JSON:
        if ($request->wantsJson()) {
            return response()->json($activities);
        }

        // Kalau kamu mau dipakai sebagai partial view navbar:
        // bikin view: resources/views/partials/activity_dropdown.blade.php
        return view('partials.activity_dropdown', compact('activities'));
    }

    /**
     * Halaman list aktivitas (kalau suatu saat butuh).
     * (boleh tidak dipakai)
     */
    public function index()
    {
        $userId = Auth::id();

        $activities = Activity::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->paginate(15);

        return view('activity.index', compact('activities'));
    }

    /**
     * Hapus semua aktivitas user (tombol "Hapus Aktivitas").
     */
    public function clear()
    {
        $userId = Auth::id();

        Activity::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->delete();

        return back()->with('success', 'Aktivitas berhasil dihapus.');
    }
}
