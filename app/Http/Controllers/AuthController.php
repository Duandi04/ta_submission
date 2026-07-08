<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    private function useGoogleRecaptcha(): bool
    {
        return !empty(config('services.recaptcha.site_key')) && !empty(config('services.recaptcha.secret_key'));
    }

    public function generateCaptcha()
    {
        $code = '';
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz';
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        session(['captcha_text' => strtolower($code)]);
        
        // Generate SVG CAPTCHA (completely offline, zero GD library dependency)
        $width = 130;
        $height = 48;
        
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}' style='background: #f1f5f9; border-radius: 6px;'>";
        
        // Noise lines
        for ($i = 0; $i < 5; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = rand(0, $width);
            $y2 = rand(0, $height);
            $color = "rgb(" . rand(180, 220) . "," . rand(180, 220) . "," . rand(180, 220) . ")";
            $strokeWidth = rand(1, 2);
            $svg .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='{$color}' stroke-width='{$strokeWidth}' />";
        }
        
        // Noise circles
        for ($i = 0; $i < 10; $i++) {
            $cx = rand(0, $width);
            $cy = rand(0, $height);
            $r = rand(2, 5);
            $color = "rgba(" . rand(180, 220) . "," . rand(180, 220) . "," . rand(180, 220) . ", 0.5)";
            $svg .= "<circle cx='{$cx}' cy='{$cy}' r='{$r}' fill='{$color}' />";
        }
        
        // Characters with fonts and transformations
        $fontFamilies = ['Arial', 'Verdana', 'Courier New', 'Georgia', 'Times New Roman'];
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            $font = $fontFamilies[rand(0, count($fontFamilies) - 1)];
            $fontSize = rand(22, 28);
            $x = 10 + ($i * 22) + rand(-3, 3);
            $y = 32 + rand(-4, 4);
            $angle = rand(-20, 20);
            $color = "rgb(" . rand(15, 90) . "," . rand(15, 90) . "," . rand(15, 90) . ")";
            
            $svg .= "<text x='{$x}' y='{$y}' font-family='{$font}' font-size='{$fontSize}' font-weight='bold' fill='{$color}' transform='rotate({$angle} {$x} {$y})'>{$char}</text>";
        }
        
        $svg .= "</svg>";
        
        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        // Check if rate limiter locked (3 attempts, 5 minutes block)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Akun Anda dikunci sementara. Silakan coba lagi dalam {$minutes} menit.",
            ])->onlyInput('email');
        }

        // Validate CAPTCHA (bypass in unit tests)
        if (!app()->runningUnitTests()) {
            if ($this->useGoogleRecaptcha()) {
                $token = $request->input('g-recaptcha-response');
                if (empty($token) || !$this->verifyGoogleRecaptcha($token, $request->ip())) {
                    RateLimiter::hit($key, 300);
                    return back()->withErrors([
                        'captcha' => 'Verifikasi Google reCAPTCHA gagal. Silakan coba lagi.',
                    ])->onlyInput('email');
                }
            } else {
                $captcha = $request->input('captcha');
                $expected = session('captcha_text');

                if ($captcha === null || strtolower($captcha) !== $expected) {
                    RateLimiter::hit($key, 300); // 300 seconds = 5 minutes lockout
                    return back()->withErrors([
                        'captcha' => 'Jawaban CAPTCHA salah.',
                    ])->onlyInput('email');
                }
            }
        }

        if ($this->authService->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->forget('captcha_text');
            $this->authService->loginUser($request);

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($key, 300);

        return back()->withErrors([
            'email' => 'Email atau password salah, atau akun Anda belum aktif.',
        ])->onlyInput('email');
    }

    private function verifyGoogleRecaptcha(string $token, string $ip): bool
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);
            return $response->json('success') === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()->route('login');
    }
}
