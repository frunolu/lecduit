-- Adminer 5.4.1 MariaDB 11.4.3-MariaDB-ubu2004 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-star',
  `name_sk` varchar(100) NOT NULL,
  `name_cz` varchar(100) NOT NULL,
  `name_pl` varchar(100) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `name_de` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `slug`, `icon`, `name_sk`, `name_cz`, `name_pl`, `name_en`, `name_de`) VALUES
(1,	'top-tipy',	'fa-star',	'⭐ TOP Tipy',	'⭐ TOP Tipy',	'⭐ TOP Tipy',	'⭐ TOP Tips',	'⭐ TOP-Tipps'),
(2,	'voda-leto',	'fa-water',	'🌊 Voda & Leto',	'🌊 Voda & Léto',	'🌊 Woda i Lato',	'🌊 Water & Summer',	'🌊 Wasser & Sommer'),
(3,	'relax-wellness',	'fa-spa',	'🧖‍♀️ Relax & Wellness',	'🧖‍♀️ Relax & Wellness',	'🧖‍♀️ Relaks i Wellness',	'🧖‍♀️ Relax & Wellness',	'🧖‍♀️ Entspannung & Wellness'),
(4,	'priroda-vylety',	'fa-tree',	'🌲 Príroda & Výlety',	'🌲 Příroda & Výlety',	'🌲 Przyroda i Wycieczki',	'🌲 Nature & Trips',	'🌲 Natur & Ausflüge'),
(5,	'ubytovanie',	'fa-home',	'🏡 Ubytovanie & Víkendy',	'🏡 Ubytování & Víkendy',	'🏡 Noclegi i Weekendy',	'🏡 Accommodation & Weekends',	'🏡 Unterkunft & Wochenenden'),
(6,	'zazitky-adrenalin',	'fa-bolt',	'⚡ Zážitky & Adrenalín',	'⚡ Zážitky & Adrenalin',	'⚡ Wrażenia i Adrenalina',	'⚡ Experiences & Adrenaline',	'⚡ Erlebnisse & Adrenalin'),
(7,	'rodina-deti',	'fa-child',	'👨‍👩‍👧‍👦 Rodina & Deti',	'👨‍👩‍👧‍👦 Rodina & Děti',	'👨‍👩‍👧‍👦 Rodzina i Dzieci',	'👨‍👩‍👧‍👦 Family & Kids',	'👨‍👩‍👧‍👦 Familie & Kinder'),
(8,	'kultura-akcie',	'fa-theater-masks',	'🎭 Kultúra & Akcie',	'🎭 Kultura & Akce',	'🎭 Kultura i Wydarzenia',	'🎭 Culture & Events',	'🎭 Kultur & Veranstaltungen'),
(9,	'zima-sneh',	'fa-snowflake',	'❄️ Zima & Sneh',	'❄️ Zima & Sníh',	'❄️ Zima i Śnieg',	'❄️ Winter & Snow',	'❄️ Winter & Schnee');

DROP TABLE IF EXISTS `experiences`;
CREATE TABLE `experiences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subcategory_id` int(11) NOT NULL,
  `title_sk` varchar(255) DEFAULT NULL,
  `title_cz` varchar(255) DEFAULT NULL,
  `title_pl` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `title_de` varchar(255) DEFAULT NULL,
  `desc_sk` text DEFAULT NULL,
  `desc_cz` text DEFAULT NULL,
  `desc_pl` text DEFAULT NULL,
  `desc_en` text DEFAULT NULL,
  `desc_de` text DEFAULT NULL,
  `price_sk` decimal(10,2) DEFAULT 0.00,
  `price_cz` decimal(10,2) DEFAULT 0.00,
  `price_pl` decimal(10,2) DEFAULT 0.00,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `region_slug` varchar(50) DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `duration_minutes` int(11) DEFAULT 60,
  `image_url` varchar(255) DEFAULT 'default.jpg',
  `is_active` tinyint(1) DEFAULT 1,
  `can_buy_voucher` tinyint(1) DEFAULT 1,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subcategory_id` (`subcategory_id`),
  KEY `idx_region` (`region_slug`),
  CONSTRAINT `experiences_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `experiences` (`id`, `subcategory_id`, `title_sk`, `title_cz`, `title_pl`, `title_en`, `title_de`, `desc_sk`, `desc_cz`, `desc_pl`, `desc_en`, `desc_de`, `price_sk`, `price_cz`, `price_pl`, `lat`, `lng`, `country`, `region_slug`, `difficulty`, `duration_minutes`, `image_url`, `is_active`, `can_buy_voucher`, `contact_phone`, `contact_email`, `contact_website`) VALUES
(1,	3,	'Bungee jumping Lafranconi',	'Bungee jumping Lafranconi',	'Bungee Lafranconi',	'Bungee Jump',	NULL,	'Zoskok z mosta nad Dunajom.',	'Seskok z mostu nad Dunajem.',	NULL,	NULL,	NULL,	59.00,	1490.00,	260.00,	48.14810000,	17.07240000,	'sk',	'bratislava',	'medium',	30,	'https://images.unsplash.com/photo-1522069634954-469a4736f875',	1,	1,	NULL,	NULL,	NULL),
(2,	8,	'Noc v luxusnom glampingu',	'Noc v luxusním glampingu',	'Noc w glampingu',	'Luxury Glamping Night',	NULL,	'Romantický pobyt v prírode s vírivkou.',	'Romantický pobyt v přírodě s vířivkou.',	NULL,	NULL,	NULL,	150.00,	3800.00,	650.00,	48.55000000,	19.15000000,	'sk',	'stredne-slovensko',	'medium',	1440,	'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7',	1,	1,	NULL,	NULL,	NULL),
(3,	11,	'Vstup do DinoParku',	'Vstup do DinoParku',	'Wstęp do DinoParku',	'DinoPark Entry',	NULL,	'Zábava pre celú rodinu medzi dinosaurami.',	'Zábava pro celou rodinu mezi dinosaury.',	NULL,	NULL,	NULL,	15.00,	390.00,	65.00,	48.15000000,	17.05000000,	'sk',	'bratislava',	'medium',	180,	'https://images.unsplash.com/photo-1551390313-05459392e210',	1,	1,	NULL,	NULL,	NULL),
(4,	1,	'Rafting v Čunove',	'Rafting v Čunovu',	'Rafting w Cunovo',	'Whitewater Rafting',	NULL,	'Divoká voda na olympijskom kanáli.',	'Divoká voda na olympijském kanálu.',	NULL,	NULL,	NULL,	35.00,	890.00,	150.00,	48.03150000,	17.23090000,	'sk',	'bratislava',	'medium',	120,	'https://images.unsplash.com/photo-1530866495561-507c9faab2ed',	1,	1,	NULL,	NULL,	NULL),
(5,	5,	'Lyžovačka na Chopku',	'Lyžování na Chopku',	'Narty na Chopoku',	'Skiing Jasna',	NULL,	'Celodenný skipas do najlepšieho strediska.',	'Celodenní skipas do nejlepšího střediska.',	NULL,	NULL,	NULL,	49.00,	1250.00,	210.00,	48.94000000,	19.59000000,	'sk',	'nizke-tatry',	'medium',	480,	'https://images.unsplash.com/photo-1551698618-1dfe5d97d256',	1,	1,	NULL,	NULL,	NULL),
(6,	7,	'Jazda na Ferrari v Brne',	'Jízda na Ferrari v Brně',	'Jazda Ferrari Brno',	'Ferrari Drive Brno',	NULL,	'Vyskúšajte silu 600 koní na okruhu.',	'Vyzkoušejte sílu 600 koní na okruhu.',	NULL,	NULL,	NULL,	199.00,	4990.00,	890.00,	49.20330000,	16.44440000,	'cz',	'morava',	'medium',	45,	'https://images.unsplash.com/photo-1583121274602-3e2820c69888',	1,	1,	NULL,	NULL,	NULL),
(7,	13,	'Thajská masáž v Prahe',	'Thajská masáž v Praze',	'Masaż tajski Praga',	'Thai Massage Prague',	NULL,	'Tradičná uvoľňujúca technika.',	'Tradiční uvolňující technika.',	NULL,	NULL,	NULL,	45.00,	1100.00,	190.00,	50.07550000,	14.43780000,	'cz',	'praha',	'medium',	60,	'https://images.unsplash.com/photo-1544161515-4ab6ce6db874',	1,	1,	NULL,	NULL,	NULL),
(8,	4,	'Let balónom nad Karlštejnom',	'Let balónem nad Karlštejnem',	'Lot balonem Karlstejn',	'Balloon Flight',	NULL,	'Ikonický výhľad na český hrad.',	'Ikonický výhled na český hrad.',	NULL,	NULL,	NULL,	180.00,	4500.00,	780.00,	49.93900000,	14.18800000,	'cz',	'stredne-cechy',	'medium',	60,	'https://images.unsplash.com/photo-1507608158173-1dcec673a2e5',	1,	1,	NULL,	NULL,	NULL),
(9,	6,	'Kurz varenia piva v Plzni',	'Kurz vaření piva v Plzni',	'Kurs warzenia piwa',	'Beer Brewing Course',	NULL,	'Uvarte si vlastnú várku piva s majstrom.',	'Uvařte si vlastní várku piva s mistrem.',	NULL,	NULL,	NULL,	79.00,	1990.00,	350.00,	49.74750000,	13.37750000,	'cz',	'zapadne-cechy',	'medium',	360,	'https://images.unsplash.com/photo-1535958636474-b021ee887b13',	1,	0,	'+420 123 456 789',	'pivo@plzen.cz',	'https://www.plzenpivo.cz'),
(10,	12,	'Národné technické múzeum',	'Národní technické muzeum',	'Muzeum Techniki Praga',	'Technical Museum',	NULL,	'Expozícia historických áut a lietadiel.',	'Expozice historických aut a letadel.',	NULL,	NULL,	NULL,	10.00,	250.00,	45.00,	50.09750000,	14.42500000,	'cz',	'praha',	'medium',	120,	'https://images.unsplash.com/photo-1566378246598-5b11a0ff7f6c',	1,	0,	'+420 987 654 321',	'info@ntm.cz',	'https://www.ntm.cz'),
(11,	6,	'Strelnica v Krakove',	'Střelnice v Krakově',	'Strzelnica Kraków',	'Shooting Range',	NULL,	'Zastrieľajte si z AK-47 a Glocku.',	'Zastřílejte si z AK-47 a Glocku.',	NULL,	NULL,	NULL,	40.00,	1000.00,	180.00,	50.06460000,	19.94490000,	'pl',	'malopolska',	'medium',	60,	'https://images.unsplash.com/photo-1595590424283-b8f17842773f',	1,	1,	NULL,	NULL,	NULL),
(12,	10,	'Horská chata v Zakopanom',	'Horská chata v Zakopaném',	'Domek v Zakopanem',	'Zakopane Cabin',	NULL,	'Ubytovanie v srdci poľských Tatier.',	'Ubytování v srdci polských Tater.',	NULL,	NULL,	NULL,	90.00,	2200.00,	400.00,	49.29910000,	19.94890000,	'pl',	'tatry',	'medium',	1440,	'https://images.unsplash.com/photo-1518732714860-b62714ce0c59',	1,	1,	NULL,	NULL,	NULL),
(13,	2,	'Plavba jachtou v Gdansku',	'Plavba jachtou v Gdaňsku',	'Rejs jachtem Gdańsk',	'Yacht Cruise',	NULL,	'Súkromná plavba po Baltskom mori.',	'Soukromá plavba po Baltském moři.',	NULL,	NULL,	NULL,	200.00,	5000.00,	850.00,	54.35200000,	18.64660000,	'pl',	'balt',	'medium',	120,	'https://images.unsplash.com/photo-1534447677768-be436bb09401',	1,	0,	'+48 500 600 700',	'marine@gdansk.pl',	'https://www.gdansk-jachty.pl'),
(14,	11,	'Energylandia Zator',	'Energylandia Zator',	'Energylandia',	'Energylandia Park',	NULL,	'Najväčší zábavný park v strednej Európe.',	'Největší zábavní park ve střední Evropě.',	NULL,	NULL,	NULL,	45.00,	1150.00,	199.00,	49.99700000,	19.41200000,	'pl',	'malopolska',	'medium',	480,	'https://images.unsplash.com/photo-1513889953293-4ad464fb7a79',	1,	1,	NULL,	NULL,	NULL),
(15,	13,	'Slaný bazén vo Wieliczke',	'Slaný bazén ve Wieliczce',	'Basen solankowy Wieliczka',	'Salt Mine Pool',	NULL,	'Unikátny relax v hĺbke 100 metrov pod zemou.',	'Unikátní relax v hloubce 100 metrů pod zemí.',	NULL,	NULL,	NULL,	30.00,	750.00,	130.00,	49.98300000,	20.05500000,	'pl',	'malopolska',	'medium',	90,	'https://images.unsplash.com/photo-1560067174-c5a3a8f37060',	1,	1,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `experience_tags`;
CREATE TABLE `experience_tags` (
  `experience_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`experience_id`,`tag_id`),
  KEY `experience_tags_ibfk_2` (`tag_id`),
  CONSTRAINT `experience_tags_ibfk_1` FOREIGN KEY (`experience_id`) REFERENCES `experiences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `experience_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `experience_tags` (`experience_id`, `tag_id`) VALUES
(3,	1),
(14,	1),
(7,	4),
(11,	4),
(15,	4),
(1,	5),
(4,	5),
(5,	5),
(6,	5);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `market_id` varchar(2) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `status` enum('pending','paid','cancelled','refunded') DEFAULT 'pending',
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `billing_street` varchar(255) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_zip` varchar(20) DEFAULT NULL,
  `billing_country` char(2) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(5) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'card',
  `payment_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `market_id`, `order_number`, `status`, `email`, `first_name`, `last_name`, `phone`, `billing_street`, `billing_city`, `billing_zip`, `billing_country`, `total_amount`, `currency`, `tax_rate`, `payment_method`, `payment_id`, `created_at`, `updated_at`) VALUES
(1,	'sk',	'202619798',	'pending',	'fruno.lu@gmail.com',	'Lukas',	'Fruno',	'+420606218592',	'Matlachova',	'Brno',	'62700',	'SK',	840.00,	'€',	20.00,	'card',	NULL,	'2026-02-05 22:18:44',	'2026-02-05 22:18:44'),
(2,	'sk',	'202634256',	'pending',	'fruno.lu@gmail.com',	'Lukas',	'Fruno',	'+421905618081',	'Riečna ulica',	'Brodzany',	'958 42',	'SK',	40.00,	'€',	20.00,	'card',	NULL,	'2026-02-06 08:48:01',	'2026-02-06 08:48:01');

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `experience_id` int(11) NOT NULL,
  `title_snapshot` varchar(255) NOT NULL,
  `price_unit` decimal(10,2) NOT NULL,
  `qty` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_items` (`id`, `order_id`, `experience_id`, `title_snapshot`, `price_unit`, `qty`) VALUES
(1,	1,	10,	'Weinprobe in Mähren',	40.00,	9),
(2,	1,	5,	'Thailändische Rückenmassage',	45.00,	2),
(3,	1,	9,	'Offroad expedícia Hummer',	150.00,	2),
(4,	1,	11,	'Sushi Cooking Course',	90.00,	1),
(5,	2,	10,	'Degustácia vín na Morave',	40.00,	1);

DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name_sk` varchar(100) DEFAULT NULL,
  `name_cz` varchar(100) DEFAULT NULL,
  `name_pl` varchar(100) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `name_de` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `subcategories` (`id`, `category_id`, `slug`, `name_sk`, `name_cz`, `name_pl`, `name_en`, `name_de`) VALUES
(1,	2,	'aquaparky',	'Aquaparky',	'Aquaparky',	'Aquaparki',	'Aquaparks',	'Wasserparks'),
(2,	2,	'kupaliska',	'Kúpaliská',	'Koupaliště',	'Baseny',	'Swimming Pools',	'Freibäder'),
(3,	6,	'bungee',	'Bungee jumping',	'Bungee jumping',	'Bungee jumping',	'Bungee jumping',	'Bungee-Jumping'),
(4,	6,	'lety-balonom',	'Lety balónom',	'Lety balónem',	'Loty balonem',	'Balloon Flights',	'Ballonfahrten'),
(5,	9,	'lyzovanie',	'Lyžovanie',	'Lyžování',	'Narciarstwo',	'Skiing',	'Skifahren'),
(6,	6,	'strelba',	'Streľba',	'Střelba',	'Strzelanie',	'Shooting',	'Schießen'),
(7,	6,	'auta',	'Rýchle autá',	'Rychlá auta',	'Szybkie auta',	'Fast cars',	'Schnelle Autos'),
(8,	4,	'wellness',	'Wellness',	'Wellness',	'Wellness',	'Wellness',	'Wellness'),
(9,	1,	'top-vyber',	'TOP Výber',	'TOP Výběr',	'TOP Wybór',	'TOP Selection',	NULL),
(10,	5,	'glamping-chaty',	'Glamping & Chaty',	'Glamping & Chaty',	'Glamping i Domki',	'Glamping & Cabins',	NULL),
(11,	7,	'deti-zabava',	'Zábava pre deti',	'Zábava pro děti',	'Zabawa dla dzieci',	'Kids Fun',	NULL),
(12,	8,	'vystavy-muzea',	'Výstavy & Múzeá',	'Výstavy & Muzea',	'Wystawy i Muzea',	'Exhibitions & Museums',	NULL),
(13,	3,	'wellness-spa',	'Wellness & Spa',	'Wellness & Spa',	'Wellness & Spa',	'Wellness & Spa',	NULL);

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-check',
  `name_sk` varchar(50) NOT NULL,
  `name_cz` varchar(50) NOT NULL,
  `name_pl` varchar(50) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `name_de` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tags` (`id`, `code`, `icon`, `name_sk`, `name_cz`, `name_pl`, `name_en`, `name_de`) VALUES
(1,	'kids',	'fa-baby',	'Vhodné pre deti',	'Vhodné pro děti',	'Dla dzieci',	'Suitable for kids',	'Kinderfreundlich'),
(2,	'dog',	'fa-dog',	'Pes povolený',	'Pes povolen',	'Pies dozwolony',	'Dog friendly',	'Hundefreundlich'),
(3,	'barrier',	'fa-wheelchair',	'Bezbariérové',	'Bezbariérové',	'Dla niepełnosprawnych',	'Wheelchair accessible',	'Barrierefrei'),
(4,	'indoor',	'fa-building',	'Interiér (Vnútri)',	'Interiér (Uvnitř)',	'Wewnątrz',	'Indoor',	'Innenbereich'),
(5,	'outdoor',	'fa-sun',	'Exteriér (Vonku)',	'Exteriér (Venku)',	'Na zewnątrz',	'Outdoor',	'Außenbereich');

-- 2026-02-06 10:40:27 UTC

CREATE TABLE `users` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `email` varchar(100) NOT NULL,
                         `first_name` varchar(50) DEFAULT NULL,
                         `last_name` varchar(50) DEFAULT NULL,
                         `google_id` varchar(100) DEFAULT NULL,
                         `apple_id` varchar(100) DEFAULT NULL,
                         `avatar` varchar(255) DEFAULT NULL,
                         `created_at` timestamp NULL DEFAULT current_timestamp(),
                         `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `email` (`email`),
                         UNIQUE KEY `google_id` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;