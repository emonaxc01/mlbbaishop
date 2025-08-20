<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;
use App\Support\Request;
use App\Support\Mailer;
use PDO;

class AuthController
{
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register(): void
    {
        $this->startSession();
        $data = Request::json();
        $email = strtolower(trim($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            return App::json(['error' => 'Invalid email or password'], 422);
        }

        $pdo = DB::conn();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT id, is_verified FROM users WHERE email = ? FOR UPDATE');
            $stmt->execute([$email]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing && (int)$existing['is_verified'] === 1) {
                $pdo->rollBack();
                return App::json(['error' => 'Email already registered'], 409);
            }

            if ($existing) {
                $userId = (int)$existing['id'];
                // Update password in case user re-registers before verification
                $stmt = $pdo->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?');
                $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO users (email, password, is_verified, wallet_balance, created_at, updated_at) VALUES (?, ?, 0, 0, NOW(), NOW())');
                $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
                $userId = (int)$pdo->lastInsertId();
            }

            // Create OTP
            $code = random_int(100000, 999999);
            $hash = password_hash((string)$code, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO otps (user_id, code_hash, purpose, expires_at, attempts, created_at) VALUES (?, ?, "register", DATE_ADD(NOW(), INTERVAL 10 MINUTE), 0, NOW())');
            $stmt->execute([$userId, $hash]);

            $pdo->commit();

            // Send mail
            $sent = Mailer::send(
                to: $email,
                subject: 'Your GameTopUp verification code',
                htmlBody: '<p>Your verification code is <strong>' . $code . '</strong>. It expires in 10 minutes.</p>'
            );

            if (!$sent) {
                return App::json(['error' => 'Failed to send verification email'], 500);
            }

            return App::json(['message' => 'Verification code sent']);
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return App::json(['error' => 'Registration failed'], 500);
        }
    }

    public function verifyOtp(): void
    {
        $this->startSession();
        $data = Request::json();
        $email = strtolower(trim($data['email'] ?? ''));
        $code = trim((string)($data['code'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^\d{6}$/', $code)) {
            return App::json(['error' => 'Invalid input'], 422);
        }

        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT id, is_verified FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return App::json(['error' => 'User not found'], 404);
        }

        $userId = (int)$user['id'];

        $stmt = $pdo->prepare('SELECT id, code_hash, expires_at, attempts FROM otps WHERE user_id = ? AND purpose = "register" AND consumed_at IS NULL ORDER BY id DESC LIMIT 1');
        $stmt->execute([$userId]);
        $otp = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$otp) {
            return App::json(['error' => 'OTP not found'], 404);
        }

        if (strtotime($otp['expires_at']) < time()) {
            return App::json(['error' => 'OTP expired'], 410);
        }

        // Simple attempt limit
        if ((int)$otp['attempts'] >= 5) {
            return App::json(['error' => 'Too many attempts'], 429);
        }

        $ok = password_verify($code, $otp['code_hash']);
        if (!$ok) {
            $pdo->prepare('UPDATE otps SET attempts = attempts + 1 WHERE id = ?')->execute([(int)$otp['id']]);
            return App::json(['error' => 'Invalid code'], 401);
        }

        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE otps SET consumed_at = NOW() WHERE id = ?')->execute([(int)$otp['id']]);
            $pdo->prepare('UPDATE users SET is_verified = 1, updated_at = NOW() WHERE id = ?')->execute([$userId]);
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return App::json(['error' => 'Failed to verify'], 500);
        }

        // Auto login
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;

        return App::json(['message' => 'Verified', 'user' => ['email' => $email]]);
    }

    public function login(): void
    {
        $this->startSession();
        $data = Request::json();
        $email = strtolower(trim($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            return App::json(['error' => 'Invalid credentials'], 422);
        }

        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT id, password, is_verified FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($password, $user['password'])) {
            return App::json(['error' => 'Invalid credentials'], 401);
        }

        if ((int)$user['is_verified'] !== 1) {
            return App::json(['error' => 'Email not verified'], 403);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['email'] = $email;
        $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([(int)$user['id']]);

        return App::json(['message' => 'Logged in']);
    }

    public function logout(): void
    {
        $this->startSession();
        session_unset();
        session_destroy();
        return App::json(['message' => 'Logged out']);
    }

    public function me(): void
    {
        $this->startSession();
        if (!isset($_SESSION['user_id'])) {
            return App::json(['authenticated' => false]);
        }
        return App::json([
            'authenticated' => true,
            'user' => [
                'id' => (int)$_SESSION['user_id'],
                'email' => (string)($_SESSION['email'] ?? ''),
            ]
        ]);
    }
}
