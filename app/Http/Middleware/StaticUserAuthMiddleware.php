<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class StaticUserAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!Auth::check()) {
            $this->setStaticUser();
        }

        return $next($request);
    }

    private function setStaticUser(): void
    {
        // Check if there's a user ID stored in the cache
        $userId = Cache::get('static_user_id');


        if (empty($userId)) {
            // If not, fetch a random user and store their ID in the cache
            $user = User::inRandomOrder()->firstOrFail();
            Cache::forever('static_user_id', $user->id);
            $userId = $user->id;
        }


        // Set the static user as the authenticated user
        Auth::loginUsingId($userId);

    }

}
