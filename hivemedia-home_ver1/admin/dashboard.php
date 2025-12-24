<?php

/**
 * Admin Dashboard
 * HIVEMEDIA Portfolio System
 */
require_once 'config.php';
requireLogin();

$db = getDB();

// Get statistics
$totalArticles = $db->query("SELECT COUNT(*) FROM portfolio_articles")->fetchColumn();
$publishedArticles = $db->query("SELECT COUNT(*) FROM portfolio_articles WHERE status = 'published'")->fetchColumn();
$draftArticles = $db->query("SELECT COUNT(*) FROM portfolio_articles WHERE status = 'draft'")->fetchColumn();
$totalViews = $db->query("SELECT SUM(view_count) FROM portfolio_articles")->fetchColumn() ?: 0;

// Get recent articles
$recentArticles = $db->query("SELECT * FROM portfolio_articles ORDER BY created_at DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 대시보드 - HIVEMEDIA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter Tight', sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            color: #fff;
        }

        .header {
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .header h1 span {
            color: #94BDF7;
        }

        .header-nav {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .header-nav a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .header-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .header-nav .btn-write {
            background: #94BDF7;
            color: #000;
            font-weight: 600;
        }

        .header-nav .btn-write:hover {
            background: #fff;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 2rem;
        }

        .stat-card h3 {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 0.5rem;
        }

        .stat-card .number {
            font-size: 3rem;
            font-weight: 600;
        }

        .stat-card.blue .number {
            color: #94BDF7;
        }

        .stat-card.green .number {
            color: #6FCF97;
        }

        .stat-card.yellow .number {
            color: #F2C94C;
        }

        .stat-card.purple .number {
            color: #BB6BD9;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .articles-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            overflow: hidden;
        }

        .articles-table th,
        .articles-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .articles-table th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: 500;
            color: rgba(255, 255, 255, 0.6);
        }

        .articles-table tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.8rem;
        }

        .status-published {
            background: rgba(111, 207, 151, 0.2);
            color: #6FCF97;
        }

        .status-draft {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
        }

        .action-btn {
            color: #94BDF7;
            text-decoration: none;
            margin-right: 1rem;
        }

        .action-btn:hover {
            text-decoration: underline;
        }

        .action-btn.delete {
            color: #EB5757;
        }
    </style>
</head>

<body>
    <header class="header">
        <h1><span>HIVE</span>MEDIA Admin</h1>
        <nav class="header-nav">
            <a href="../portfolio.php" target="_blank">포트폴리오 보기</a>
            <a href="write.php" class="btn-write">+ 새 글쓰기</a>
            <a href="logout.php">로그아웃</a>
        </nav>
    </header>

    <div class="container">
        <div class="stats">
            <div class="stat-card blue">
                <h3>전체 글</h3>
                <div class="number"><?= $totalArticles ?></div>
            </div>
            <div class="stat-card green">
                <h3>발행됨</h3>
                <div class="number"><?= $publishedArticles ?></div>
            </div>
            <div class="stat-card yellow">
                <h3>임시저장</h3>
                <div class="number"><?= $draftArticles ?></div>
            </div>
            <div class="stat-card purple">
                <h3>총 조회수</h3>
                <div class="number"><?= number_format($totalViews) ?></div>
            </div>
        </div>

        <h2 class="section-title">최근 글</h2>
        <table class="articles-table">
            <thead>
                <tr>
                    <th>제목</th>
                    <th>카테고리</th>
                    <th>상태</th>
                    <th>조회</th>
                    <th>작성일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentArticles)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:3rem; color:rgba(255,255,255,0.5);">
                            아직 작성된 글이 없습니다. <a href="write.php" style="color:#94BDF7;">첫 글을 작성해보세요!</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentArticles as $article): ?>
                        <tr>
                            <td><?= sanitize($article['title']) ?></td>
                            <td><?= sanitize($article['category'] ?? '-') ?></td>
                            <td>
                                <span class="status-badge status-<?= $article['status'] ?>">
                                    <?= $article['status'] === 'published' ? '발행됨' : '임시저장' ?>
                                </span>
                            </td>
                            <td><?= number_format($article['view_count']) ?></td>
                            <td><?= date('Y-m-d', strtotime($article['created_at'])) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $article['id'] ?>" class="action-btn">수정</a>
                                <a href="delete.php?id=<?= $article['id'] ?>" class="action-btn delete" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>