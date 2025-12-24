-- ======================================
-- HIVEMEDIA PORTFOLIO DATABASE SETUP
-- ======================================

-- Create database (run this first in phpMyAdmin)
CREATE DATABASE IF NOT EXISTS hivemedia_portfolio DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE hivemedia_portfolio;

-- ======================================
-- PORTFOLIO ARTICLES TABLE
-- ======================================
CREATE TABLE IF NOT EXISTS portfolio_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    thumbnail VARCHAR(500),
    category VARCHAR(100),
    client VARCHAR(255),
    project_date DATE,
    tags VARCHAR(500),
    view_count INT DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======================================
-- ADMIN USERS TABLE
-- ======================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hivemedia.co.kr');

-- ======================================
-- SAMPLE DATA
-- ======================================
INSERT INTO portfolio_articles (title, slug, content, category, client, project_date, tags, status) VALUES
('브랜드 리뉴얼 프로젝트', 'brand-renewal-project', '클라이언트의 브랜드 아이덴티티를 현대적으로 재해석한 프로젝트입니다.', '브랜딩', '(주)샘플컴퍼니', '2024-01-15', '브랜딩,로고,디자인', 'published'),
('SNS 마케팅 캠페인', 'sns-marketing-campaign', '인스타그램과 페이스북을 활용한 통합 마케팅 캠페인을 진행했습니다.', '마케팅', '패션브랜드A', '2024-02-20', '마케팅,SNS,광고', 'published'),
('기업 홈페이지 제작', 'corporate-website', '반응형 웹 디자인과 SEO 최적화를 적용한 기업 홈페이지입니다.', '웹개발', 'IT스타트업B', '2024-03-10', '웹개발,반응형,SEO', 'published');
