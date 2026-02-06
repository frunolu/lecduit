<?php
require_once __DIR__ . '/Database.php';

class ExperienceRepository {
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

    /**
     * Načte tagy (vlastnosti) pro konkrétní zážitek
     */
    public function getTagsForExperience($id) {
        $sql = "SELECT t.* FROM tags t 
                JOIN experience_tags et ON t.id = et.tag_id 
                WHERE et.experience_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public function search(array $filters = []) {
        $params = [];
        $suffix = '_' . $this->lang;

        $sqlSelect = "SELECT p.*, c.name$suffix as cat_name, c.slug as cat_slug, s.name$suffix as sub_name";

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

        if (!empty($filters['countries'])) {
            $phs = [];
            foreach ($filters['countries'] as $k => $v) {
                $ph = ":co$k";
                $phs[] = $ph;
                $params[$ph] = $v;
            }
            $sql .= " AND p.country IN (" . implode(',', $phs) . ")";
        }

        if (!empty($filters['cat'])) {
            $phs = [];
            foreach ((array)$filters['cat'] as $k => $v) {
                $ph = ":ca$k";
                $phs[] = $ph;
                $params[$ph] = $v;
            }
            $sql .= " AND c.slug IN (" . implode(',', $phs) . ")";
        }

        if (!empty($filters['tags'])) {
            $phs = [];
            foreach ((array)$filters['tags'] as $k => $v) {
                $ph = ":t$k";
                $phs[] = $ph;
                $params[$ph] = $v;
            }
            $sql .= " AND EXISTS (
                SELECT 1 FROM experience_tags et 
                JOIN tags t ON et.tag_id = t.id 
                WHERE et.experience_id = p.id AND t.code IN (" . implode(',', $phs) . ")
            )";
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $sql .= " AND p.{$this->priceCol} >= :min";
            $params[':min'] = $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $sql .= " AND p.{$this->priceCol} <= :max";
            $params[':max'] = $filters['max_price'];
        }

        if (!empty($filters['lat']) && isset($filters['radius'])) {
            $sql .= " HAVING distance <= :rad";
            $params[':rad'] = $filters['radius'];
        }

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