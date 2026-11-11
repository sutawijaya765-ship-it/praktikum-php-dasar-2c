<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
// Keep runtime diagnostics in the server log instead of exposing paths or SQL details.
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('Cache-Control: no-store, private');
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function end_admin_session(string $notice=''): never
{
    $_SESSION=[];
    session_regenerate_id(true);
    csrf();
    header('Location: ?page=login'.($notice!==''?'&notice='.rawurlencode($notice):''));
    exit;
}
function require_admin(): void
{
    if (empty($_SESSION['user']) || !is_array($_SESSION['user']) || empty($_SESSION['user']['id'])) {
        header('Location: ?page=login');
        exit;
    }
    $userId=(int)$_SESSION['user']['id'];
    $lastActivity=$_SESSION['last_activity']??null;
    if (!is_int($lastActivity) || time()-$lastActivity>1800) end_admin_session('expired');

    // The password hash acts as a per-user revocation version without storing session rows.
    $query=db()->prepare("SELECT password FROM users WHERE id=? AND role='admin'");
    $query->execute([$userId]);
    $passwordHash=$query->fetchColumn();
    $storedVersion=$_SESSION['auth_version']??'';
    $currentVersion=is_string($passwordHash)&&$passwordHash!==''?hash_hmac('sha256',$passwordHash,APP_SECRET):'';
    if (!is_string($storedVersion) || $currentVersion==='' || !hash_equals($currentVersion,$storedVersion)) end_admin_session('expired');

    $_SESSION['last_activity']=time();
}
function post_csrf(): void { if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) { http_response_code(400); exit('Permintaan tidak valid. Muat ulang halaman.'); } }
function flash(string $text, string $type='success'): void { $_SESSION['flash']=['text'=>$text,'type'=>$type]; }
function go(string $page): never { header('Location: ?page='.rawurlencode($page)); exit; }
function authenticate_admin(PDO $pdo, string $username, string $password): array
{
    $pdo->exec('DELETE FROM login_attempts WHERE updated_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');
    $address = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $ipHash = hash_hmac('sha256', $address, APP_SECRET);
    $pdo->beginTransaction();
    try {
        $insert = $pdo->prepare('INSERT IGNORE INTO login_attempts(ip_hash,attempts,window_started_at) VALUES(?,0,NOW())');
        $insert->execute([$ipHash]);
        $stateQuery = $pdo->prepare('SELECT attempts, window_started_at, locked_until, (window_started_at <= DATE_SUB(NOW(),INTERVAL 15 MINUTE)) expired, IF(locked_until IS NULL,0,GREATEST(0,TIMESTAMPDIFF(SECOND,NOW(),locked_until))) remaining FROM login_attempts WHERE ip_hash=? FOR UPDATE');
        $stateQuery->execute([$ipHash]);
        $state = $stateQuery->fetch();
        if ((int)$state['expired'] === 1) {
            $pdo->prepare('UPDATE login_attempts SET attempts=0,window_started_at=NOW(),locked_until=NULL WHERE ip_hash=?')->execute([$ipHash]);
            $state['attempts'] = 0;
            $state['remaining'] = 0;
        }
        if ((int)$state['remaining'] > 0) {
            $pdo->commit();
            return ['user'=>null, 'remaining'=>(int)$state['remaining']];
        }

        $query = $pdo->prepare("SELECT id,name,username,password FROM users WHERE username=? AND role='admin'");
        $query->execute([$username]);
        $user = $query->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $pdo->prepare('DELETE FROM login_attempts WHERE ip_hash=?')->execute([$ipHash]);
            $pdo->commit();
            return ['user'=>$user, 'remaining'=>0];
        }

        $attempts = (int)$state['attempts'] + 1;
        $pdo->prepare('UPDATE login_attempts SET attempts=?,locked_until=IF(? >= 5,DATE_ADD(NOW(),INTERVAL 15 MINUTE),NULL) WHERE ip_hash=?')->execute([$attempts,$attempts,$ipHash]);
        $pdo->commit();
        return ['user'=>null, 'remaining'=>$attempts >= 5 ? 900 : 0];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $exception;
    }
}
set_exception_handler(function (Throwable $exception): void {
    error_log(sprintf('Unhandled application exception: %s at %s:%d', get_class($exception), $exception->getFile(), $exception->getLine()));
    if (!headers_sent()) {
        http_response_code($exception instanceof PDOException ? 503 : 500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Layanan sementara tidak tersedia</title><body><h1>Layanan sementara tidak tersedia</h1><p>Aplikasi belum dapat terhubung ke database. Silakan coba kembali beberapa saat lagi.</p></body></html>';
});
