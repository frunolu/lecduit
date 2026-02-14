<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';

$user = new User();
$error = '';
$token = $_GET['token'] ?? '';

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $resetToken = $_POST['token'] ?? '';

    if (empty($newPassword) || empty($resetToken)) {
        $error = $t['invalid_credentials'];
    }
    elseif (strlen($newPassword) < 8) {
        $error = $t['password_min'];
    }
    elseif ($newPassword !== $passwordConfirm) {
        $error = $t['passwords_not_match'];
    }
    else {
        $result = $user->resetPassword($resetToken, $newPassword);
        if ($result['success']) {
            header("Location: login.php?reset=1");
            exit;
        }
        else {
            $error = $t['invalid_token'];
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
        <?php echo $t['reset_password']; ?> - Lecduit
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

        <!-- Reset Password Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <h2 class="text-2xl font-black text-gray-800 mb-6 text-center">
                <?php echo $t['reset_password']; ?>
            </h2>

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

            <!-- Reset Password Form -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="token" value="<?php echo h($token); ?>">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <?php echo $t['new_password']; ?>
                    </label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="••••••••">
                    <p class="mt-1 text-xs text-gray-500">
                        <?php echo $t['password_min']; ?>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <?php echo $t['password_confirm']; ?>
                    </label>
                    <input type="password" name="password_confirm" required minlength="8"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-black text-sm uppercase hover:shadow-lg transition">
                    <?php echo $t['reset_password']; ?>
                </button>
            </form>

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