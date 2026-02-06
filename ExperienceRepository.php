<?php
require_once __DIR__ . '/Database.php';

class ExperienceRepository {
    // Opraveno: Každá vlastnost třídy musí mít definovanou viditelnost
    private $pdo;
    private $lang;
    private $priceCol;

    public function __construct($lang, $priceCol) {
        $this->pdo = Database::getInstance();
        $this->lang = $lang;
        $this->priceCol = $priceCol;
    }

    public function getAllTags() { 
        return $this->pdo->query("SELECT * FROM tags ORDER BY id")->fetchAll(); 
    }

    public function search(array $filters = []) {
        $params = [];
        $suffix = '_' . $this->lang;
        
        // Základní výběr sloupců
        $sqlSelect = "SELECT p.*, c.name$suffix as cat_name, c.slug as cat_slug, s.name$suffix as sub_name";

        // VÝPOČET VZDÁLENOSTI (Haversine Formula)
        if (!empty($filters['lat']) && !empty($filters['lng'])) {
            $sqlSelect .= ", ( 6371 * acos( cos( radians(:lat1) ) * cos( radians( p.lat ) ) * cos( radians( p.lng ) - radians(:lng) ) + sin( radians(:lat2) ) * sin( radians( p.lat ) ) ) ) AS distance";
            $params[':lat1'] = $filters['lat']; 
            $params[':lat2'] = $filters['lat']; 
            $params[':lng']  = $filters['lng'];
        } else { 
            $sqlSelect .= ", NULL as distance"; 
        }

        $sql = "$sqlSelect FROM experiences p 
                JOIN subcategories s ON p.subcategory_id = s.id 
                JOIN categories c ON s.category_id = c.id 
                WHERE p.is_active = 1";

        // Filtr zemí
        if (!empty($filters['countries'])) {
            $phs = []; 
            foreach ($filters['countries'] as $k => $v) { 
                $ph = ":co$k"; 
                $phs[] = $ph; 
                $params[$ph] = $v; 
            }
            $sql .= " AND p.country IN (" . implode(',', $phs) . ")";
        }

        // Filtr kategorií
        if (!empty($filters['cat'])) {
            $phs = []; 
            foreach ((array)$filters['cat'] as $k => $v) { 
                $ph = ":ca$k"; 
                $phs[] = $ph; 
                $params[$ph] = $v; 
            }
            $sql .= " AND c.slug IN (" . implode(',', $phs) . ")";
        }

        // Filtr ceny (včetně nuly)
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $sql .= " AND p.{$this->priceCol} >= :min"; 
            $params[':min'] = $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $sql .= " AND p.{$this->priceCol} <= :max"; 
            $params[':max'] = $filters['max_price'];
        }

        // Filtr rádiusu (používá vypočítanou vzdálenost)
        if (!empty($filters['lat']) && isset($filters['radius'])) {
            $sql .= " HAVING distance <= :rad"; 
            $params[':rad'] = $filters['radius'];
        }

        // Řazení
        $sort = $filters['sort'] ?? 'newest';
        if ($sort === 'price_asc') {
            $sql .= " ORDER BY p.{$this->priceCol} ASC";
        } elseif ($sort === 'nearest' && !empty($filters['lat'])) {
            $sql .= " ORDER BY distance ASC";
        } else {
            $sql .= " ORDER BY p.id DESC";
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
