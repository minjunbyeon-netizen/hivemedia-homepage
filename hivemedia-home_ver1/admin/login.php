<?php

/**
 * Admin Login Page
 * HIVEMEDIA Portfolio System
 */
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = '아이디 또는 비밀번호가 올바르지 않습니다.';
        }
    } else {
        $error = '아이디와 비밀번호를 입력해주세요.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 로그인 - HIVEMEDIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter Tight', sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 4rem;
            width: 100%;
            max-width: 400px;
        }

        .login-box h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }

        .login-box h1 span {
            color: #94BDF7;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #94BDF7;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: #94BDF7;
            color: #000;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #fff;
        }

        .error {
            background: rgba(255, 100, 100, 0.1);
            border: 1px solid rgba(255, 100, 100, 0.3);
            color: #ff6464;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
        }

        .back-link:hover {
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h1><span>HIVE</span>MEDIA Admin</h1>

        <?php if ($error): ?>
            <div class="error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>아이디</label>
                <input type="text" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label>비밀번호</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-login">로그인</button>
        </form>

        <a href="../index.html" class="back-link">← 홈으로 돌아가기</a>
    </div>
</body>

</html>