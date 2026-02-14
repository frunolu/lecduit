<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';

$user = new User();
$error = '';
$success = '';

// Check if already logged in
if ($user->isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = $t['invalid_credentials'];
    }
    else {
        $result = $user->login($email, $password);
        if ($result['success']) {
            header("Location: index.php");
            exit;
        }
        else {
            $error = $t[$result['error']] ?? $t['invalid_credentials'];
        }
    }
}

// Check for success messages from other pages
if (isset($_GET['verified'])) {
    $success = $t['email_verified'];
}
if (isset($_GET['reset'])) {
    $success = $t['password_reset_success'];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $t['login']; ?> - Lecduit
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="index.php" class="inline-block">
                <h1 class="text-4xl font-black text-white">LECDUIT</h1>
            </a>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <h2 class="text-2xl font-black text-gray-800 mb-6 text-center">
                <?php echo $t['login']; ?>
            </h2>

            <!-- Success Message -->
            <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl">
                <p class="text-sm font-bold text-green-700 text-center">
                    <i class="fa fa-check-circle mr-2"></i>
                    <?php echo h($success); ?>
                </p>
            </div>
            <?php
endif; ?>

            <!-- Error Message -->
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <p class="text-sm font-bold text-red-700 text-center">
                    <i class="fa fa-exclamation-circle mr-2"></i>
                    <?php echo h($error); ?>
                </p>
            </div>
            <?php
endif; ?>

            <!-- Email/Password Login Form -->
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <?php echo $t['email']; ?>
                    </label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="your@email.com">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <?php echo $t['password']; ?>
                    </label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="••••••••">
                </div>

                <div class="text-right">
                    <a href="forgot_password.php" class="text-sm font-bold text-purple-600 hover:text-purple-700">
                        <?php echo $t['forgot_password']; ?>
                    </a>
                </div>

                <button type="submit" name="login"
                    class="w-full py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-black text-sm uppercase hover:shadow-lg transition">
                    <?php echo $t['login']; ?>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500 font-bold">
                        <?php echo $t['or']; ?>
                    </span>
                </div>
            </div>

            <!-- Google Login Button -->
            <a href="login_google.php"
                class="flex items-center justify-center w-full py-3 bg-white border-2 border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                    <path fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                <?php echo $t['login_with_google']; ?>
            </a>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    <?php echo $t['no_account']; ?>
                    <a href="register.php" class="font-bold text-purple-600 hover:text-purple-700">
                        <?php echo $t['register']; ?>
                    </a>
                </p>
            </div>

            <!-- Language Switcher -->
            <div class="mt-6 flex justify-center gap-2">
                <?php foreach (['sk', 'cz', 'pl', 'en', 'de'] as $l): ?>
                <a href="?lang=<?php echo $l; ?>"
                    class="px-3 py-1 text-xs font-bold rounded-lg <?php echo $lang === $l ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                    <?php echo strtoupper($l); ?>
                </a>
                <?php
endforeach; ?>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="index.php" class="text-white text-sm font-bold hover:underline">
                <i class="fa fa-arrow-left mr-2"></i>
                <?php echo $t['back']; ?>
            </a>
        </div>
    </div>
</body>

</html>