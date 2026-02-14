<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ExperienceRepository.php';
require_once __DIR__ . '/User.php';

$user = new User();
$currentUser = $user->getCurrentUser();

$repo = new ExperienceRepository($lang, $price_col);
$allTags = $repo->getAllTags();

$filters = [
    'cat' => $_GET['cat'] ?? [],
    'countries' => $_GET['countries'] ?? [],
    'tags' => $_GET['tags'] ?? [],
    'min_price' => $_GET['min_price'] ?? null,
    'max_price' => $_GET['max_price'] ?? null,
    'lat' => $_GET['lat'] ?? null,
    'lng' => $_GET['lng'] ?? null,
    'radius' => $_GET['radius'] ?? 50,
];

$products = $repo->search($filters);
$categories = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll();

$resetUrl = 'index.php?lang=' . urlencode($lang);
$qsAllCat = $_GET;
unset($qsAllCat['cat']);
$allCatUrl = 'index.php' . (!empty($qsAllCat) ? ('?' . http_build_query($qsAllCat)) : '');
$hasCatFilter = !empty($filters['cat']);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecduit.
        <?php echo $market_id; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

<body class="bg-slate-50 text-slate-900 font-sans">

    <header class="bg-white/90 backdrop-blur border-b sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 h-16 md:h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2">
                <img src="lecduit-logo.png" class="h-8 md:h-10">
                <span class="font-extrabold text-xl uppercase tracking-tighter">Lecduit<span class="text-lec-teal">.
                        <?php echo $market_id; ?>
                    </span></span>
            </a>
            <div class="flex items-center gap-3">

                <div class="relative">
                    <button onclick="document.getElementById('langDropdown').classList.toggle('hidden')"
                        class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl text-[10px] font-bold uppercase text-slate-600 hover:text-lec-teal transition">
                        <i class="fa fa-globe text-slate-400"></i>
                        <span>
                            <?php echo $lang; ?>
                        </span>
                        <i class="fa fa-chevron-down text-[8px] ml-1"></i>
                    </button>
                    <div id="langDropdown"
                        class="hidden absolute top-full right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl py-2 min-w-[80px] flex flex-col z-50">
                        <?php foreach (['sk', 'cz', 'pl', 'en', 'de'] as $l): ?>
                        <a href="?lang=<?php echo $l; ?>"
                            class="px-4 py-2 hover:bg-slate-50 text-[10px] font-bold uppercase flex justify-between items-center <?php echo $lang == $l ? 'text-lec-teal bg-slate-50' : 'text-slate-500'; ?>">
                            <?php echo $l; ?>
                            <?php if ($lang == $l): ?><i class="fa fa-check text-[8px]"></i>
                            <?php
    endif; ?>
                        </a>
                        <?php
endforeach; ?>
                    </div>
                </div>

                <?php if ($currentUser): ?>
                <div class="flex items-center gap-2 ml-1 pl-2 border-l border-slate-100">
                    <?php if (!empty($currentUser['avatar'])): ?>
                    <img src="<?php echo h($currentUser['avatar']); ?>"
                        class="w-8 h-8 rounded-full border border-slate-200">
                    <?php
    else: ?>
                    <div class="w-8 h-8 rounded-full bg-lec-teal text-white flex items-center justify-center font-bold">
                        <?php echo substr($currentUser['first_name'], 0, 1); ?>
                    </div>
                    <?php
    endif; ?>
                    <a href="logout.php" class="text-xs text-slate-400 hover:text-red-500 ml-1 p-2" title="Odhlásit"><i
                            class="fa fa-sign-out"></i></a>
                </div>
                <?php
else: ?>
                <a href="login_google.php"
                    class="ml-1 pl-2 border-l border-slate-100 text-xs font-bold uppercase text-slate-500 hover:text-lec-teal flex items-center gap-1 p-2">
                    <i class="fa-brands fa-google"></i> <span class="hidden sm:inline">Login</span>
                </a>
                <?php
endif; ?>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-6 md:py-10 flex flex-col md:flex-row gap-8">

        <aside class="w-full md:w-72 shrink-0">
            <div class="md:hidden mb-4">
                <button type="button" id="filterToggle"
                    class="w-full bg-white border-2 border-slate-100 shadow-sm rounded-2xl px-5 py-4 font-black text-sm uppercase text-slate-700 flex items-center justify-between">
                    <span><i class="fa fa-sliders mr-2 text-lec-teal"></i>
                        <?php echo $t['filter_btn']; ?>
                    </span>
                    <span class="bg-slate-100 px-2 py-1 rounded-lg text-[10px]">
                        <?php echo count(array_filter($filters)); ?>
                    </span>
                </button>
            </div>

            <div id="filterPanel"
                class="fixed inset-0 z-[100] hidden md:block md:relative md:z-0 bg-slate-900/40 backdrop-blur-sm md:bg-transparent">
                <div
                    class="absolute inset-y-0 left-0 w-full max-w-[320px] md:max-w-full bg-white md:bg-transparent h-full md:h-auto flex flex-col shadow-2xl md:shadow-none">

                    <form action="index.php" method="GET" id="filterForm"
                        class="flex flex-col h-full bg-white p-6 md:rounded-[2.5rem] md:border md:border-slate-100 md:sticky md:top-24">

                        <div class="flex md:hidden justify-between items-center mb-6">
                            <h2 class="text-xl font-black uppercase italic tracking-tighter">
                                <?php echo $t['filter_btn']; ?>
                            </h2>
                            <button type="button" id="filterClose"
                                class="w-10 h-10 bg-slate-100 rounded-xl text-slate-400"><i
                                    class="fa fa-times"></i></button>
                        </div>

                        <div class="flex-1 overflow-y-auto pr-2 md:overflow-visible">
                            <input type="hidden" name="lat" id="lat-input" value="<?php echo h($filters['lat']); ?>">
                            <input type="hidden" name="lng" id="lng-input" value="<?php echo h($filters['lng']); ?>">
                            <input type="hidden" name="lang" value="<?php echo h($lang); ?>">

                            <div class="mb-6">
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block">
                                    <?php echo $t['location']; ?>
                                </label>
                                <button type="button" id="locate-btn"
                                    class="w-full py-3 rounded-xl border-2 border-dashed <?php echo $filters['lat'] ? 'border-green-500 text-green-600 bg-green-50' : 'border-lec-teal/30 text-lec-teal'; ?> font-bold text-xs uppercase mb-3">
                                    <i class="fa fa-location-crosshairs mr-2"></i>
                                    <?php echo $filters['lat'] ? 'GPS AKTÍVNE' : 'V okolí (GPS)'; ?>
                                </button>

                                <?php if ($filters['lat']): ?>
                                <div class="px-1">
                                    <input type="range" name="radius" min="5" max="500" step="5"
                                        value="<?php echo $filters['radius']; ?>"
                                        class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-lec-teal"
                                        oninput="document.getElementById('rv').innerText = this.value">
                                    <div
                                        class="flex justify-between text-[10px] font-bold mt-2 text-slate-400 uppercase">
                                        <span>
                                            <?php echo $t['radius']; ?>
                                        </span>
                                        <span class="text-lec-teal font-black"><span id="rv">
                                                <?php echo $filters['radius']; ?>
                                            </span> km</span>
                                    </div>
                                </div>
                                <?php
endif; ?>
                            </div>

                            <div class="mb-6 border-t pt-6 border-slate-50">
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block">
                                    <?php echo $t['country']; ?>
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <?php foreach (['sk' => '🇸🇰 SK', 'cz' => '🇨🇿 CZ', 'pl' => '🇵🇱 PL'] as $c => $n): ?>
                                    <label
                                        class="flex items-center gap-2 p-2 rounded-lg border border-slate-50 hover:bg-slate-50 cursor-pointer">
                                        <input type="checkbox" name="countries[]" value="<?php echo $c; ?>" <?php echo
                                            in_array($c, $filters['countries']) ? 'checked' : '' ; ?> class="w-4 h-4
                                        rounded text-lec-teal border-slate-200">
                                        <span class="text-[11px] font-bold text-slate-600">
                                            <?php echo $n; ?>
                                        </span>
                                    </label>
                                    <?php
endforeach; ?>
                                </div>
                            </div>

                            <div class="mb-6 border-t pt-6 border-slate-50">
                                <label
                                    class="text-[10px] font-black uppercase text-slate-400 mb-3 block">Vlastnosti</label>
                                <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                                    <?php foreach ($allTags as $tag): ?>
                                    <label
                                        class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 cursor-pointer group">
                                        <input type="checkbox" name="tags[]" value="<?php echo h($tag['code']); ?>"
                                            <?php echo in_array($tag['code'], (array)$filters['tags']) ? 'checked' : ''
                                            ; ?> class="w-3.5 h-3.5 rounded text-lec-teal border-slate-200">
                                        <span
                                            class="text-[10px] font-bold text-slate-500 group-hover:text-lec-teal leading-tight">
                                            <?php echo h($tag['name' . $suffix]); ?>
                                        </span>
                                    </label>
                                    <?php
endforeach; ?>
                                </div>
                            </div>

                            <div class="mb-6 border-t pt-6 border-slate-50">
                                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block">Cena (
                                    <?php echo $currency; ?>)
                                </label>
                                <div class="flex gap-2">
                                    <input type="number" name="min_price" placeholder="Od"
                                        value="<?php echo h($filters['min_price']); ?>"
                                        class="w-full bg-slate-50 border-0 rounded-xl px-3 py-2 text-xs font-black outline-none focus:ring-1 focus:ring-lec-teal">
                                    <input type="number" name="max_price" placeholder="Do"
                                        value="<?php echo h($filters['max_price']); ?>"
                                        class="w-full bg-slate-50 border-0 rounded-xl px-3 py-2 text-xs font-black outline-none focus:ring-1 focus:ring-lec-teal">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 bg-white mt-auto flex gap-2">
                            <a href="<?php echo $resetUrl; ?>"
                                class="flex-1 py-3 rounded-xl font-black text-[10px] uppercase bg-slate-50 text-slate-400 text-center flex items-center justify-center border border-slate-100">Reset</a>
                            <button type="submit"
                                class="flex-[2] bg-lec-dark text-white py-3 rounded-xl font-black text-[10px] uppercase shadow-lg shadow-lec-dark/10">
                                <?php echo $t['filter_btn']; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1">
            <div class="flex flex-wrap gap-2 mb-8 justify-start">
                <a href="<?php echo h($allCatUrl); ?>"
                    class="px-4 py-2.5 rounded-xl border-2 transition-all text-[10px] font-black uppercase flex items-center gap-2 <?php echo $hasCatFilter ? 'bg-white border-slate-100 text-slate-400' : 'bg-lec-dark border-lec-dark text-white'; ?>">
                    <i class="fa fa-border-all"></i> Všetko
                </a>

                <?php foreach ($categories as $c): ?>
                <label class="cursor-pointer">
                    <input type="checkbox" name="cat[]" value="<?php echo $c['slug']; ?>" form="filterForm"
                        class="hidden peer" <?php echo in_array($c['slug'], (array)$filters['cat']) ? 'checked' : '' ;
                        ?> onchange="this.form.submit()">
                    <div
                        class="px-4 py-2.5 rounded-xl border-2 transition-all text-[10px] font-black uppercase flex items-center gap-2 peer-checked:bg-lec-dark peer-checked:border-lec-dark peer-checked:text-white bg-white border-slate-100 text-slate-400 hover:border-lec-teal/30">
                        <i class="fa <?php echo h($c['icon']); ?>"></i>
                        <?php echo h($c['name' . $suffix]); ?>
                    </div>
                </label>
                <?php
endforeach; ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $p): ?>
                <a href="detail.php?id=<?php echo $p['id']; ?>"
                    class="group bg-white rounded-[2.5rem] p-4 border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col">
                    <div class="aspect-[4/3] overflow-hidden rounded-[2rem] relative mb-4">
                        <img src="<?php echo h($p['image_url']); ?>"
                            class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                            alt="Zážitek">
                        <?php if (isset($p['distance'])): ?>
                        <div
                            class="absolute top-3 left-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-[10px] font-black text-lec-dark">
                            📍
                            <?php echo round($p['distance'], 1); ?> km
                        </div>
                        <?php
    endif; ?>
                    </div>
                    <h3 class="font-black text-base mb-4 px-2 line-clamp-2 leading-tight">
                        <?php echo h($p['title' . $suffix]); ?>
                    </h3>
                    <div class="mt-auto flex justify-between items-end px-2 pb-2">
                        <span class="text-xl font-black text-lec-dark tracking-tighter">
                            <?php echo formatPrice($p[$price_col]); ?>
                        </span>
                        <div class="bg-slate-50 px-3 py-1.5 rounded-lg text-[9px] font-black text-slate-400 uppercase">
                            <?php echo h($p['duration_minutes']); ?> m
                        </div>
                    </div>
                </a>
                <?php
endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filtr logika
            const filterToggle = document.getElementById('filterToggle');
            const filterClose = document.getElementById('filterClose');
            const filterPanel = document.getElementById('filterPanel');
            const locateBtn = document.getElementById('locate-btn');

            if (filterToggle && filterPanel) {
                filterToggle.addEventListener('click', () => {
                    filterPanel.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            }
            if (filterClose && filterPanel) {
                filterClose.addEventListener('click', () => {
                    filterPanel.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }

            // Kliknutí mimo dropdown zavře menu
            document.addEventListener('click', function (event) {
                const dropdown = document.getElementById('langDropdown');
                const button = dropdown.previousElementSibling; // Tlačítko před dropdownem

                // Pokud kliknutí nebylo uvnitř dropdownu ani na tlačítku, zavřeme ho
                if (!dropdown.contains(event.target) && !button.contains(event.target) && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });

            if (locateBtn) {
                locateBtn.addEventListener('click', function () {
                    locateBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>';
                    if (!navigator.geolocation) return;
                    navigator.geolocation.getCurrentPosition(function (pos) {
                        document.getElementById('lat-input').value = pos.coords.latitude;
                        document.getElementById('lng-input').value = pos.coords.longitude;
                        document.getElementById('filterForm').submit();
                    });
                });
            }
        });
    </script>
</body>

</html>