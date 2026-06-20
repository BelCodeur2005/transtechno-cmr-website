<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Mise à jour toutes les 5 minutes max pour éviter une écriture à chaque requête.
            $user = Auth::user();
            if (! $user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 5) {
                $user->updateQuietly(['last_seen_at' => now()]);
            }
        }

        return $next($request);
    }
}
