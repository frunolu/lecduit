<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ExperienceRepository.php';
require_once __DIR__ . '/User.php';

$user = new User();
$currentUser = $user->getCurrentUser();
$repo = new ExperienceRepository($lang, $price_col);

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

// Načítání detailu zážitku
$sql = "SELECT p.*, c.name$suffix as cat_name, s.name$suffix as sub_name 
        FROM experiences p 
        JOIN subcategories s ON p.subcategory_id = s.id 
        JOIN categories c ON s.category_id = c.id 
        WHERE p.id = :id AND p.is_active = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$p = $stmt->fetch();

if (!$p)
    die("Error: Zážitek nenalezen.");

$productTags = $repo->getTagsForExperience($id);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo h($p['title' . $suffix]); ?> | Lecduit
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

<body class="bg-slate-50 text-slate-900 font-sans antialiased">

    <header class="bg-white/90 backdrop-blur border-b sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 h-16 md:h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2">
                <img src="lecduit-logo.png" class="h-8 md:h-10">
                <span class="font-extrabold text-xl uppercase">Lecduit<span class="text-lec-teal">.
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
                        <a href="?id=<?php echo $id; ?>&lang=<?php echo $l; ?>"
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

                <a href="index.php"
                    class="text-xs font-bold uppercase text-slate-400 hover:text-lec-teal transition hidden sm:inline-block ml-2">
                    <i class="fa fa-arrow-left mr-1"></i>
                    <?php echo $t['back']; ?>
                </a>
                <a href="index.php"
                    class="text-xs font-bold uppercase text-slate-400 hover:text-lec-teal transition sm:hidden ml-2">
                    <i class="fa fa-arrow-left"></i>
                </a>

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

    <main class="max-w-6xl mx-auto px-4 py-8">

        <div class="flex flex-col md:flex-row gap-10">
            <div class="w-full md:w-1/2">
                <div class="sticky top-28">
                    <div class="aspect-[4/3] rounded-[2.5rem] overflow-hidden shadow-2xl">
                        <img src="<?php echo h($p['image_url']); ?>" class="w-full h-full object-cover"
                            alt="Experience">
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <?php foreach ($productTags as $tag): ?>
                        <span
                            class="bg-white border border-slate-100 px-4 py-2 rounded-full text-[10px] font-bold uppercase text-slate-500 shadow-sm">
                            <i class="fa <?php echo h($tag['icon']); ?> text-lec-teal mr-2"></i>
                            <?php echo h($tag['name' . $suffix]); ?>
                        </span>
                        <?php
endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/2 flex flex-col">
                <nav class="flex items-center gap-2 text-[10px] font-bold uppercase text-slate-400 mb-4">
                    <span>
                        <?php echo h($p['cat_name']); ?>
                    </span>
                    <i class="fa fa-chevron-right text-[8px]"></i>
                    <span class="text-lec-teal">
                        <?php echo h($p['sub_name']); ?>
                    </span>
                </nav>

                <h1 class="text-3xl md:text-4xl font-black leading-tight mb-6">
                    <?php echo h($p['title' . $suffix]); ?>
                </h1>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 flex items-center gap-3 shadow-sm">
                        <div class="w-10 h-10 bg-lec-teal/10 rounded-xl flex items-center justify-center text-lec-teal">
                            <i class="fa fa-calendar-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Dostupnost</p>
                            <p class="text-xs font-black">
                                <?php echo $t['validity']; ?>
                            </p>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 flex items-center gap-3 shadow-sm">
                        <div
                            class="w-10 h-10 bg-lec-orange/10 rounded-xl flex items-center justify-center text-lec-orange">
                            <i class="fa fa-bolt text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Doručení</p>
                            <p class="text-xs font-black">
                                <?php echo $t['delivery']; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="prose prose-slate mb-10">
                    <h3 class="text-[10px] font-bold uppercase text-slate-400 mb-3 tracking-widest">
                        <?php echo $t['desc']; ?>
                    </h3>
                    <p class="text-slate-600 leading-relaxed font-medium">
                        <?php echo nl2br(h($p['description' . $suffix] ?? $p['desc' . $suffix])); ?>
                    </p>
                </div>

                <div
                    class="mt-auto bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-lec-teal/5 rounded-full -mr-16 -mt-16"></div>

                    <div class="mb-6 relative z-10 border-b border-slate-50 pb-6">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">
                            <?php echo $t['price_for']; ?>
                        </span>
                        <span class="text-4xl font-black text-lec-dark">
                            <?php echo formatPrice($p[$price_col]); ?>
                        </span>
                    </div>

                    <div class="space-y-4 relative z-10">
                        <h4 class="font-black text-lg uppercase italic">
                            <?php echo $t['contact']; ?>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php if ($p['contact_phone']): ?>
                            <a href="tel:<?php echo h($p['contact_phone']); ?>"
                                class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-lec-teal transition group">
                                <i class="fa fa-phone text-lec-teal"></i>
                                <span class="text-xs font-bold text-slate-600">
                                    <?php echo h($p['contact_phone']); ?>
                                </span>
                            </a>
                            <?php
endif; ?>
                            <?php if ($p['contact_email']): ?>
                            <a href="mailto:<?php echo h($p['contact_email']); ?>"
                                class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-lec-teal transition group">
                                <i class="fa fa-envelope text-lec-teal"></i>
                                <span class="text-xs font-bold text-slate-600 truncate">
                                    <?php echo h($p['contact_email']); ?>
                                </span>
                            </a>
                        </div>
                        <?php
endif; ?>
                        <?php if ($p['contact_website']): ?>
                        <a href="<?php echo h($p['contact_website']); ?>" target="_blank"
                            class="block w-full text-center bg-lec-dark text-white py-5 rounded-2xl font-bold text-xs uppercase hover:bg-lec-teal transition shadow-lg">
                            <i class="fa fa-external-link mr-2"></i>
                            <?php echo $t['visit_web']; ?>
                        </a>
                        <?php
endif; ?>
                        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-start gap-3">
                            <i class="fa fa-info-circle text-blue-500 mt-1"></i>
                            <p class="text-[10px] font-bold text-blue-700 leading-tight uppercase">
                                <?php echo $t['catalog_info']; ?>
                            </p>
                        </div>
                    </div>