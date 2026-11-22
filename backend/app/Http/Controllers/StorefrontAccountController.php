<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class StorefrontAccountController extends Controller
{
    public function session(Request $request): JsonResponse
    {
        $user = $request->user('storefront');
        $customer = $user?->isCustomer() && $user->isActive() ? $user : null;

        return response()->json([
            'googleAvailable' => $this->googleAvailable(),
            'customer' => $customer ? [
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ] : null,
            'csrfToken' => $request->session()->token(),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'method' => 'required|in:email,phone',
            'identity' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);
        $match = $data['method'] === 'email'
            ? $this->emailQuery($data['identity'])
            : fn (Builder $query) => $query->whereIn('phone', $this->phoneVariants($data['identity']));

        if (! Auth::guard('storefront')->attempt([
            'role' => 'customer',
            'status' => 'active',
            'password' => $data['password'],
            $match,
        ])) {
            throw ValidationException::withMessages(['identity' => 'بيانات الدخول غير صحيحة.']);
        }

        return $this->session($request);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'password' => $this->passwordRules(),
        ]);
        $matchEmail = $this->emailQuery($data['email']);
        $phones = filled($data['phone'] ?? null) ? $this->phoneVariants($data['phone']) : [];
        if (filled($data['phone'] ?? null) && ! $phones) {
            throw ValidationException::withMessages(['phone' => 'اكتب رقم موبايل مصري صحيح.']);
        }
        if ($matchEmail(User::withTrashed())->exists()
            || ($phones && User::withTrashed()->whereIn('phone', $phones)->exists())) {
            throw ValidationException::withMessages(['email' => 'البيانات مرتبطة بحساب موجود. جرّب تسجيل الدخول أو استعادة كلمة المرور.']);
        }

        try {
            $customer = User::create([
                'name' => $data['name'],
                'email' => Str::lower($data['email']),
                'phone' => $phones[0] ?? null,
                'password' => $data['password'],
                'role' => 'customer',
                'status' => 'active',
            ]);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'البيانات مرتبطة بحساب موجود. جرّب تسجيل الدخول أو استعادة كلمة المرور.']);
        }

        Auth::guard('storefront')->login($customer);

        return $this->session($request)->setStatusCode(201);
    }

    public function forgotPassword(Request $request): Response
    {
        $data = $request->validate(['email' => 'required|email|max:255']);
        abort_if(config('mail.default') === 'log', 503, 'إرسال رابط الاستعادة غير متاح حاليًا.');
        try {
            Password::sendResetLink($this->customerEmail($data['email']), function (User $user, string $token): void {
                $url = rtrim(config('app.frontend_url'), '/') . '/account?' . http_build_query([
                    'reset' => $token,
                    'email' => $user->email,
                ]);
                Mail::raw("لتعيين كلمة مرور جديدة لحسابك في ORQ، افتح الرابط التالي:\n{$url}\nإذا لم تطلب ذلك، يمكنك تجاهل الرسالة.", function ($mail) use ($user): void {
                    $mail->to($user->email)->subject('تعيين كلمة مرور حساب ORQ');
                });
            });
        } catch (Throwable $exception) {
            Log::warning('Storefront password reset email failed', ['exception_type' => $exception::class]);
        }

        return response()->noContent()->header('Cache-Control', 'private, no-store');
    }

    public function resetPassword(Request $request): Response
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'token' => 'required|string|max:255',
            'password' => $this->passwordRules(),
        ]);
        $result = Password::reset([
            ...$this->customerEmail($data['email']),
            'token' => $data['token'],
            'password' => $data['password'],
        ], function (User $user, string $password): void {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });
        if ($result !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['token' => 'رابط تعيين كلمة المرور غير صالح أو انتهت مدته.']);
        }

        return response()->noContent()->header('Cache-Control', 'private, no-store');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'required|string|max:120']);
        $request->user('storefront')->update(['name' => $data['name']]);

        return $this->session($request);
    }

    public function redirectToGoogle(): RedirectResponse
    {
        if (! $this->googleAvailable()) {
            return $this->accountPage('unavailable');
        }

        return Socialite::driver('google')->enablePKCE()->redirect()
            ->header('Cache-Control', 'private, no-store');
    }

    public function googleCallback(Request $request): RedirectResponse
    {
        if (! $this->googleAvailable()) {
            return $this->accountPage('unavailable');
        }

        try {
            if (! $request->filled('code')) {
                return $this->accountPage($request->query('error') === 'access_denied' ? 'cancelled' : 'failed');
            }

            // Socialite validates the session state before exchanging the authorization code.
            $google = Socialite::driver('google')->enablePKCE()->user();
        } catch (InvalidStateException) {
            return $this->accountPage('failed');
        } catch (Throwable $exception) {
            Log::warning('Storefront Google sign-in could not complete', ['exception_type' => $exception::class]);

            return $this->accountPage('failed');
        } finally {
            $request->session()->forget(['state', 'code_verifier']);
        }

        $subject = $google->getId();
        $email = Str::lower(trim((string) $google->getEmail()));
        if (! is_string($subject) || $subject === '' || strlen($subject) > 255
            || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255
            || ($google->getRaw()['email_verified'] ?? false) !== true) {
            return $this->accountPage('failed');
        }

        $customer = User::withTrashed()->where('google_id', $subject)->first();

        if (! $customer) {
            if (User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                return $this->accountPage('existing');
            }

            try {
                $customer = User::firstOrCreate(['google_id' => $subject], [
                    'name' => Str::limit(trim((string) $google->getName()) ?: Str::before($email, '@'), 255, ''),
                    'email' => $email,
                    'password' => Str::random(64),
                    'role' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            } catch (UniqueConstraintViolationException) {
                return $this->accountPage('existing');
            }
        }

        if ($customer->trashed() || ! $customer->isCustomer() || ! $customer->isActive()) {
            return $this->accountPage('failed');
        }

        Auth::guard('storefront')->login($customer);

        return $this->accountPage();
    }

    public function logout(Request $request): Response
    {
        Auth::guard('storefront')->logoutCurrentDevice();
        $request->session()->regenerate(true);

        return response()->noContent()->header('Cache-Control', 'private, no-store');
    }

    public function accountPage(?string $reason = null): RedirectResponse
    {
        $url = rtrim(config('app.frontend_url'), '/') . '/account';

        return redirect()->away($url . ($reason ? '?google=' . $reason : ''))
            ->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }

    private function googleAvailable(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

    private function emailQuery(string $email): \Closure
    {
        return fn (Builder $query) => $query->whereRaw('LOWER(email) = ?', [Str::lower(trim($email))]);
    }

    private function customerEmail(string $email): array
    {
        return ['role' => 'customer', 'status' => 'active', $this->emailQuery($email)];
    }

    private function phoneVariants(string $phone): array
    {
        $digits = strtr($phone, array_combine(
            preg_split('//u', '٠١٢٣٤٥٦٧٨٩۰۱۲۳۴۵۶۷۸۹', -1, PREG_SPLIT_NO_EMPTY),
            str_split('01234567890123456789'),
        ));
        $digits = preg_replace('/[\s()+-]+/u', '', $digits);
        if (! preg_match('/^(?:0|0020|20)?(1[0125][0-9]{8})$/D', $digits, $matches)) {
            return [];
        }

        $mobile = $matches[1];

        return ['0'.$mobile, '+20'.$mobile, '0020'.$mobile, '20'.$mobile, $mobile];
    }

    private function passwordRules(): array
    {
        return ['bail', 'required', 'string', 'min:8', 'confirmed', function ($attribute, $value, $fail) {
            if (strlen($value) > 72) {
                $fail('كلمة المرور أطول من الحد المسموح.');
            }
        }];
    }
}
