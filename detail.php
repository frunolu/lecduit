<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ExperienceRepository.php';
require_once __DIR__ . '/Cart.php';

$cart = new Cart();
$repo = new ExperienceRepository($lang, $price_col);

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) { header("Location: index.php"); exit; }

// Načtení detailu zážitku
$sql = "SELECT p.*, c.name$suffix as cat_name, s.name$suffix as sub_name 
        FROM experiences p 
        JOIN subcategories s ON p.subcategory_id = s.id 
        JOIN categories c ON s.category_id = c.id 
        WHERE p.id = :id AND p.is_active = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$p = $stmt->fetch();

if (!$p) die("Error: Zážitek nenalezen.");

// NOVÉ: Načtení tagů (vlastností) pro tento zážitek
$productTags = $repo->getTagsForExperience($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $cart->add($p['id'], $p['title'.$suffix], $p[$price_col]);
    header("Location: detail.php?id=$id&added=1"); exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($p['title'.$suffix]); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { 'lec-orange': '#e86e2d', 'lec-teal': '#58b8bc', 'lec-dark': '#0f172a' }, fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }</script>
</head>
<body class="bg-white text-slate-900">

<nav class="h-20 border-b flex items-center sticky top-0 bg-white/90 backdrop-blur z-50">
    <div class="max-w-7xl mx-auto px-4 w-full flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-2">
            <img src="lecduit-logo.jpg" class="h-8 md:h-10"><span class="font-extrabold text-xl uppercase">Lecduit<span class="text-lec-teal">.<?php echo $market_id; ?></span></span>
        </a>
        <div class="flex items-center gap-4">
            <div class="hidden md:flex gap-2 text-[10px] font-bold uppercase">
                <?php foreach(['sk', 'cz', 'pl', 'en', 'de'] as $l): ?>
                    <a href="?id=<?php echo $id; ?>&lang=<?php echo $l; ?>" class="<?php echo $lang == $l ? 'text-lec-teal' : 'text-slate-300'; ?> transition"><?php echo $l; ?></a>
                <?php endforeach; ?>
            </div>
            <a href="index.php" class="text-xs font-extrabold uppercase text-slate-400">← <?php echo $t['back']; ?></a>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 py-12">
    <?php if(isset($_GET['added'])): ?>
        <div id="notif" class="max-w-3xl mx-auto bg-green-500 text-white p-4 rounded-2xl mb-8 flex items-center justify-between shadow-lg transition-all duration-500">
            <span class="font-bold text-sm"><i class="fa fa-check-circle mr-2"></i> <?php echo $t['added']; ?></span>
            <a href="checkout.php" class="text-[10px] bg-white text-green-600 px-4 py-2 rounded-xl font-black uppercase"><?php echo $t['view_cart']; ?></a>
        </div>
        <script>setTimeout(() => { document.getElementById('notif').style.opacity = '0'; setTimeout(() => document.getElementById('notif').remove(), 500); }, 5000);</script>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
        <div class="space-y-12">
            <img src="<?php echo h($p['image_url']); ?>" class="w-full rounded-[3rem] shadow-2xl">
            <div>
                <h2 class="text-2xl font-black mb-6 italic border-l-4 border-lec-teal pl-4 uppercase"><?php echo $t['desc']; ?></h2>
                <p class="text-slate-500 leading-relaxed text-lg"><?php echo nl2br(h($p['desc'.$suffix])); ?></p>
            </div>
        </div>
        <div class="lg:sticky lg:top-32 bg-slate-50 p-10 rounded-[3rem] border border-slate-100 shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest"><?php echo h($p['cat_name']); ?></span>
                <i class="fa fa-chevron-right text-[10px] text-slate-200"></i>
                <span class="text-lec-teal text-[10px] font-black uppercase tracking-widest"><?php echo h($p['sub_name']); ?></span>
            </div>
            <h1 class="text-4xl font-black mb-8 leading-tight"><?php echo h($p['title'.$suffix]); ?></h1>

            <div class="space-y-4 mb-10 border-t py-8 border-slate-200">
                <div class="flex items-center gap-4 text-slate-600 font-bold text-sm"><i class="fa fa-check text-green-500 w-5 text-center"></i> <?php echo $t['validity']; ?></div>
                <div class="flex items-center gap-4 text-slate-600 font-bold text-sm"><i class="fa fa-envelope text-green-500 w-5 text-center"></i> <?php echo $t['delivery']; ?></div>
                <div class="flex items-center gap-4 text-slate-600 font-bold text-sm"><i class="fa fa-clock text-green-500 w-5 text-center"></i> <?php echo $p['duration_minutes']; ?> min.</div>

                <?php foreach($productTags as $tag): ?>
                    <div class="flex items-center gap-4 text-slate-600 font-bold text-sm">
                        <i class="fa <?php echo h($tag['icon']); ?> text-lec-teal w-5 text-center"></i>
                        <?php echo h($tag['name'.$suffix]); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mb-10">
                <span class="text-[10px] font-black text-slate-400 uppercase italic block mb-1"><?php echo $t['price_for']; ?></span>
                <div class="text-5xl font-black text-lec-dark italic"><?php echo formatPrice($p[$price_col]); ?></div>
            </div>
            <form method="POST">
                <button type="submit" name="add_to_cart" class="w-full bg-lec-orange text-white py-6 rounded-3xl font-black text-xl shadow-xl hover:scale-[1.02] transition-all">
                    <i class="fa fa-shopping-basket mr-2"></i> <?php echo $t['buy']; ?>
                </button>
            </form>
        </div>
    </div>
</main>
</body>
</html>