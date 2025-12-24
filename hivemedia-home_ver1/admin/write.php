<?php

/**
 * Write/Edit Article
 * HIVEMEDIA Portfolio System
 */
require_once 'config.php';
requireLogin();

$db = getDB();
$error = '';
$success = '';
$article = null;
$isEdit = false;

// Edit mode
if (isset($_GET['id'])) {
    $isEdit = true;
    $stmt = $db->prepare("SELECT * FROM portfolio_articles WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $article = $stmt->fetch();
    if (!$article) {
        header('Location: dashboard.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $client = trim($_POST['client'] ?? '');
    $project_date = $_POST['project_date'] ?? null;
    $tags = trim($_POST['tags'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $thumbnail = trim($_POST['thumbnail'] ?? '');

    if (empty($title)) {
        $error = '제목을 입력해주세요.';
    } else {
        $slug = generateSlug($title);

        if ($isEdit) {
            // Update
            $stmt = $db->prepare("UPDATE portfolio_articles SET 
                title = ?, slug = ?, content = ?, thumbnail = ?, category = ?, 
                client = ?, project_date = ?, tags = ?, status = ? 
                WHERE id = ?");
            $result = $stmt->execute([$title, $slug, $content, $thumbnail, $category, $client, $project_date, $tags, $status, $_GET['id']]);
        } else {
            // Insert
            $stmt = $db->prepare("INSERT INTO portfolio_articles 
                (title, slug, content, thumbnail, category, client, project_date, tags, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$title, $slug, $content, $thumbnail, $category, $client, $project_date, $tags, $status]);
        }

        if ($result) {
            $success = $isEdit ? '글이 수정되었습니다!' : '글이 저장되었습니다!';
            if (!$isEdit) {
                header('Location: dashboard.php');
                exit;
            }
        } else {
            $error = '저장 중 오류가 발생했습니다.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? '글 수정' : '새 글쓰기' ?> - HIVEMEDIA Admin</title>
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

        .header-nav a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin-left: 1rem;
        }

        .header-nav a:hover {
            color: #fff;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #94BDF7;
        }

        .form-group textarea {
            min-height: 300px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #94BDF7;
            color: #000;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #fff;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background: rgba(255, 100, 100, 0.1);
            border: 1px solid rgba(255, 100, 100, 0.3);
            color: #ff6464;
        }

        .alert-success {
            background: rgba(111, 207, 151, 0.1);
            border: 1px solid rgba(111, 207, 151, 0.3);
            color: #6FCF97;
        }
    </style>
</head>

<body>
    <header class="header">
        <h1><span>HIVE</span>MEDIA Admin</h1>
        <nav class="header-nav">
            <a href="dashboard.php">← 대시보드</a>
        </nav>
    </header>

    <div class="container">
        <h2 class="page-title"><?= $isEdit ? '글 수정' : '새 글쓰기' ?></h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= sanitize($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>제목 *</label>
                <input type="text" name="title" value="<?= sanitize($article['title'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>카테고리</label>
                    <select name="category">
                        <option value="">선택하세요</option>
                        <option value="브랜딩" <?= ($article['category'] ?? '') === '브랜딩' ? 'selected' : '' ?>>브랜딩</option>
                        <option value="마케팅" <?= ($article['category'] ?? '') === '마케팅' ? 'selected' : '' ?>>마케팅</option>
                        <option value="웹개발" <?= ($article['category'] ?? '') === '웹개발' ? 'selected' : '' ?>>웹개발</option>
                        <option value="영상" <?= ($article['category'] ?? '') === '영상' ? 'selected' : '' ?>>영상</option>
                        <option value="전시" <?= ($article['category'] ?? '') === '전시' ? 'selected' : '' ?>>전시</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>클라이언트</label>
                    <input type="text" name="client" value="<?= sanitize($article['client'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>프로젝트 날짜</label>
                    <input type="date" name="project_date" value="<?= $article['project_date'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>태그 (쉼표로 구분)</label>
                    <input type="text" name="tags" value="<?= sanitize($article['tags'] ?? '') ?>" placeholder="마케팅, 브랜딩, SNS">
                </div>
            </div>

            <div class="form-group">
                <label>썸네일 이미지 URL</label>
                <input type="text" name="thumbnail" value="<?= sanitize($article['thumbnail'] ?? '') ?>" placeholder="https://...">
            </div>

            <div class="form-group">
                <label>내용</label>
                <textarea name="content"><?= sanitize($article['content'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>상태</label>
                <select name="status">
                    <option value="draft" <?= ($article['status'] ?? '') === 'draft' ? 'selected' : '' ?>>임시저장</option>
                    <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>발행</option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? '수정하기' : '저장하기' ?></button>
                <a href="dashboard.php" class="btn btn-secondary">취소</a>
            </div>
        </form>
    </div>
</body>

</html>