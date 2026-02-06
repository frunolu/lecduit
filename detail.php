<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ExperienceRepository.php';
require_once __DIR__ . '/Cart.php';

$cart = new Cart();
$repo = new ExperienceRepository($lang, $price_col);

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) { header("Location: index.php"); exit; }

// Načítání detailu zážitku
$sql = "SELECT p.*, c.name$suffix as cat_name, s.name$suffix as sub_name 
        FROM experiences p 
        JOIN subcategories s ON p.subcategory_id = s.id 
        JOIN categories c ON s.category_id = c.id 
        WHERE p.id = :id AND p.is_active = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$p = $stmt->fetch();

if (!$p) die("Error: Zážitek nenalezen.");

$productTags = $repo->getTagsForExperience($id);

// Zpracování přidání do košíku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $cart->add($p['id'], $p['title'.$suffix], $p[$price_col]);
    header("Location: detail.php?id=$id&added=1"); exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($p['title'.$suffix]); ?> | Lecduit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'lec-teal': '#58b8bc', 'lec-orange': '#e86e2d', 'lec-dark': '#0f172a' },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">

<header class="bg-white/90 backdrop-blur border-b sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 h-16 md:h-20 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-2">
            <img src="lecduit-logo.png" class="h-8 md:h-10">
            <span class="font-extrabold text-xl uppercase">Lecduit<span class="text-lec-teal">.<?php echo $market_id; ?></span></span>
        </a>
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-xs font-bold uppercase text-slate-400 hover:text-lec-teal transition">
                <i class="fa fa-arrow-left mr-1"></i> <?php echo $t['back']; ?>
            </a>
            <a href="checkout.php" class="relative p-2 bg-slate-100 rounded-2xl group transition">
                <i class="fa fa-shopping-basket text-slate-400 group-hover:text-lec-teal"></i>
                <?php if($cart->getCount() > 0): ?>
                    <span class="absolute -top-1 -right-1 bg-lec-orange text-white text-[9px] font-black w-5 h-5 flex items-center justify-center rounded-full"><?php echo $cart->getCount(); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-8">

    <?php if (isset($_GET['added'])): ?>
        <div class="mb-6 p-4 bg-green-500 text-white rounded-2xl font-bold text-center animate-bounce shadow-lg shadow-green-200">
            <i class="fa fa-check-circle mr-2"></i> <?php echo $t['added']; ?>
            <a href="checkout.php" class="underline ml-4 uppercase text-xs"><?php echo $t['view_cart']; ?> →</a>
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row gap-10">
        <div class="w-full md:w-1/2">
            <div class="sticky top-28">
                <div class="aspect-[4/3] rounded-[2.5rem] overflow-hidden shadow-2xl">
                    <img src="<?php echo h($p['image_url']); ?>" class="w-full h-full object-cover" alt="Experience">
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <?php foreach ($productTags as $tag): ?>
                        <span class="bg-white border border-slate-100 px-4 py-2 rounded-full text-[10px] font-bold uppercase text-slate-500 shadow-sm">
                            <i class="fa <?php echo h($tag['icon']); ?> text-lec-teal mr-2"></i> <?php echo h($tag['name'.$suffix]); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex flex-col">
            <nav class="flex items-center gap-2 text-[10px] font-bold uppercase text-slate-400 mb-4">
                <span><?php echo h($p['cat_name']); ?></span>
                <i class="fa fa-chevron-right text-[8px]"></i>
                <span class="text-lec-teal"><?php echo h($p['sub_name']); ?></span>
            </nav>

            <h1 class="text-3xl md:text-4xl font-black leading-tight mb-6">
                <?php echo h($p['title'.$suffix]); ?>
            </h1>

            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white p-4 rounded-2xl border border-slate-100 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 bg-lec-teal/10 rounded-xl flex items-center justify-center text-lec-teal">
                        <i class="fa fa-calendar-check text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Dostupnost</p>
                        <p class="text-xs font-black"><?php echo $t['validity']; ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 bg-lec-orange/10 rounded-xl flex items-center justify-center text-lec-orange">
                        <i class="fa fa-bolt text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Doručení</p>
                        <p class="text-xs font-black"><?php echo $t['delivery']; ?></p>
                    </div>
                </div>
            </div>

            <div class="prose prose-slate mb-10">
                <h3 class="text-[10px] font-bold uppercase text-slate-400 mb-3 tracking-widest"><?php echo $t['desc']; ?></h3>
                <p class="text-slate-600 leading-relaxed font-medium">
                    <?php echo nl2br(h($p['description'.$suffix] ?? $p['desc'.$suffix])); ?>
                </p>
            </div>

            <div class="mt-auto bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-lec-teal/5 rounded-full -mr-16 -mt-16"></div>

                <div class="mb-6 relative z-10 border-b border-slate-50 pb-6">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1"><?php echo $t['price_for']; ?></span>
                    <span class="text-4xl font-black text-lec-dark"><?php echo formatPrice($p[$price_col]); ?></span>
                </div>

                <?php if ($p['can_buy_voucher']): ?>
                    <div class="relative z-10">
                        <form method="POST" class="w-full">
                            <button type="submit" name="add_to_cart" class="w-full py-5 bg-lec-orange text-white rounded-2xl font-black text-sm uppercase hover:scale-105 active:scale-95 transition-all shadow-lg shadow-lec-orange/20">
                                <i class="fa fa-shopping-cart mr-2"></i> <?php echo $t['buy']; ?>
                            </button>
                        </form>
                        <p class="mt-4 text-[10px] font-bold text-slate-400 text-center uppercase tracking-widest">
                            <i class="fa fa-lock mr-1"></i> <?php echo $t['secure']; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4 relative z-10">
                        <h4 class="font-black text-lg uppercase italic"><?php echo $t['contact']; ?></h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php if ($p['contact_phone']): ?>
                                <a href="tel:<?php echo h($p['contact_phone']); ?>" class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-lec-teal transition group">
                                    <i class="fa fa-phone text-lec-teal"></i>
                                    <span class="text-xs font-bold text-slate-600"><?php echo h($p['contact_phone']); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php if ($p['contact_email']): ?>
                                <a href="mailto:<?php echo h($p['contact_email']); ?>" class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-lec-teal transition group">
                                    <i class="fa fa-envelope text-lec-teal"></i>
                                    <span class="text-xs font-bold text-slate-600 truncate"><?php echo h($p['contact_email']); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if ($p['contact_website']): ?>
                            <a href="<?php echo h($p['contact_website']); ?>" target="_blank" class="block w-full text-center bg-lec-dark text-white py-5 rounded-2xl font-bold text-xs uppercase hover:bg-lec-teal transition shadow-lg">
                                <i class="fa fa-external-link mr-2"></i> <?php echo $t['visit_web']; ?>
                            </a>
                        <?php endif; ?>
                        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-start gap-3">
                            <i class="fa fa-info-circle text-blue-500 mt-1"></i>
                            <p class="text-[10px] font-bold text-blue-700 leading-tight uppercase">
                                <?php echo ($lang == 'sk' ? 'Priamy nákup nie je k dispozícii. Kontaktujte poskytovateľa.' : 'Přímý nákup není k dispozici. Kontaktujte poskytovatele.'); ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<footer class="mt-20 py-10 border-t bg-white">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-[10px] font-bold uppercase text-slate-300 tracking-widest">© 2026 Lecduit. All rights reserved.</p>
    </div>
</footer>

</body>
</html>