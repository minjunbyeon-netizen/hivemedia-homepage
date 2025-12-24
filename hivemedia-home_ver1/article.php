<?php

/**
 * Article Detail Page
 * HIVEMEDIA Portfolio System
 */
require_once 'admin/config.php';

$db = getDB();

if (!isset($_GET['slug'])) {
    header('Location: archive.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM portfolio_articles WHERE slug = ? AND status = 'published'");
$stmt->execute([$_GET['slug']]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: archive.php');
    exit;
}

// Increment view count
$db->prepare("UPDATE portfolio_articles SET view_count = view_count + 1 WHERE id = ?")->execute([$article['id']]);

// Get related articles
$stmt = $db->prepare("SELECT * FROM portfolio_articles WHERE category = ? AND id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$article['category'], $article['id']]);
$related = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($article['title']) ?> - HIVEMEDIA Portfolio</title>
    <meta name="description" content="<?= sanitize(mb_substr(strip_tags($article['content']), 0, 160)) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .article-page {
            min-height: 100vh;
            background: var(--black, #0a0a0a);
            padding: 8rem 0 5rem;
        }

        .article-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .article-header {
            margin-bottom: 4rem;
        }

        .article-category {
            font-size: 1rem;
            color: #94BDF7;
            margin-bottom: 1rem;
        }

        .article-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 2rem;
        }

        .article-meta {
            display: flex;
            gap: 2rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1rem;
        }

        .article-image {
            width: 100%;
            border-radius: 1.5rem;
            margin-bottom: 3rem;
            overflow: hidden;
        }

        .article-image img {
            width: 100%;
            display: block;
        }

        .article-content {
            font-size: 1.2rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.85);
        }

        .article-content p {
            margin-bottom: 1.5rem;
        }

        .article-tags {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .article-tag {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 3rem;
            color: #94BDF7;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .related-section {
            margin-top: 5rem;
            padding-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .related-title {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 2rem;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .related-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .related-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .related-card__title {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }

        .related-card__title:hover {
            color: #94BDF7;
        }

        .related-card__date {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>
    <article class="article-page">
        <div class="article-container">
            <header class="article-header">
                <div class="article-category"><?= sanitize($article['category'] ?? '프로젝트') ?></div>
                <h1 class="article-title"><?= sanitize($article['title']) ?></h1>
                <div class="article-meta">
                    <?php if ($article['client']): ?>
                        <span>👤 <?= sanitize($article['client']) ?></span>
                    <?php endif; ?>
                    <span>📅 <?= date('Y년 m월 d일', strtotime($article['created_at'])) ?></span>
                    <span>👁 <?= number_format($article['view_count']) ?> views</span>
                </div>
            </header>

            <?php if ($article['thumbnail']): ?>
                <div class="article-image">
                    <img src="<?= sanitize($article['thumbnail']) ?>" alt="<?= sanitize($article['title']) ?>">
                </div>
            <?php endif; ?>

            <div class="article-content">
                <?= nl2br(sanitize($article['content'])) ?>
            </div>

            <?php if ($article['tags']): ?>
                <div class="article-tags">
                    <?php foreach (explode(',', $article['tags']) as $tag): ?>
                        <span class="article-tag">#<?= sanitize(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="archive.php" class="back-link">← 목록으로 돌아가기</a>

            <?php if (!empty($related)): ?>
                <section class="related-section">
                    <h3 class="related-title">관련 프로젝트</h3>
                    <div class="related-grid">
                        <?php foreach ($related as $item): ?>
                            <div class="related-card">
                                <a href="article.php?slug=<?= urlencode($item['slug']) ?>" class="related-card__title"><?= sanitize($item['title']) ?></a>
                                <div class="related-card__date"><?= date('Y.m.d', strtotime($item['created_at'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </article>
</body>

</html>