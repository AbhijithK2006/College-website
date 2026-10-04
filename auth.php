<?php
// Apex Institute of Technology - Authentication Module (auth.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('USERS_DB_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'users.json');

/**
 * Initialize default users if users.json does not exist.
 */
function init_users_storage() {
    if (!file_exists(USERS_DB_FILE)) {
        $defaultUsers = [
            [
                'id' => 'AIT-2026-1001',
                'name' => 'Alex Johnson',
                'email' => 'alex.johnson@apex.edu',
                'username' => 'alex.johnson',
                'program' => 'B.Tech Computer Science & Engineering',
                'department' => 'Computer Science',
                'password_hash' => password_hash('apex123', PASSWORD_DEFAULT),
                'created_at' => '2026-01-15 10:30:00',
                'last_login' => null
            ]
        ];
        file_put_contents(USERS_DB_FILE, json_encode($defaultUsers, JSON_PRETTY_PRINT), LOCK_EX);
    }
}

/**
 * Load all registered users from storage.
 * @return array
 */
function get_all_users() {
    init_users_storage();
    $data = file_get_contents(USERS_DB_FILE);
    if ($data === false) {
        return [];
    }
    $users = json_decode($data, true);
    return is_array($users) ? $users : [];
}

/**
 * Save users array to storage.
 * @param array $users
 * @return bool
 */
function save_all_users($users) {
    $json = json_encode($users, JSON_PRETTY_PRINT);
    return file_put_contents(USERS_DB_FILE, $json, LOCK_EX) !== false;
}

/**
 * Find user by email or username (case-insensitive).
 * @param string $identifier
 * @return array|null
 */
function find_user($identifier) {
    $identifier = trim(strtolower($identifier));
    if ($identifier === '') {
        return null;
    }

    $users = get_all_users();
    foreach ($users as $user) {
        if (strtolower($user['email']) === $identifier || strtolower($user['username']) === $identifier) {
            return $user;
        }
    }
    return null;
}

/**
 * Register a new user account.
 * Only non-existing emails can be registered.
 * 
 * @param string $name
 * @param string $email
 * @param string $password
 * @param string $program
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function register_account($name, $email, $password, $program = 'B.Tech Computer Science') {
    $name = trim($name);
    $email = trim(strtolower($email));
    $program = trim($program);

    if ($name === '' || $email === '' || $password === '') {
        return ['success' => false, 'message' => 'Please fill in all mandatory fields.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please provide a valid email address.'];
    }

    if (strlen($password) < 4) {
        return ['success' => false, 'message' => 'Password must be at least 4 characters long.'];
    }

    $existing = find_user($email);
    if ($existing !== null) {
        return ['success' => false, 'message' => 'An account with this email (' . htmlspecialchars($email) . ') is already registered. Please sign in instead.'];
    }

    $users = get_all_users();

    // Generate unique student ID
    $studentSeq = 1000 + count($users) + 1;
    $studentId = 'AIT-2026-' . $studentSeq;

    // Generate username from email or name
    $emailParts = explode('@', $email);
    $username = $emailParts[0];

    $newUser = [
        'id' => $studentId,
        'name' => $name,
        'email' => $email,
        'username' => $username,
        'program' => !empty($program) ? $program : 'B.Tech Computer Science',
        'department' => 'School of Computing & Technology',
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s'),
        'last_login' => null
    ];

    $users[] = $newUser;
    if (save_all_users($users)) {
        return ['success' => true, 'message' => 'Registration successful! You can now sign in with your credentials.', 'user' => $newUser];
    }

    return ['success' => false, 'message' => 'Server error: unable to save registration data. Please try again.'];
}

/**
 * Authenticate login. ONLY registered accounts can login.
 * 
 * @param string $identifier (email or username)
 * @param string $password
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function authenticate_student($identifier, $password) {
    $identifier = trim($identifier);
    if ($identifier === '' || $password === '') {
        return ['success' => false, 'message' => 'Please enter both your email/username and password.'];
    }

    $user = find_user($identifier);

    // CRITICAL REQUIREMENT: In login only registered account can login
    if ($user === null) {
        return [
            'success' => false,
            'message' => 'Account not found! This account is not registered. Please create a new account using the registration form first.'
        ];
    }

    // Verify hashed password
    if (!password_verify($password, $user['password_hash'])) {
        return [
            'success' => false,
            'message' => 'Incorrect password entered for account (' . htmlspecialchars($user['email']) . '). Please try again.'
        ];
    }

    // Update last login
    $users = get_all_users();
    foreach ($users as &$u) {
        if ($u['id'] === $user['id']) {
            $u['last_login'] = date('Y-m-d H:i:s');
            break;
        }
    }
    save_all_users($users);

    // Establish authenticated session
    if (!headers_sent()) {
        session_regenerate_id(true);
    }
    $_SESSION['student_user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'username' => $user['username'],
        'program' => $user['program'],
        'department' => $user['department'] ?? 'School of Computing & Technology',
        'created_at' => $user['created_at'],
        'logged_in_at' => date('Y-m-d H:i:s')
    ];

    return ['success' => true, 'message' => 'Login successful!', 'user' => $user];
}

/**
 * Check if a student is currently logged in.
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['student_user']) && is_array($_SESSION['student_user']);
}

/**
 * Retrieve current logged in student session.
 * @return array|null
 */
function current_student() {
    return is_logged_in() ? $_SESSION['student_user'] : null;
}

/**
 * Require login for protected pages (e.g. dashboard.php).
 */
function require_student_login() {
    if (!is_logged_in()) {
        header('Location: login.html?msg=unauthorized');
        exit();
    }
}
