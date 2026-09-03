<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TeamApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const NOT_A_MEMBER_MESSAGE = "This account isn't linked to a GG Prime membership. Please register or log in using your GG Prime email or mobile number.";

    private const UNREACHABLE_MESSAGE = "We're having trouble connecting to the GG Prime service right now. Please try again in a few minutes, or contact your administrator if the issue continues.";

    public function __construct(private TeamApiService $teamApi)
    {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $identifier = trim($credentials['login']);

        // Phone numbers are stored with the +91 country code (from GG Prime's
        // "mobile" field), but people naturally type their own number without
        // it — so a bare-digit login also tries the +91-prefixed form.
        $phoneCandidates = [$identifier];
        if (preg_match('/^\d{6,15}$/', $identifier)) {
            $phoneCandidates[] = '+91' . $identifier;
        }

        $account = User::where('email', $identifier)->orWhereIn('phone', $phoneCandidates)->first();

        if (! $account || ! Auth::attempt(['id' => $account->id, 'password' => $credentials['password']], $remember)) {
            return back()
                ->withErrors(['login' => 'These credentials do not match our records.'])
                ->onlyInput('login');
        }

        $user = Auth::user();

        if ($user->isBlocked()) {
            Auth::logout();

            return back()
                ->withErrors(['login' => 'Your account has been blocked. Please contact your administrator.'])
                ->onlyInput('login');
        }

        if (! $user->isAdmin() && ! $this->isBypassEmail($user->email)) {
            $ggProfile = $this->verifyGgPrimeMembership($user->email, $user->phone);

            if ($ggProfile === null) {
                Auth::logout();

                return back()->withErrors(['login' => self::UNREACHABLE_MESSAGE])->onlyInput('login');
            }

            if ($ggProfile === false) {
                Auth::logout();

                return back()->withErrors(['login' => self::NOT_A_MEMBER_MESSAGE])->onlyInput('login');
            }

            $this->syncGgProfile($user, $ggProfile);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathFor($user));
    }

    public function showRegister(Request $request): View
    {
        $referralCode = trim((string) $request->query('ref'));

        if ($referralCode !== '') {
            $request->session()->put('referral_code', $referralCode);
        }

        return view('auth.register', [
            'referralCode' => $request->session()->get('referral_code'),
        ]);
    }

    public function googleStub(Request $request): RedirectResponse
    {
        return redirect()->route('login')
            ->with('status', 'Google sign-in isn\'t connected yet. Please log in with your email and password for now.');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'required_without:phone', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $ggProfile = null;

        if (! $this->isBypassEmail($data['email'] ?? null)) {
            $ggProfile = $this->verifyGgPrimeMembership($data['email'] ?? null, $data['phone'] ?? null);

            if ($ggProfile === null) {
                return back()
                    ->withErrors(['email' => self::UNREACHABLE_MESSAGE])
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            if ($ggProfile === false) {
                return back()
                    ->withErrors(['email' => self::NOT_A_MEMBER_MESSAGE])
                    ->withInput($request->except('password', 'password_confirmation'));
            }
        }

        $referrer = User::where('referral_code', trim((string) $request->input('referral_code')))->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $ggProfile
                ? $this->resolveSyncedEmail($ggProfile['email'] ?? null, $data['email'] ?? null)
                : ($data['email'] ?? null),
            'phone' => $ggProfile['mobile'] ?? $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'referred_by' => $referrer?->id,
            'gg_user_id' => $ggProfile['user_id'] ?? null,
        ]);

        $request->session()->forget('referral_code');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathFor($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectPathFor(User $user): string
    {
        return $user->isAdmin() ? route('admin.dashboard') : route('user.dashboard');
    }

    /**
     * Test/QA accounts listed in TEAM_API_BYPASS_EMAILS skip GG Prime
     * verification entirely — for local/staging testing without a real
     * GG Prime membership. Leave empty in production.
     */
    private function isBypassEmail(?string $email): bool
    {
        return User::isGgBypassEmail($email);
    }

    /**
     * Checks against GG Prime by email first (if given), then by phone if
     * one is known. Returns the matched profile payload, `false` if they're
     * genuinely not a GG Prime member, or `null` if the API couldn't be
     * reached at all (a different message than "not a member"). At least
     * one of email/phone must be given — enforced by validation upstream.
     */
    private function verifyGgPrimeMembership(?string $email, ?string $phone): array|false|null
    {
        if ($email) {
            $result = $this->teamApi->profile(['email' => $email]);

            if ($result->status === 'found') {
                return $result->data;
            }

            if ($result->status === 'unreachable') {
                return null;
            }
        }

        if ($phone) {
            $phoneResult = $this->teamApi->profile(['mobile' => $this->normalizePhoneForApi($phone)]);

            if ($phoneResult->status === 'found') {
                return $phoneResult->data;
            }

            if ($phoneResult->status === 'unreachable') {
                return null;
            }
        }

        return false;
    }

    /**
     * GG Prime's own mobile lookup only matches the bare 10-digit number —
     * a "+91"-prefixed or otherwise formatted number returns "not found"
     * even when it exists, so always normalize before querying.
     */
    private function normalizePhoneForApi(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    /**
     * Keeps gg_user_id, phone, and email in sync with GG Prime's own records
     * on every login — so if someone registered with a mistyped phone or
     * email, it self-corrects to GG Prime's version, and a later login using
     * the correct GG Prime email/phone will find the account.
     */
    private function syncGgProfile(User $user, array $ggProfile): void
    {
        $updates = [];

        $ggUserId = isset($ggProfile['user_id']) ? (string) $ggProfile['user_id'] : null;
        if ($ggUserId && $ggUserId !== $user->gg_user_id) {
            $updates['gg_user_id'] = $ggUserId;
        }

        $ggPhone = $ggProfile['mobile'] ?? null;
        if ($ggPhone && $ggPhone !== $user->phone) {
            $updates['phone'] = $ggPhone;
        }

        $ggEmail = $this->resolveSyncedEmail($ggProfile['email'] ?? null, $user->email, excludeUserId: $user->id);
        if ($ggEmail !== $user->email) {
            $updates['email'] = $ggEmail;
        }

        if ($updates !== []) {
            $user->forceFill($updates)->save();
        }
    }

    /**
     * Prefers GG Prime's email over a mistyped/fallback one, but never
     * adopts it if another account already owns that email — emails are
     * unique, so silently switching to a taken one would crash the save.
     */
    private function resolveSyncedEmail(?string $ggEmail, ?string $fallback, ?int $excludeUserId = null): ?string
    {
        if (! $ggEmail || $ggEmail === $fallback) {
            return $fallback;
        }

        $taken = User::where('email', $ggEmail)
            ->when($excludeUserId, fn ($query) => $query->where('id', '!=', $excludeUserId))
            ->exists();

        return $taken ? $fallback : $ggEmail;
    }
}
