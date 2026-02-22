<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class HoneypotProtection
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('website')) {
            return $this->fakeSuccess($request);
        }

        if ($formToken = $request->input('_ft')) {
            try {
                $timestamp = (int) Crypt::decryptString($formToken);

                if (now()->timestamp - $timestamp < 3) {
                    return $this->tooFast($request);
                }
            } catch (\Exception) {
                return $this->fakeSuccess($request);
            }
        }

        return $next($request);
    }

    private function fakeSuccess(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Kehadiran berjaya disahkan.']);
        }

        return redirect()->back()->with('success', 'Kehadiran berjaya disahkan.');
    }

    private function tooFast(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sila lengkapkan borang dengan betul.'], 422);
        }

        return redirect()->back()->with('error', 'Sila lengkapkan borang dengan betul.');
    }
}
