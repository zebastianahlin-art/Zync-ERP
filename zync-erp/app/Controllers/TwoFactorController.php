<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\TotpService;
use App\Models\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TwoFactorController extends Controller
{
    private TotpService    $totp;
    private UserRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->totp = new TotpService();
        $this->repo = new UserRepository();
    }

    /**
     * GET /2fa/setup
     * Show a QR-code URI and the raw secret so the user can add it to their
     * authenticator app. The generated secret is stored in the session until
     * the user confirms it with a valid code.
     */
    public function setup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->redirect($response, '/login');
        }

        $user = $this->repo->findById($userId);
        if ($user === null) {
            return $this->redirect($response, '/login');
        }

        // Keep the same secret if the user reloads the page mid-setup
        if (empty($_SESSION['totp_setup_secret'])) {
            $_SESSION['totp_setup_secret'] = $this->totp->generateSecret();
        }

        $secret    = $_SESSION['totp_setup_secret'];
        $qrCodeUri = $this->totp->getQrCodeUri($secret, $user->email);

        return $this->render($response, '2fa/setup', [
            'title'     => 'Aktivera tvåfaktorsautentisering – ZYNC ERP',
            'secret'    => $secret,
            'qrCodeUri' => $qrCodeUri,
            'enabled'   => $user->totpEnabled === 1,
            'error'     => Flash::get('error'),
            'success'   => Flash::get('success'),
        ]);
    }

    /**
     * POST /2fa/enable
     * Verify the code entered by the user and, if valid, persist the secret
     * and mark TOTP as enabled for the account.
     */
    public function enable(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->redirect($response, '/login');
        }

        $secret = (string) ($_SESSION['totp_setup_secret'] ?? '');
        if ($secret === '') {
            Flash::set('error', 'Sessionen har gått ut. Försök igen.');
            return $this->redirect($response, '/2fa/setup');
        }

        $body = (array) $request->getParsedBody();
        $code = trim((string) ($body['code'] ?? ''));

        if (!$this->totp->verifyCode($secret, $code)) {
            Flash::set('error', 'Felaktig kod. Kontrollera din autentiseringsapp och försök igen.');
            return $this->redirect($response, '/2fa/setup');
        }

        $this->repo->enableTotp($userId, $secret);
        unset($_SESSION['totp_setup_secret']);

        Flash::set('success', 'Tvåfaktorsautentisering har aktiverats för ditt konto.');
        return $this->redirect($response, '/2fa/setup');
    }

    /**
     * GET /2fa/verify
     * Show the 6-digit code entry form. Displayed after password login when
     * the user has 2FA enabled.
     */
    public function verify(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        // Already fully authenticated — go to dashboard
        if (!Auth::is2faPending()) {
            return $this->redirect($response, '/dashboard');
        }

        return $this->render($response, '2fa/verify', [
            'title' => 'Verifiera inloggning – ZYNC ERP',
            'error' => Flash::get('error'),
        ]);
    }

    /**
     * POST /2fa/verify
     * Verify the submitted TOTP code. On success the session is marked as
     * fully authenticated and the user is redirected to the dashboard.
     */
    public function verifyPost(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        if (!Auth::is2faPending()) {
            return $this->redirect($response, '/dashboard');
        }

        $userId = Auth::id();
        if ($userId === null) {
            return $this->redirect($response, '/login');
        }

        $user = $this->repo->findById($userId);
        if ($user === null || $user->totpSecret === null) {
            // 2FA inconsistency — clear the pending flag and continue
            Auth::complete2fa();
            return $this->redirect($response, '/dashboard');
        }

        $body = (array) $request->getParsedBody();
        $code = trim((string) ($body['code'] ?? ''));

        if (!$this->totp->verifyCode($user->totpSecret, $code)) {
            Flash::set('error', 'Felaktig kod. Försök igen.');
            return $this->redirect($response, '/2fa/verify');
        }

        Auth::complete2fa();
        return $this->redirect($response, '/dashboard');
    }

    /**
     * POST /2fa/disable
     * Require a valid TOTP code before disabling 2FA to prevent account
     * takeover if an attacker has only the password.
     */
    public function disable(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = Auth::id();
        if ($userId === null) {
            return $this->redirect($response, '/login');
        }

        $user = $this->repo->findById($userId);
        if ($user === null || $user->totpSecret === null || $user->totpEnabled !== 1) {
            Flash::set('error', 'Tvåfaktorsautentisering är inte aktiverad för ditt konto.');
            return $this->redirect($response, '/2fa/setup');
        }

        $body = (array) $request->getParsedBody();
        $code = trim((string) ($body['code'] ?? ''));

        if (!$this->totp->verifyCode($user->totpSecret, $code)) {
            Flash::set('error', 'Felaktig kod. Tvåfaktorsautentisering har inte inaktiverats.');
            return $this->redirect($response, '/2fa/setup');
        }

        $this->repo->disableTotp($userId);

        Flash::set('success', 'Tvåfaktorsautentisering har inaktiverats för ditt konto.');
        return $this->redirect($response, '/2fa/setup');
    }
}
