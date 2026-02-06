<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ExperienceRepository.php';
require_once __DIR__ . '/Cart.php';

$cart = new Cart();
$repo = new ExperienceRepository($lang, $price_col);
$allTags = $repo->getAllTags();

$filters = [
    'cat' => $_GET['cat'] ?? [],
    'countries' => $_GET['countries'] ?? [],
    'min_price' => $_GET['min_price'] ?? null,
    'max_price' => $_GET['max_price'] ?? null,
    'lat' => $_GET['lat'] ?? null,
    'lng' => $_GET['lng'] ?? null,
    'radius' => $_GET['radius'] ?? 50,
];

$products = $repo->search($filters);
$categories = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecduit.<?php echo $market_id; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { 'lec-teal': '#58b8bc', 'lec-orange': '#e86e2d', 'lec-dark': '#0f172a' }, fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }</script>
</head>
<body class="bg-slate-50 text-slate-900">

    <header class="bg-white/90 backdrop-blur border-b sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 h-16 md:h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2">
                <img src="lecduit-logo.jpg" class="h-8 md:h-10"><span class="font-extrabold text-xl uppercase">Lecduit<span class="text-lec-teal">.<?php echo $market_id; ?></span></span>
            </a>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex gap-3 text-[10px] font-bold uppercase">
                    <?php foreach(['sk', 'cz', 'pl', 'en', 'de'] as $l): ?>
                        <a href="?lang=<?php echo $l; ?>" class="<?php echo $lang == $l ? 'text-lec-teal border-b-2 border-lec-teal' : 'text-slate-300'; ?> transition"><?php echo $l; ?></a>
                    <?php endforeach; ?>
                </div>
                <a href="checkout.php" class="relative p-2 bg-slate-100 rounded-full group transition">
                    <i class="fa fa-shopping-basket text-slate-400 group-hover:text-lec-teal"></i>
                    <?php if($cart->getCount() > 0): ?><span class="absolute -top-1 -right-1 bg-lec-orange text-white text-[9px] font-black w-5 h-5 flex items-center justify-center rounded-full"><?php echo $cart->getCount(); ?></span><?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row gap-8">
        <aside class="w-full md:w-64">
            <form action="index.php" method="GET" id="filterForm" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm sticky top-24">
                <input type="hidden" name="lat" id="lat-input" value="<?php echo h($filters['lat']); ?>"><input type="hidden" name="lng" id="lng-input" value="<?php echo h($filters['lng']); ?>">
                <div class="mb-6">
                    <label class="text-[10px] font-bold uppercase text-slate-400 mb-3 block"><?php echo $t['location']; ?></label>
                    <button type="button" id="locate-btn" class="w-full mb-4 py-3 rounded-xl border-2 border-dashed <?php echo $filters['lat'] ? 'border-green-500 text-green-600 bg-green-50' : 'border-lec-teal text-lec-teal'; ?> font-bold text-xs uppercase"><?php echo $filters['lat'] ? 'GPS OK' : 'V okolí'; ?></button>
                    <?php if($filters['lat']): ?>
                    <input type="range" name="radius" min="5" max="500" step="10" value="<?php echo $filters['radius']; ?>" class="w-full h-2 accent-lec-teal" oninput="document.getElementById('rv').innerText = this.value">
                    <div class="text-[10px] font-bold mt-2 text-slate-400"><?php echo $t['radius']; ?>: <span id="rv"><?php echo $filters['radius']; ?></span> km</div>
                    <?php endif; ?>
                </div>
                <div class="mb-6">
                    <label class="text-[10px] font-bold uppercase text-slate-400 mb-3 block"><?php echo $t['country']; ?></label>
                    <?php foreach(['sk'=>'🇸🇰 SK','cz'=>'🇨🇿 CZ','pl'=>'🇵🇱 PL'] as $c=>$n): ?>
                        <label class="flex items-center gap-2 mb-2"><input type="checkbox" name="countries[]" value="<?php echo $c; ?>" <?php echo in_array($c, $filters['countries']) ? 'checked' : ''; ?> class="rounded text-lec-teal"><span class="text-sm font-bold text-slate-600"><?php echo $n; ?></span></label>
                    <?php endforeach; ?>
                </div>
                <div class="mb-6">
                    <label class="text-[10px] font-bold uppercase text-slate-400 mb-3 block">Cena (<?php echo $currency; ?>)</label>
                    <div class="flex gap-2"><input type="number" name="min_price" placeholder="Od" value="<?php echo h($filters['min_price']); ?>" class="w-1/2 bg-slate-50 border rounded-xl px-2 py-2 text-xs font-bold outline-none focus:border-lec-teal"><input type="number" name="max_price" placeholder="Do" value="<?php echo h($filters['max_price']); ?>" class="w-1/2 bg-slate-50 border rounded-xl px-2 py-2 text-xs font-bold outline-none focus:border-lec-teal"></div>
                </div>
                <button type="submit" class="w-full bg-lec-dark text-white py-4 rounded-xl font-bold text-xs uppercase"><?php echo $t['filter_btn']; ?></button>
            </form>
        </aside>
        <main class="flex-1">
            <div class="flex flex-wrap gap-2 justify-center mb-8">
                <?php foreach($categories as $c): ?>
                    <label class="cursor-pointer"><input type="checkbox" name="cat[]" value="<?php echo $c['slug']; ?>" form="filterForm" class="hidden peer" <?php echo in_array($c['slug'], (array)$filters['cat']) ? 'checked' : ''; ?> onchange="this.form.submit()"><div class="px-4 py-2 rounded-full border border-slate-200 text-[10px] font-bold uppercase peer-checked:bg-lec-dark peer-checked:text-white bg-white text-slate-500"><i class="fa <?php echo $c['icon']; ?> mr-1"></i> <?php echo h($c['name'.$suffix]); ?></div></label>
                <?php endforeach; ?>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $p): ?>
                    <a href="detail.php?id=<?php echo $p['id']; ?>" class="group bg-white rounded-[2rem] p-3 border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col">
                        <div class="h-48 overflow-hidden rounded-[1.5rem] relative mb-4"><img src="<?php echo $p['image_url']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700"><?php if (isset($p['distance'])): ?><div class="absolute bottom-3 left-3 bg-lec-dark/80 text-white px-2 py-1 rounded text-[9px] font-bold uppercase">📍 <?php echo round($p['distance'], 1); ?> km</div><?php endif; ?></div>
                        <h3 class="font-extrabold text-base mb-4 px-2 line-clamp-2"><?php echo h($p['title'.$suffix]); ?></h3>
                        <div class="mt-auto flex justify-between items-center px-2 border-t pt-3"><span class="text-lg font-black text-lec-dark"><?php echo formatPrice($p[$price_col]); ?></span><span class="text-[9px] font-bold text-slate-400 italic"><?php echo h($p['duration_minutes']); ?> m</span></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    <script>
        document.getElementById('locate-btn')?.addEventListener('click', () => { navigator.geolocation.getCurrentPosition((pos) => { document.getElementById('lat-input').value = pos.coords.latitude; document.getElementById('lng-input').value = pos.coords.longitude; document.getElementById('filterForm').submit(); }); });
    </script>
</body>
</html>
