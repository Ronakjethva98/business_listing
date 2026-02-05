<?php
/**
 * Advertisement Display Component - Rotating Ad System
 * Include this file on pages where you want to show approved advertisements
 * 
 * Usage: include "display_ads.php";
 */

// Include database connection if not already included
if (!isset($conn)) {
    include_once "db.php";
}

// Fetch approved and PAID advertisements within active date range
$ad_sql = "SELECT * FROM advertisements 
           WHERE status='approved' 
           AND is_paid=1 
           AND CURDATE() BETWEEN start_date AND end_date 
           ORDER BY RAND()";
$ad_result = mysqli_query($conn, $ad_sql);
$ads = [];
while ($ad = mysqli_fetch_assoc($ad_result)) {
    $ads[] = $ad;
}

if (count($ads) > 0):
?>
<style>
    .advertisement-section {
        margin: 48px 0 60px 0;
    }
    .advertisement-label {
        text-align: center;
        font-size: 13px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 28px;
        font-weight: 700;
    }
    
    /* ROTATING AD CONTAINER */
    .ad-rotator-container {
        position: relative;
        width: 100%;
        max-width: 850px; /* Decreased from 1000px */
        margin: 0 auto;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }
    
    /* KEYFRAME ANIMATIONS */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(-100%);
            opacity: 0;
        }
    }
    
    .ad-item {
        border-radius: 12px;
        overflow: hidden;
        display: block;
        position: absolute;
        width: 100%;
        opacity: 0;
        visibility: hidden;
        transform: translateX(100%);
    }
    
    .ad-item.active {
        opacity: 1;
        visibility: visible;
        position: relative;
        animation: slideIn 0.6s ease-out forwards;
        transform: translateX(0);
    }
    
    .ad-item.exiting {
        animation: slideOut 0.6s ease-in forwards;
    }

    

    
    .ad-image-container {
        position: relative;
        width: 100%;
        height: 350px; /* Decreased from 450px */
        overflow: hidden;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
    }

    /* BLURRED BACKGROUND FOR PREMIUM LOOK */
    .ad-image-blur {
        position: absolute;
        top: -20px;
        left: -20px;
        right: -20px;
        bottom: -20px;
        background-size: cover;
        background-position: center;
        filter: blur(20px) brightness(0.4);
        z-index: 1;
        opacity: 0.8;
    }
    
    .ad-item img {
        position: relative;
        z-index: 2;
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
        image-rendering: -webkit-optimize-contrast;
        filter: drop-shadow(0 10px 30px rgba(0,0,0,0.5));
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .ad-item.active img {
        animation: hd-entry 1s ease-out;
    }

    @keyframes hd-entry {
        0% { filter: brightness(1.1) contrast(1.1) blur(1px); }
        100% { filter: brightness(1.02) contrast(1.05) blur(0); }
    }
    
    /* COMPANY LOGO BADGE */
    .ad-company-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: rgba(255, 255, 255, 0.95);
        padding: 8px 16px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        backdrop-filter: blur(10px);
        z-index: 10; /* Added to stay on top */
    }
    
    .ad-company-icon {
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        color: white;
    }
    
    .ad-company-name {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
    }
    
    .ad-company-tagline {
        font-size: 11px;
        color: #6b7280;
        margin-top: -2px;
    }
    
    /* TRY NOW BUTTON */
    .ad-try-now {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(59, 130, 246, 0.95);
        color: white;
        padding: 14px 40px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        text-align: center;
        min-width: 140px;
    }
    
    .ad-try-now:hover {
        background: rgba(37, 99, 235, 1);
        transform: translateX(-50%) translateY(-4px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
    }
    
    .ad-content {
        display: none;
    }
    
    /* NAVIGATION DOTS */
    .ad-dots {
        text-align: center;
        margin-top: 20px;
        padding: 10px 0;
    }
    
    .ad-dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        margin: 0 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .ad-dot.active {
        background: #3b82f6;
        transform: scale(1.3);
    }
    
    .ad-dot:hover {
        background: #6b7280;
    }
    
    /* AD COUNTER */
    .ad-counter {
        text-align: center;
        font-size: 14px;
        color: #9ca3af;
        margin-top: 10px;
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .ad-image-container {
            height: 200px;
        }
        .ad-title {
            font-size: 20px;
        }
        .ad-description {
            font-size: 14px;
        }
        .ad-try-now {
            padding: 12px 30px;
            font-size: 14px;
            bottom: 20px;
        }
        .ad-company-badge {
            top: 15px;
            left: 15px;
            padding: 6px 12px;
        }
    }
</style>

<div class="advertisement-section">
    <div class="advertisement-label">Sponsored Advertisements</div>
    
    <div class="ad-rotator-container">
        <?php foreach ($ads as $index => $ad): ?>
            <div class="ad-item <?php echo $index === 0 ? 'active' : ''; ?>"
                 data-ad-index="<?php echo $index; ?>">
                <div class="ad-image-container">
                    <!-- Blurred background -->
                    <div class="ad-image-blur" style="background-image: url('<?php echo htmlspecialchars($ad['image_path']); ?>');"></div>
                    
                    <?php if (!empty($ad['link_url'])): ?>
                        <a href="<?php echo htmlspecialchars($ad['link_url']); ?>" target="_blank" 
                           style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; position: relative; z-index: 3; text-decoration: none;">
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; position: relative; z-index: 3;">
                    <?php endif; ?>
                        
                        <img src="<?php echo htmlspecialchars($ad['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($ad['title']); ?>">
                             
                    <?php if (!empty($ad['link_url'])): ?>
                        </a>
                    <?php else: ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- COMPANY BADGE -->
                    <div class="ad-company-badge">
                        <div class="ad-company-icon">AD</div>
                        <div>
                            <div class="ad-company-name"><?php echo htmlspecialchars($ad['title']); ?></div>
                            <?php if (!empty($ad['description'])): ?>
                                <div class="ad-company-tagline">
                                    <?php 
                                        $desc = htmlspecialchars($ad['description']);
                                        echo strlen($desc) > 40 ? substr($desc, 0, 40) . '...' : $desc;
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    

                </div>
                
                <div class="ad-content">
                    <h3 class="ad-title"><?php echo htmlspecialchars($ad['title']); ?></h3>
                    <?php if (!empty($ad['description'])): ?>
                        <p class="ad-description">
                            <?php 
                                $desc = htmlspecialchars($ad['description']);
                                echo strlen($desc) > 150 ? substr($desc, 0, 150) . '...' : $desc;
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- NAVIGATION DOTS -->
    <?php if (count($ads) > 1): ?>
    <div class="ad-dots">
        <?php foreach ($ads as $index => $ad): ?>
            <span class="ad-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                  data-dot-index="<?php echo $index; ?>"
                  onclick="changeAd(<?php echo $index; ?>)"></span>
        <?php endforeach; ?>
    </div>
    <div class="ad-counter">
        <span id="current-ad">1</span> / <?php echo count($ads); ?>
    </div>
    <?php endif; ?>
</div>

<script>
    // Ad Rotator JavaScript
    let currentAdIndex = 0;
    const totalAds = <?php echo count($ads); ?>;
    let autoRotateInterval;

    function changeAd(newIndex) {
        // Get current ad and dot
        const currentAd = document.querySelector('.ad-item.active');
        const currentDot = document.querySelector('.ad-dot.active');
        
        // Add exit animation to current ad
        if (currentAd) {
            currentAd.classList.add('exiting');
            
            // Wait for exit animation to complete before showing new ad
            setTimeout(() => {
                currentAd.classList.remove('active', 'exiting');
                
                // Update index
                currentAdIndex = newIndex;
                
                // Add active class to new ad
                const newAd = document.querySelector(`.ad-item[data-ad-index="${newIndex}"]`);
                const newDot = document.querySelector(`.ad-dot[data-dot-index="${newIndex}"]`);
                
                if (currentDot) currentDot.classList.remove('active');
                if (newAd) newAd.classList.add('active');
                if (newDot) newDot.classList.add('active');
                
                // Update counter
                const counterElement = document.getElementById('current-ad');
                if (counterElement) {
                    counterElement.textContent = newIndex + 1;
                }
            }, 300); // Half of animation duration for smoother overlap
        } else {
            // First load or no current ad
            currentAdIndex = newIndex;
            
            const newAd = document.querySelector(`.ad-item[data-ad-index="${newIndex}"]`);
            const newDot = document.querySelector(`.ad-dot[data-dot-index="${newIndex}"]`);
            
            if (currentDot) currentDot.classList.remove('active');
            if (newAd) newAd.classList.add('active');
            if (newDot) newDot.classList.add('active');
            
            const counterElement = document.getElementById('current-ad');
            if (counterElement) {
                counterElement.textContent = newIndex + 1;
            }
        }
        
        // Reset auto-rotation timer
        resetAutoRotate();
    }

    function nextAd() {
        const nextIndex = (currentAdIndex + 1) % totalAds;
        changeAd(nextIndex);
    }

    function startAutoRotate() {
        // Rotate every 10 seconds (2000 milliseconds)
        autoRotateInterval = setInterval(nextAd, 10000);
    }

    function resetAutoRotate() {
        clearInterval(autoRotateInterval);
        startAutoRotate();
    }

    // Start auto-rotation when page loads
    if (totalAds > 1) {
        startAutoRotate();
    }

    // Pause rotation when user hovers over ad
    document.querySelectorAll('.ad-item').forEach(ad => {
        ad.addEventListener('mouseenter', () => {
            clearInterval(autoRotateInterval);
        });
        
        ad.addEventListener('mouseleave', () => {
            if (totalAds > 1) {
                startAutoRotate();
            }
        });
    });
</script>

<?php endif; ?>
