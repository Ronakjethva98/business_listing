<?php
/**
 * Advertisement Display Component
 * Include this file on pages where you want to show approved advertisements
 * 
 * Usage: include "display_ads.php";
 */

// Include database connection if not already included
if (!isset($conn)) {
    include_once "db.php";
}

// Fetch approved advertisements (random selection for rotation)
$ad_sql = "SELECT * FROM advertisements WHERE status='approved' ORDER BY RAND() LIMIT 3";
$ad_result = mysqli_query($conn, $ad_sql);

if (mysqli_num_rows($ad_result) > 0):
?>
<style>
    .advertisement-section {
        margin: 30px 0;
        padding: 20px;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 12px;
        border: 2px dashed #9ca3af;
    }
    .advertisement-label {
        text-align: center;
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        font-weight: 600;
    }
    .ads-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    .ad-item {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .ad-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .ad-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .ad-content {
        padding: 16px;
    }
    .ad-title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
    }
    .ad-description {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        line-height: 1.5;
    }
    @media (max-width: 768px) {
        .ads-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="advertisement-section">
    <div class="advertisement-label">📢 Sponsored Advertisements</div>
    <div class="ads-container">
        <?php while ($ad = mysqli_fetch_assoc($ad_result)): ?>
            <a href="<?php echo !empty($ad['link_url']) ? htmlspecialchars($ad['link_url']) : '#'; ?>" 
               target="<?php echo !empty($ad['link_url']) ? '_blank' : '_self'; ?>" 
               class="ad-item"
               style="text-decoration: none;">
                <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($ad['title']); ?>">
                <div class="ad-content">
                    <h3 class="ad-title"><?php echo htmlspecialchars($ad['title']); ?></h3>
                    <?php if (!empty($ad['description'])): ?>
                        <p class="ad-description">
                            <?php 
                                $desc = htmlspecialchars($ad['description']);
                                echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<?php endif; ?>
