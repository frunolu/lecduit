<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';

$user = new User();
$success = '';
$error = '';

// Handle email verification
if (isset($_GET['token'])) {
    $result = $user->verifyEmail($_GET['token']);
    if ($result['success']) {
        header("Location: login.php?verified=1");
        exit;
    }
    else {
        $error = $t['invalid_token'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $t['email_verified']; ?> - Lecduit
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
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <?php if ($error): ?>
            <div class="mb-6">
                <i class="fa fa-times-circle text-6xl text-red-500"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-800 mb-4">
                <?php echo $t['invalid_token']; ?>
            </h2>
            <p class="text-gray-600 mb-6">
                <?php echo h($error); ?>
            </p>
            <?php
else: ?>
            <div class="mb-6">
                <i class="fa fa-spinner fa-spin text-6xl text-purple-600"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-800 mb-4">Verifying...</h2>
            <?php
endif; ?>

            <a href="login.php"
                class="inline-block px-6 py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition">
                <?php echo $t['back_to_login']; ?>
            </a>
        </div>
    </div>
</body>

</html>