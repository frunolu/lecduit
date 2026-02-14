<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';

$user = new User();
$success = '';
$error = '';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $t['invalid_credentials'];
    }
    else {
        $result = $user->requestPasswordReset($email);
        if ($result['success']) {
            $success = $t['reset_email_sent'];
        // In production, send email here with reset link
        // For now, we'll just show success message
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $t['forgot_password']; ?> - Lecduit
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

        <!-- Forgot Password Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <h2 class="text-2xl font-black text-gray-800 mb-2 text-center">
                <?php echo $t['forgot_password']; ?>
            </h2>
            <p class="text-sm text-gray-600 mb-6 text-center">
                <?php echo $t['reset_password']; ?>
            </p>

            <!-- Success Message -->
            <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl">
                <p class="text-sm font-bold text-green-700 text-center">
                    <i class="fa fa-check-circle mr-2"></i>
                    <?php echo h($success); ?>
                </p>
                <div class="mt-4 text-center">
                    <a href="login.php" class="text-sm font-bold text-purple-600 hover:text-purple-700">
                        <?php echo $t['back_to_login']; ?> →
                    </a>
                </div>
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

            <?php if (!$success): ?>
            <!-- Reset Request Form -->
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <?php echo $t['email']; ?>
                    </label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="your@email.com">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-black text-sm uppercase hover:shadow-lg transition">
                    <?php echo $t['send_reset_link']; ?>
                </button>
            </form>
            <?php
endif; ?>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="login.php" class="text-sm font-bold text-purple-600 hover:text-purple-700">
                    <i class="fa fa-arrow-left mr-2"></i>
                    <?php echo $t['back_to_login']; ?>
                </a>
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
    </div>
</body>

</html>