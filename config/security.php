<?php

/**
 * ============================================================
 *  security.php — Couche de sécurité complète
 *  Couvre : CSRF, XSS, SQLi, Headers HTTP, Sessions sécurisées,
 *           Rate Limiting, Brute-force, Path Traversal,
 *           Clickjacking, MIME Sniffing, Upload sécurisé, etc.
 * ============================================================
 */

declare(strict_types=1);

// ============================================================
// 1. CONFIGURATION GÉNÉRALE
// ============================================================

define('SECURITY_VERSION', '1.0.0');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900);       // 15 minutes en secondes
define('RATE_LIMIT_REQUESTS', 100);    // requêtes max par fenêtre
define('RATE_LIMIT_WINDOW', 60);       // fenêtre en secondes
define('CSRF_TOKEN_LENGTH', 64);
define('SESSION_LIFETIME', 1800);      // 30 minutes
define('ALLOWED_UPLOAD_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf']);
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 Mo

// ============================================================
// 2. HEADERS HTTP SÉCURISÉS
// ============================================================

function security_set_headers(): void
{
    // Empêche le clickjacking
    header('X-Frame-Options: DENY');

    // Empêche le MIME sniffing
    header('X-Content-Type-Options: nosniff');

    // Active le filtre XSS du navigateur (legacy)
    header('X-XSS-Protection: 1; mode=block');

    // Force HTTPS (à activer uniquement en production HTTPS)
    // header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

    // Content Security Policy — adapter selon votre app
    header(
        "Content-Security-Policy: " .
        "default-src 'self'; " .
        "script-src 'self'; " .
        "style-src 'self' 'unsafe-inline'; " .
        "img-src 'self' data: https:; " .
        "font-src 'self'; " .
        "connect-src 'self'; " .
        "frame-ancestors 'none'; " .
        "base-uri 'self'; " .
        "form-action 'self';"
    );

    // Contrôle du referrer
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions API (désactive caméra, micro, géoloc par défaut)
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    // Supprime les informations serveur
    header_remove('X-Powered-By');
    header_remove('Server');
}


// ============================================================
// 3. SESSION SÉCURISÉE
// ============================================================

function security_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // Configuration stricte de la session
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
    ini_set('session.entropy_length', '32');

    // Activer Secure uniquement en HTTPS
    // ini_set('session.cookie_secure', '1');

    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'] ?? '',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();

    // Régénère l'ID de session périodiquement (anti-fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 300) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }

    // Validation User-Agent (anti-hijacking basique)
    $ua_hash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
    if (isset($_SESSION['_ua_hash']) && $_SESSION['_ua_hash'] !== $ua_hash) {
        security_destroy_session();
        security_abort(403, 'Session invalide détectée.');
    }
    $_SESSION['_ua_hash'] = $ua_hash;

    // Expiration manuelle de session
    if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity'] > SESSION_LIFETIME)) {
        security_destroy_session();
        security_abort(401, 'Session expirée.');
    }
    $_SESSION['_last_activity'] = time();
}

function security_destroy_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}


// ============================================================
// 4. PROTECTION CSRF
// ============================================================

function csrf_generate_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        security_start_session();
    }

    $token = bin2hex(random_bytes(CSRF_TOKEN_LENGTH / 2));
    $_SESSION['_csrf_token']   = $token;
    $_SESSION['_csrf_time']    = time();

    return $token;
}

function csrf_get_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        return csrf_generate_token();
    }
    return $_SESSION['_csrf_token'];
}

function csrf_html_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(csrf_get_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_validate(string $method = 'POST'): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        security_start_session();
    }

    $submitted = match(strtoupper($method)) {
        'POST'   => $_POST['_csrf_token']   ?? '',
        'GET'    => $_GET['_csrf_token']    ?? '',
        default  => $_REQUEST['_csrf_token'] ?? '',
    };

    $stored = $_SESSION['_csrf_token'] ?? '';
    $time   = $_SESSION['_csrf_time']  ?? 0;

    // Le token expire après SESSION_LIFETIME
    if ((time() - $time) > SESSION_LIFETIME) {
        security_abort(403, 'Token CSRF expiré.');
    }

    if (empty($submitted) || !hash_equals($stored, $submitted)) {
        security_abort(403, 'Token CSRF invalide.');
    }

    // Rotation du token après usage (one-time token)
    csrf_generate_token();
}


// ============================================================
// 5. PROTECTION XSS
// ============================================================

/**
 * Échappe une valeur pour affichage HTML.
 */
function xss_clean(mixed $value): string
{
    if (is_array($value)) {
        return htmlspecialchars(json_encode($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Nettoie récursivement un tableau (ex: $_GET, $_POST).
 */
function xss_clean_array(array $data): array
{
    return array_map(fn($v) => is_array($v) ? xss_clean_array($v) : xss_clean($v), $data);
}

/**
 * Purge les balises dangereuses dans du HTML riche (nécessite HTML Purifier ou strip_tags).
 */
function xss_sanitize_html(string $html, array $allowed_tags = []): string
{
    if (empty($allowed_tags)) {
        return strip_tags($html);
    }
    $allowed = '<' . implode('><', $allowed_tags) . '>';
    return strip_tags($html, $allowed);
}


// ============================================================
// 6. PROTECTION INJECTION SQL
// ============================================================

/**
 * Retourne une connexion PDO sécurisée.
 */
function db_connect(string $dsn, string $user, string $pass): PDO
{
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // IMPORTANT : désactive l'émulation
        PDO::ATTR_STRINGIFY_FETCHES  => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        // Ne jamais exposer les détails de connexion
        security_abort(500, 'Erreur de connexion à la base de données.');
    }
}

/**
 * Exemple de requête préparée sécurisée.
 * Toujours utiliser des requêtes préparées — JAMAIS de concaténation.
 *
 * Exemple d'usage :
 *   $user = db_query($pdo, "SELECT * FROM users WHERE email = ?", [$email])->fetch();
 */
function db_query(PDO $pdo, string $sql, array $params = []): \PDOStatement
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}


// ============================================================
// 7. RATE LIMITING (basé sur fichiers — remplacer par Redis en prod)
// ============================================================

function rate_limit_check(string $identifier, int $max = RATE_LIMIT_REQUESTS, int $window = RATE_LIMIT_WINDOW): void
{
    $key  = sys_get_temp_dir() . '/rl_' . hash('sha256', $identifier);
    $data = ['count' => 0, 'start' => time()];

    if (file_exists($key)) {
        $data = json_decode(file_get_contents($key), true);
        if ((time() - $data['start']) > $window) {
            $data = ['count' => 0, 'start' => time()];
        }
    }

    $data['count']++;
    file_put_contents($key, json_encode($data), LOCK_EX);

    if ($data['count'] > $max) {
        header('Retry-After: ' . $window);
        security_abort(429, 'Trop de requêtes. Réessayez dans ' . $window . ' secondes.');
    }
}


// ============================================================
// 8. PROTECTION BRUTE-FORCE (connexion)
// ============================================================

function brute_force_check(string $identifier): void
{
    $key  = sys_get_temp_dir() . '/bf_' . hash('sha256', $identifier);
    $data = ['attempts' => 0, 'last' => time(), 'locked_until' => 0];

    if (file_exists($key)) {
        $data = json_decode(file_get_contents($key), true);
    }

    if (time() < ($data['locked_until'] ?? 0)) {
        $wait = $data['locked_until'] - time();
        security_abort(429, "Compte verrouillé. Réessayez dans {$wait} secondes.");
    }
}

function brute_force_register_failure(string $identifier): void
{
    $key  = sys_get_temp_dir() . '/bf_' . hash('sha256', $identifier);
    $data = ['attempts' => 0, 'last' => time(), 'locked_until' => 0];

    if (file_exists($key)) {
        $data = json_decode(file_get_contents($key), true);
    }

    $data['attempts']++;
    $data['last'] = time();

    if ($data['attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $data['locked_until'] = time() + LOCKOUT_DURATION;
    }

    file_put_contents($key, json_encode($data), LOCK_EX);
}

function brute_force_reset(string $identifier): void
{
    $key = sys_get_temp_dir() . '/bf_' . hash('sha256', $identifier);
    if (file_exists($key)) {
        unlink($key);
    }
}


// ============================================================
// 9. VALIDATION DES ENTRÉES
// ============================================================

class Validator
{
    private array $errors = [];

    public function email(string $value, string $field = 'email'): static
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Adresse email invalide.';
        }
        return $this;
    }

    public function url(string $value, string $field = 'url'): static
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = 'URL invalide.';
        }
        return $this;
    }

    public function integer(mixed $value, string $field, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): static
    {
        $int = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
        if ($int === false) {
            $this->errors[$field] = "Entier invalide (min: {$min}, max: {$max}).";
        }
        return $this;
    }

    public function length(string $value, string $field, int $min = 1, int $max = 255): static
    {
        $len = mb_strlen($value, 'UTF-8');
        if ($len < $min || $len > $max) {
            $this->errors[$field] = "Longueur invalide (entre {$min} et {$max} caractères).";
        }
        return $this;
    }

    public function regex(string $value, string $pattern, string $field): static
    {
        if (!preg_match($pattern, $value)) {
            $this->errors[$field] = "Format invalide pour le champ {$field}.";
        }
        return $this;
    }

    public function noSQLInjection(string $value, string $field): static
    {
        $patterns = ['/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bDELETE\b|\bDROP\b|\bEXEC\b)/i', '/[\'";]--/', '/\/\*/'];
        foreach ($patterns as $p) {
            if (preg_match($p, $value)) {
                $this->errors[$field] = "Contenu non autorisé dans {$field}.";
                break;
            }
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return array_values($this->errors)[0] ?? '';
    }
}


// ============================================================
// 10. UPLOAD DE FICHIERS SÉCURISÉ
// ============================================================

function upload_validate(array $file, string $upload_dir): string
{
    // Vérification erreur PHP
    if ($file['error'] !== UPLOAD_ERR_OK) {
        security_abort(400, 'Erreur lors de l\'upload : code ' . $file['error']);
    }

    // Taille max
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        security_abort(400, 'Fichier trop volumineux (max ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . ' Mo).');
    }

    // Vérification MIME réel (pas le header envoyé par le client)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mime     = $finfo->file($file['tmp_name']);

    if (!in_array($mime, ALLOWED_UPLOAD_TYPES, true)) {
        security_abort(415, 'Type de fichier non autorisé : ' . $mime);
    }

    // Nom de fichier sécurisé (aucun contrôle client)
    $ext      = match($mime) {
        'image/jpeg'       => 'jpg',
        'image/png'        => 'png',
        'image/gif'        => 'gif',
        'image/webp'       => 'webp',
        'application/pdf'  => 'pdf',
        default            => 'bin',
    };
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest     = rtrim($upload_dir, '/') . '/' . $filename;

    // Vérification Path Traversal
    $realDir  = realpath($upload_dir);
    $realDest = realpath(dirname($dest));
    if ($realDir === false || $realDest === false || !str_starts_with($realDest . '/', $realDir . '/')) {
        security_abort(400, 'Chemin de destination invalide.');
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        security_abort(500, 'Impossible de déplacer le fichier uploadé.');
    }

    // Retire les droits d'exécution
    chmod($dest, 0644);

    return $filename;
}


// ============================================================
// 11. PROTECTION PATH TRAVERSAL
// ============================================================

function path_validate(string $path, string $base_dir): string
{
    $real_base = realpath($base_dir);
    $real_path = realpath($base_dir . DIRECTORY_SEPARATOR . $path);

    if ($real_base === false || $real_path === false) {
        security_abort(404, 'Fichier introuvable.');
    }

    if (!str_starts_with($real_path, $real_base . DIRECTORY_SEPARATOR)) {
        security_abort(403, 'Accès refusé.');
    }

    return $real_path;
}


// ============================================================
// 12. HACHAGE DE MOTS DE PASSE
// ============================================================

function password_hash_secure(string $password): string
{
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,  // 64 Mo
        'time_cost'   => 4,
        'threads'     => 2,
    ]);
}

function password_verify_secure(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function password_needs_rehash(string $hash): bool
{
    return password_needs_rehash($hash, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost'   => 4,
        'threads'     => 2,
    ]);
}


// ============================================================
// 13. REDIRECTION SÉCURISÉE (Open Redirect)
// ============================================================

function redirect_safe(string $url, array $allowed_hosts = []): never
{
    $parsed = parse_url($url);

    // Redirection relative : toujours OK
    if (!isset($parsed['host'])) {
        $safe = '/' . ltrim($url, '/');
        header('Location: ' . $safe, true, 302);
        exit;
    }

    // Redirection absolue : vérifier l'hôte
    $host = $parsed['host'];
    if (!empty($allowed_hosts) && !in_array($host, $allowed_hosts, true)) {
        security_abort(400, 'Redirection vers un hôte non autorisé.');
    }

    header('Location: ' . $url, true, 302);
    exit;
}


// ============================================================
// 14. GESTION DES ERREURS SÉCURISÉE
// ============================================================

function security_set_error_handlers(): void
{
    // En production, désactiver l'affichage des erreurs
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);

    set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        error_log("[ERROR {$errno}] {$errstr} in {$errfile}:{$errline}");
        return true;
    });

    set_exception_handler(function (\Throwable $e): void {
        error_log('[EXCEPTION] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        security_abort(500, 'Une erreur interne est survenue.');
    });

    register_shutdown_function(function (): void {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            error_log('[FATAL] ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
            if (!headers_sent()) {
                http_response_code(500);
            }
        }
    });
}

function security_abort(int $code, string $message): never
{
    http_response_code($code);
    // En prod : afficher une page d'erreur générique, pas le message interne
    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    exit;
}


// ============================================================
// 15. IP HELPER
// ============================================================

function get_client_ip(): string
{
    // Attention : X-Forwarded-For peut être forgé — ne faire confiance qu'à REMOTE_ADDR
    // Si vous êtes derrière un proxy de confiance, adaptez cette logique.
    return filter_var(
        $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) ?: ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}


// ============================================================
// 16. BOOTSTRAP — Appeler en tout début de chaque script
// ============================================================

function security_bootstrap(): void
{
    security_set_error_handlers();
    security_set_headers();
    security_start_session();

    // Rate limiting global par IP
    rate_limit_check(get_client_ip());

    // Désactiver l'accès direct aux fichiers includes
    if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
        security_abort(403, 'Accès direct interdit.');
    }
}


// ============================================================
// EXEMPLE D'UTILISATION
// ============================================================
/*

// --- En haut de chaque page ---
require_once 'security.php';
security_bootstrap();


// --- Formulaire HTML ---
<form method="POST" action="/login">
    <?= csrf_html_field() ?>
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Connexion</button>
</form>


// --- Traitement du formulaire de connexion ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    csrf_validate('POST');

    $email = xss_clean($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';     // Ne pas échapper les mots de passe avant vérification

    $v = (new Validator())
        ->email($email)
        ->length($pass, 'password', 8, 128);

    if (!$v->passes()) {
        security_abort(400, $v->firstError());
    }

    $ip = get_client_ip();
    brute_force_check($ip);

    $pdo  = db_connect('mysql:host=localhost;dbname=mydb;charset=utf8mb4', 'user', 'pass');
    $user = db_query($pdo, 'SELECT * FROM users WHERE email = ?', [$email])->fetch();

    if (!$user || !password_verify_secure($pass, $user['password_hash'])) {
        brute_force_register_failure($ip);
        security_abort(401, 'Identifiants invalides.');
    }

    brute_force_reset($ip);
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];

    redirect_safe('/dashboard', ['monsite.com']);
}


// --- Upload de fichier ---
if (isset($_FILES['avatar'])) {
    $filename = upload_validate($_FILES['avatar'], __DIR__ . '/uploads');
    echo 'Fichier enregistré : ' . xss_clean($filename);
}


// --- Affichage sécurisé ---
echo xss_clean($user['username']);

*/
