<?php

namespace App\Http\Middleware;

use App\Services\FirebaseUserService;
use Closure;
use App\Services\FirebaseAuthService;
use Illuminate\Http\Request;

class FirebaseAuthMiddleware
{
    protected $firebaseAuthService;

    public function __construct(FirebaseUserService $firebaseAuthService)
    {
        $this->firebaseAuthService = $firebaseAuthService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Récupère le token de l'en-tête Authorization
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token is required'], 401);
        }

        try {
            // Vérifie le token via le service FirebaseAuthService
            $uid = $this->firebaseAuthService->verifyToken($token);

            if ($uid) {
                // Associe l'UID utilisateur à la requête
                $request->attributes->set('uid', $uid);

                // Passe la requête à la prochaine étape
                return $next($request);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }
}
