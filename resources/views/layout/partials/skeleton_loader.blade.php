<div id="global-skeleton-loader">
    <div class="skeleton-header">
        <div class="sk-logo"></div>
        <div class="sk-nav">
            <div class="sk-link"></div>
            <div class="sk-link"></div>
            <div class="sk-link"></div>
            <div class="sk-link"></div>
        </div>
        <div class="sk-user"></div>
    </div>
    
    <div class="skeleton-hero">
        <div class="sk-hero-text">
            <div class="sk-title"></div>
            <div class="sk-subtitle"></div>
            <div class="sk-button"></div>
        </div>
        <div class="sk-hero-image"></div>
    </div>

    <div class="skeleton-grid">
        <div class="sk-card"></div>
        <div class="sk-card"></div>
        <div class="sk-card"></div>
        <div class="sk-card"></div>
    </div>
</div>

<style>
    #global-skeleton-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f8f9fa; /* Light background */
        z-index: 99999;
        display: flex;
        flex-direction: column;
        padding: 20px;
        box-sizing: border-box;
        overflow: hidden;
        transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
    }

    /* Hide class to be added via JS */
    #global-skeleton-loader.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    /* Shared skeleton style */
    .sk-logo, .sk-link, .sk-user, .sk-title, .sk-subtitle, .sk-button, .sk-hero-image, .sk-card {
        background: #e0e0e0;
        border-radius: 4px;
        position: relative;
        overflow: hidden;
    }

    /* Shimmer Effect */
    .sk-logo::after, .sk-link::after, .sk-user::after, .sk-title::after, .sk-subtitle::after, .sk-button::after, .sk-hero-image::after, .sk-card::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transform: translateX(-100%);
        background-image: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0,
            rgba(255, 255, 255, 0.2) 20%,
            rgba(255, 255, 255, 0.5) 60%,
            rgba(255, 255, 255, 0)
        );
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }

    /* Header Structure */
    .skeleton-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
        margin-bottom: 30px;
        flex-shrink: 0;
    }
    .sk-logo { width: 120px; height: 40px; }
    .sk-nav { display: flex; gap: 20px; flex: 1; justify-content: center; }
    .sk-link { width: 80px; height: 20px; }
    .sk-user { width: 40px; height: 40px; border-radius: 50%; }

    /* Hero Section */
    .skeleton-hero {
        display: flex;
        gap: 40px;
        margin-bottom: 40px;
        padding: 0 40px;
        align-items: center;
    }
    .sk-hero-text { flex: 1; display: flex; flex-direction: column; gap: 15px; }
    .sk-title { width: 70%; height: 40px; }
    .sk-subtitle { width: 90%; height: 20px; }
    .sk-button { width: 150px; height: 45px; margin-top: 10px; border-radius: 25px; }
    .sk-hero-image { width: 400px; height: 300px; flex-shrink: 0; }

    /* Grid Section */
    .skeleton-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 0 40px;
        flex: 1;
    }
    .sk-card {
        height: 200px;
        width: 100%;
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
        .sk-nav { display: none; }
        .skeleton-hero { flex-direction: column; padding: 0; }
        .sk-hero-image { width: 100%; height: 200px; }
        .sk-title { width: 90%; }
        .skeleton-grid { padding: 0; }
    }
</style>

<script>
    (function() {
        // Function to hide loader
        function hideSkeleton() {
            var loader = document.getElementById('global-skeleton-loader');
            if (loader) {
                loader.classList.add('hidden');
                // Remove from DOM after transition to avoid blocking clicks if z-index issue
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 500);
            }
        }

        // Hide on window load (all assets loaded)
        window.addEventListener('load', hideSkeleton);

        // Fallback: Max wait 3 seconds
        setTimeout(hideSkeleton, 3000);
    })();
</script>
