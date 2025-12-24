<?php

/**
 * Portfolio Archive Page
 * HIVEMEDIA Portfolio System
 */
require_once 'admin/config.php';

$db = getDB();

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Filter by category
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build query
$where = "WHERE status = 'published'";
$params = [];

if ($category) {
    $where .= " AND category = ?";
    $params[] = $category;
}

// Get total count
$countSql = "SELECT COUNT(*) FROM portfolio_articles $where";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Get articles
$sql = "SELECT * FROM portfolio_articles $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get categories
$categories = $db->query("SELECT DISTINCT category FROM portfolio_articles WHERE category IS NOT NULL AND category != '' AND status = 'published'")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>포트폴리오 아카이브 - HIVEMEDIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .archive-page {
            min-height: 100vh;
            background: var(--black, #0a0a0a);
            padding: 10rem 0 5rem;
        }

        .archive-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .archive-header h1 {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 600;
            color: #fff;
            margin-bottom: 1rem;
        }

        .archive-header p {
            font-size: 1.4rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .filter-bar {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 4rem;
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 3rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #94BDF7;
            border-color: #94BDF7;
            color: #000;
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .portfolio-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            overflow: hidden;
            transition: all 0.3s;
        }

        .portfolio-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .portfolio-card__image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.2);
            font-size: 3rem;
        }

        .portfolio-card__image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .portfolio-card__content {
            padding: 2rem;
        }

        .portfolio-card__category {
            font-size: 0.9rem;
            color: #94BDF7;
            margin-bottom: 0.5rem;
        }

        .portfolio-card__title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 1rem;
        }

        .portfolio-card__title a {
            color: inherit;
            text-decoration: none;
        }

        .portfolio-card__title a:hover {
            color: #94BDF7;
        }

        .portfolio-card__meta {
            display: flex;
            gap: 1.5rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .portfolio-card__tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .portfolio-card__tag {
            padding: 0.3rem 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 2rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 4rem;
        }

        .pagination a {
            padding: 0.8rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }

        .pagination a:hover,
        .pagination a.active {
            background: #94BDF7;
            border-color: #94BDF7;
            color: #000;
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .admin-link {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 2rem;
            background: #94BDF7;
            color: #000;
            text-decoration: none;
            border-radius: 3rem;
            font-weight: 600;
            transition: all 0.3s;
            z-index: 100;
        }

        .admin-link:hover {
            background: #fff;
        }
    </style>
</head>

<body>
    <div class="archive-page">
        <div class="archive-header">
            <h1>Portfolio</h1>
            <p>하이브미디어의 프로젝트 아카이브</p>
        </div>

        <div class="filter-bar">
            <a href="archive.php" class="filter-btn <?= !$category ? 'active' : '' ?>">전체</a>
            <?php foreach ($categories as $cat): ?>
                <a href="?category=<?= urlencode($cat) ?>" class="filter-btn <?= $category === $cat ? 'active' : '' ?>"><?= sanitize($cat) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($articles)): ?>
            <div class="empty-state">
                <h3>아직 포트폴리오가 없습니다</h3>
                <p>곧 멋진 프로젝트들이 업로드될 예정입니다!</p>
            </div>
        <?php else: ?>
            <div class="portfolio-grid">
                <?php foreach ($articles as $article): ?>
                    <article class="portfolio-card">
                        <div class="portfolio-card__image">
                            <?php if ($article['thumbnail']): ?>
                                <img src="<?= sanitize($article['thumbnail']) ?>" alt="<?= sanitize($article['title']) ?>">
                            <?php else: ?>
                                📁
                            <?php endif; ?>
                        </div>
                        <div class="portfolio-card__content">
                            <div class="portfolio-card__category"><?= sanitize($article['category'] ?? '프로젝트') ?></div>
                            <h2 class="portfolio-card__title">
                                <a href="article.php?slug=<?= urlencode($article['slug']) ?>"><?= sanitize($article['title']) ?></a>
                            </h2>
                            <div class="portfolio-card__meta">
                                <?php if ($article['client']): ?>
                                    <span>👤 <?= sanitize($article['client']) ?></span>
                                <?php endif; ?>
                                <span>📅 <?= date('Y.m.d', strtotime($article['created_at'])) ?></span>
                            </div>
                            <?php if ($article['tags']): ?>
                                <div class="portfolio-card__tags">
                                    <?php foreach (explode(',', $article['tags']) as $tag): ?>
                                        <span class="portfolio-card__tag"><?= sanitize(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= $category ? '&category=' . urlencode($category) : '' ?>">←</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?><?= $category ? '&category=' . urlencode($category) : '' ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?><?= $category ? '&category=' . urlencode($category) : '' ?>">→</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <a href="admin/login.php" class="admin-link">✏️ 글쓰기</a>
</body>

</html>