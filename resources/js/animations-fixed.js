/**
 * Simplified Animation System for Blogger Website
 * Fixed version - Does not interfere with normal functionality
 */

class BloggerAnimations {
    constructor() {
        this.pageTransition = document.getElementById('pageTransition');
        this.loadingTimeout = null;
        this.init();
    }

    init() {
        this.setupInitialPageLoad();
        this.setupMinimalLoading();
        this.setupSmoothScroll();
        this.setupRevealAnimations();
        this.removeInitialTransition();
        console.log('✅ Blogger Animations initialized (safe mode)');
    }

    // Minimal loading - only for form submissions
    setupMinimalLoading() {
        // Only handle form submissions for loading animation
        document.addEventListener('submit', (e) => {
            const form = e.target;
            const method = form.getAttribute('method') || 'GET';
            
            // Only show loading for non-GET methods (actual server changes)
            if (method.toLowerCase() !== 'get') {
                const action = form.getAttribute('action') || '';
                let loadingText = 'Processing...';
                
                if (action.includes('login')) {
                    loadingText = 'Logging in...';
                } else if (action.includes('store')) {
                    loadingText = 'Creating...';
                } else if (action.includes('update')) {
                    loadingText = 'Updating...';
                } else if (action.includes('destroy')) {
                    loadingText = 'Deleting...';
                }
                
                this.showLoadingBrief(loadingText, 2000);
            }
        });
    }

    // Brief loading display
    showLoadingBrief(message = 'Processing...', duration = 1000) {
        if (!this.pageTransition) return;
        
        // Clear any existing timeout
        if (this.loadingTimeout) {
            clearTimeout(this.loadingTimeout);
        }
        
        // Show loading
        this.pageTransition.style.display = 'flex';
        this.pageTransition.style.opacity = '1';
        this.pageTransition.style.visibility = 'visible';
        this.pageTransition.style.pointerEvents = 'all';
        this.pageTransition.classList.add('active');
        
        const textEl = this.pageTransition.querySelector('.loading-text');
        if (textEl) {
            textEl.textContent = message;
        }
        
        // Auto-hide after duration
        this.loadingTimeout = setTimeout(() => {
            this.hideLoading();
        }, Math.min(duration, 3000)); // Max 3 seconds
    }

    hideLoading() {
        if (!this.pageTransition) return;
        
        this.pageTransition.style.opacity = '0';
        this.pageTransition.style.visibility = 'hidden';
        this.pageTransition.style.pointerEvents = 'none';
        this.pageTransition.classList.remove('active');
        
        setTimeout(() => {
            if (this.pageTransition) {
                this.pageTransition.style.display = 'none';
            }
        }, 300);
        
        if (this.loadingTimeout) {
            clearTimeout(this.loadingTimeout);
            this.loadingTimeout = null;
        }
    }

    // Setup loading on initial page load
    setupInitialPageLoad() {
        // Hide loading when page is fully loaded
        window.addEventListener('load', () => {
            setTimeout(() => {
                this.hideLoading();
            }, 100);
        });
        
        // Also handle DOMContentLoaded
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                this.hideLoading();
            }, 50);
        });
    }

    removeInitialTransition() {
        // Aggressively hide loading at multiple intervals
        const attempts = [50, 100, 200, 500, 1000];
        attempts.forEach(delay => {
            setTimeout(() => {
                this.hideLoading();
                
                // Hide any other loading elements
                const initialLoading = document.getElementById('initialLoading');
                if (initialLoading) {
                    initialLoading.style.opacity = '0';
                    initialLoading.style.visibility = 'hidden';
                    initialLoading.style.pointerEvents = 'none';
                    setTimeout(() => {
                        if (initialLoading.parentNode) {
                            initialLoading.parentNode.removeChild(initialLoading);
                        }
                    }, 100);
                }
            }, delay);
        });
    }

    // Smooth scroll functionality
    setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Simple reveal animations (non-blocking)
    setupRevealAnimations() {
        // Only apply gentle fade-in to specific elements that need it
        const elementsToAnimate = document.querySelectorAll('.animate-on-scroll');
        
        if (elementsToAnimate.length > 0) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            elementsToAnimate.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        }
    }
}

// Initialize animations when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        try {
            new BloggerAnimations();
        } catch (error) {
            console.error('❌ Error initializing Blogger Animations:', error);
        }
    }, 100);
});

// Make functions globally accessible for compatibility
window.BloggerAnimations = BloggerAnimations;