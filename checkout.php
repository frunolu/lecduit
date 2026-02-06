<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/User.php'; // Nové

$cart = new Cart();
$user = new User(); // Nové
$currentUser = $user->getCurrentUser(); // Nové
$items = $cart->getItems();

// 1. Logika pro odstranění položky
if (isset($_GET['remove'])) {
    $cart->remove($_GET['remove']);
    header("Location: checkout.php");
    exit;
}

// 2. Zpracování odeslání objednávky
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($items)) {
    try {
        $pdo->beginTransaction();

        // Generování čísla objednávky (např. 2026 + náhodné číslo)
        $orderNumber = date('Y') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Výpočet DPH podle trhu
        $taxRate = ($market_id === 'cz') ? 21.00 : (($market_id === 'pl') ? 23.00 : 20.00);

        // Uložení hlavní objednávky
        // Poznámka: Zde bychom ideálně ukládali i ID uživatele, pokud existuje, ale prozatím to necháme takto, aby to sedělo se strukturou DB
        $sqlOrder = "INSERT INTO orders (market_id, order_number, email, first_name, last_name, phone, billing_street, billing_city, billing_zip, billing_country, total_amount, currency, tax_rate) 
                     VALUES (:mid, :onum, :email, :fname, :lname, :phone, :street, :city, :zip, :bcountry, :total, :curr, :tax)";

        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([
                ':mid'      => $market_id,
                ':onum'     => $orderNumber,
                ':email'    => $_POST['email'],
                ':fname'    => $_POST['first_name'],
                ':lname'    => $_POST['last_name'],
                ':phone'    => $_POST['phone'],
                ':street'   => $_POST['street'],
                ':city'     => $_POST['city'],
                ':zip'      => $_POST['zip'],
                ':bcountry' => $_POST['billing_country'],
                ':total'    => $cart->getTotal(),
                ':curr'     => $currency,
                ':tax'      => $taxRate
        ]);

        $orderId = $pdo->lastInsertId();

        // Uložení položek objednávky (Order Items)
        $sqlItem = "INSERT INTO order_items (order_id, experience_id, title_snapshot, price_unit, qty) 
                    VALUES (:oid, :eid, :title, :price, :qty)";
        $stmtItem = $pdo->prepare($sqlItem);

        foreach ($items as $item) {
            $stmtItem->execute([
                    ':oid'   => $orderId,
                    ':eid'   => $item['id'],
                    ':title' => $item['title'],
                    ':price' => $item['price'],
                    ':qty'   => $item['qty']
            ]);
        }

        $pdo->commit();
        $cart->clear();
        $success = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Chyba při ukládání objednávky: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['checkout']; ?> | Lecduit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { 'lec-teal': '#58b8bc', 'lec-orange': '#e86e2d', 'lec-dark': '#0f172a' }, fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }</script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">

<header class="bg-white border-b sticky top-0 z-40">
    <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-2">
            <img src="lecduit-logo.jpg" class="h-8 md:h-10">
            <span class="font-extrabold text-xl uppercase">Lecduit<span class="text-lec-teal">.<?php echo $market_id; ?></span></span>
        </a>
        <div class="flex items-center gap-4">
            <?php if ($currentUser): ?>
                <div class="flex items-center gap-2 ml-2 pl-4 border-l border-slate-100">
                    <?php if(!empty($currentUser['avatar'])): ?>
                        <img src="<?php echo h($currentUser['avatar']); ?>" class="w-8 h-8 rounded-full border border-slate-200">
                    <?php else: ?>
                        <div class="w-8 h-8 rounded-full bg-lec-teal text-white flex items-center justify-center font-bold">
                            <?php echo substr($currentUser['first_name'], 0, 1); ?>
                        </div>
                    <?php endif; ?>
                    <span class="hidden md:inline text-xs font-bold"><?php echo h($currentUser['first_name']); ?></span>
                    <a href="logout.php" class="text-xs text-slate-400 hover:text-red-500 ml-1" title="Odhlásit"><i class="fa fa-sign-out"></i></a>
                </div>
            <?php else: ?>
                <a href="login_google.php" class="text-xs font-bold uppercase text-slate-500 hover:text-lec-teal flex items-center gap-1">
                    <i class="fa-brands fa-google"></i> <span class="hidden sm:inline">Login</span>
                </a>
            <?php endif; ?>

            <a href="index.php" class="text-xs font-bold uppercase text-slate-400 hover:text-lec-teal transition ml-4">← <?php echo $t['back']; ?></a>
        </div>
    </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-12">

    <?php if ($success): ?>
        <div class="bg-white p-12 rounded-[3rem] shadow-xl text-center border border-green-100">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl animate-bounce"><i class="fa fa-check"></i></div>
            <h1 class="text-3xl font-black mb-4">Děkujeme za objednávku!</h1>
            <p class="text-slate-500 mb-8">Vouchery byly odeslány na váš e-mail.</p>
            <a href="index.php" class="inline-block bg-lec-dark text-white px-8 py-4 rounded-2xl font-bold uppercase text-sm">Zpět do katalogu</a>
        </div>
    <?php elseif (empty($items)): ?>
        <div class="text-center py-20">
            <i class="fa fa-shopping-basket text-slate-200 text-6xl mb-6"></i>
            <h1 class="text-2xl font-bold text-slate-400 uppercase italic"><?php echo $t['empty_cart']; ?></h1>
            <a href="index.php" class="inline-block mt-6 text-lec-teal font-bold border-b-2 border-lec-teal pb-1"><?php echo $t['katalog']; ?></a>
        </div>
    <?php else: ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <div>
                <h2 class="text-xl font-black mb-8 uppercase italic tracking-tighter"><?php echo $t['your_cart']; ?></h2>
                <div class="space-y-4">
                    <?php foreach ($items as $id => $item): ?>
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex justify-between items-center group">
                            <div>
                                <h3 class="font-bold text-slate-800"><?php echo h($item['title']); ?></h3>
                                <span class="text-sm font-black text-lec-teal"><?php echo formatPrice($item['price']); ?></span>
                                <?php if($item['qty'] > 1): ?> <span class="text-xs text-slate-400 ml-2">x <?php echo $item['qty']; ?></span> <?php endif; ?>
                            </div>
                            <a href="?remove=<?php echo $id; ?>" class="text-slate-300 hover:text-red-500 transition px-2"><i class="fa fa-trash-can"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 p-6 bg-lec-dark text-white rounded-3xl shadow-xl flex justify-between items-center">
                    <span class="font-bold text-sm uppercase opacity-60"><?php echo $t['total']; ?></span>
                    <span class="text-2xl font-black"><?php echo formatPrice($cart->getTotal()); ?></span>
                </div>
            </div>

            <div class="bg-white p-8 md:p-10 rounded-[3rem] shadow-2xl border border-slate-100">
                <form action="checkout.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-400 ml-2"><?php echo $t['f_name']; ?></label>
                            <input type="text" name="first_name" value="<?php echo h($currentUser['first_name'] ?? ''); ?>" required class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 font-bold focus:ring-2 focus:ring-lec-teal outline-none transition">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-400 ml-2"><?php echo $t['l_name']; ?></label>
                            <input type="text" name="last_name" value="<?php echo h($currentUser['last_name'] ?? ''); ?>" required class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 font-bold focus:ring-2 focus:ring-lec-teal outline-none transition">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-slate-400 ml-2"><?php echo $t['email']; ?></label>
                        <input type="email" name="email" value="<?php echo h($currentUser['email'] ?? ''); ?>" required class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 font-bold focus:ring-2 focus:ring-lec-teal outline-none transition">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-slate-400 ml-2"><?php echo $t['phone']; ?></label>
                        <input type="text" name="phone" placeholder="+421 ..." class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 font-bold focus:ring-2 focus:ring-lec-teal outline-none transition">
                    </div>

                    <div class="pt-4 border-t border-slate-50">
                        <label class="text-[10px] font-bold uppercase text-slate-400 ml-2"><?php echo $t['street']; ?></label>
                        <input type="text" name="street" class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 font-bold mb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="city" placeholder="<?php echo $t['city']; ?>" class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 font-bold">
                            <input type="text" name="zip" placeholder="<?php echo $t['zip']; ?>" class="w-full bg-slate-50 border-0 rounded-2xl px-4 py-3 font-bold">
                        </div>
                        <input type="hidden" name="billing_country" value="<?php echo strtoupper($market_id); ?>">
                    </div>

                    <button type="submit" class="w-full bg-lec-orange text-white py-6 rounded-3xl font-black text-lg shadow-xl shadow-lec-orange/20 hover:scale-[1.02] active:scale-95 transition-all uppercase">
                        <?php echo $t['finish_order']; ?>
                    </button>
                </form>
            </div>
        </div>

    <?php endif; ?>
</main>

</body>
</html>