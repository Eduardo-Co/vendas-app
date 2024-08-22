<?php

namespace App\Http\Controllers;

use App\Models\Playlist;

class PlaylistController extends Controller
{
    public function show($id)
    {
        $playlist = Playlist::findOrFail($id);

        return view('playlists.show', [
            'playlistId' => $playlist->id
        ]);
    }
}
