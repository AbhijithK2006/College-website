<?php
require_once __DIR__ . '/auth.php';

// Detect JSON / AJAX request
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
    || isset($_POST['ajax'])
    || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Support JSON raw body or standard form POST
    $inputData = $_POST;
    if (empty($inputData)) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $inputData = $json;
            $isAjax = true;
        }
    }

    $action = $inputData['action'] ?? '';
    if (empty($action)) {
        if (isset($inputData['name']) || isset($inputData['confirm-password'])) {
            $action = 'register';
        } else {
            $action = 'login';
        }
    }

    if ($action === 'login') {
        $username = trim($inputData['username'] ?? '');
        $password = $inputData['password'] ?? '';

        if ($username === '' || $password === '') {
            $response = [
                'success' => false,
                'code' => 'missing_fields',
                'message' => 'Please enter both your email/username and password.'
            ];
        } else {
            $user = find_user($username);
            if ($user === null) {
                $response = [
                    'success' => false,
                    'code' => 'not_registered',
                    'message' => 'Account not found! This account is not registered. Please register first using the registration form.'
                ];
            } elseif (!password_verify($password, $user['password_hash'])) {
                $response = [
                    'success' => false,
                    'code' => 'wrong_password',
                    'message' => 'Incorrect password! If you do not have an account yet, please register first.'
                ];
            } else {
                $authResult = authenticate_student($username, $password);
                $response = [
                    'success' => true,
                    'code' => 'login_success',
                    'message' => 'Login successful! Welcome back, ' . $user['name'] . '.',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'program' => $user['program'],
                        'department' => $user['department'] ?? 'School of Computing & Technology'
                    ]
                ];
            }
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }

        // Standard non-ajax fallback
        if ($response['success']) {
            header("Location: login.html?login_success=1");
        } else {
            header("Location: login.html?error=" . $response['code'] . "&username=" . urlencode($username));
        }
        exit();

    } elseif ($action === 'register') {
        $name     = trim($inputData['name'] ?? '');
        $email    = trim($inputData['email'] ?? '');
        $program  = trim($inputData['program'] ?? 'B.Tech Computer Science');
        $password = $inputData['password'] ?? '';
        $confirm  = $inputData['confirm-password'] ?? '';

        if ($password !== $confirm) {
            $response = [
                'success' => false,
                'code' => 'password_mismatch',
                'message' => 'Passwords do not match. Please verify and try again.'
            ];
        } else {
            $regResult = register_account($name, $email, $password, $program);
            if ($regResult['success']) {
                $response = [
                    'success' => true,
                    'code' => 'registered',
                    'message' => 'Registration successful! Your account has been registered. You can now log in.',
                    'email' => $email
                ];
            } else {
                $code = str_contains($regResult['message'], 'already registered') ? 'already_registered' : 'registration_failed';
                $response = [
                    'success' => false,
                    'code' => $code,
                    'message' => $regResult['message']
                ];
            }
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }

        if ($response['success']) {
            header("Location: login.html?msg=registered&email=" . urlencode($email));
        } else {
            header("Location: login.html?error=" . $response['code']);
        }
        exit();

    } elseif ($action === 'logout') {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        session_destroy();
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
            exit();
        }
        header("Location: login.html?msg=logged_out");
        exit();

    } elseif ($action === 'check_session') {
        header('Content-Type: application/json; charset=utf-8');
        if (is_logged_in()) {
            echo json_encode(['logged_in' => true, 'user' => current_student()]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        exit();
    }
}

// If direct GET to login.php, redirect straight to login.html
header("Location: login.html");
exit();
