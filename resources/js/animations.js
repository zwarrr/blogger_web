/**
 * Enhanced Animation System for Blogger Website
 * Provides smooth page transitions, section reveals, and interactive animations
 */

class BloggerAnimations {
    constructor() {
        this.pageTransition = document.getElementById('pageTransition');
        this.init();
    }

    init() {
        this.setupInitialPageLoad();
        this.setupPageUnload();
        this.setupPageTransitions();
        this.setupCRUDOperations();
        this.setupSmoothScroll();
        this.setupRevealAnimations();
        this.setupStaggerAnimations();
        this.setupRippleEffects();
        this.setupParallaxEffects();
        this.removeInitialTransition();
    }

    // Page transition handler - Intercept ALL internal links
    setupPageTransitions() {
        // Intercept ALL links, not just those with page-link class
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href');
            
            // Skip animation for external links, anchors, mailto, or tel
            if (!href || 
                href.startsWith('http://') || 
                href.startsWith('https://') || 
                href.startsWith('#') || 
                href.startsWith('mailto:') || 
                href.startsWith('tel:') ||
                href.startsWith('javascript:') ||
                link.hasAttribute('download') ||
                link.getAttribute('target') === '_blank') {
                return;
            }
            
            // Prevent default navigation
            e.preventDefault();
            
            // Determine loading text based on link context
            let loadingText = 'Memuat halaman...';
            const linkText = link.textContent.trim().toLowerCase();
            const linkHref = href.toLowerCase();
            
            if (linkText.includes('baca') || linkText.includes('read') || linkHref.includes('post')) {
                loadingText = 'Memuat artikel...';
            } else if (linkText.includes('home') || linkText.includes('beranda') || linkHref === '/' || linkHref.includes('home')) {
                loadingText = 'Kembali ke beranda...';
            } else if (linkText.includes('detail') || linkHref.includes('detail')) {
                loadingText = 'Memuat detail...';
            } else if (linkText.includes('kategori') || linkText.includes('category')) {
                loadingText = 'Memuat kategori...';
            } else if (linkText.includes('search') || linkText.includes('cari')) {
                loadingText = 'Mencari...';
            }
            
            // Show loading and navigate with faster duration
            this.showTransition(() => {
                window.location.href = href;
            }, loadingText, 400);
        });
        
        // Also intercept browser back/forward buttons
        window.addEventListener('popstate', () => {
            this.showTransition(() => {
                // Page will reload automatically
            }, 'Memuat halaman...', 300);
        });
    }

    showTransition(callback, loadingText = 'Memuat halaman...', duration = 500) {
        if (this.pageTransition) {
            // Clear any existing timeout to prevent stuck loading
            if (this.loadingTimeout) {
                clearTimeout(this.loadingTimeout);
            }
            
            // Force show loading overlay with display block
            this.pageTransition.style.display = 'flex';
            this.pageTransition.style.opacity = '1';
            this.pageTransition.style.visibility = 'visible';
            this.pageTransition.style.pointerEvents = 'all';
            this.pageTransition.classList.add('active');
            
            // Update loading text
            const loadingTextElement = this.pageTransition.querySelector('.loading-text');
            if (loadingTextElement) {
                loadingTextElement.textContent = loadingText;
            }
            
            if (window.loadingDebug) {
                console.log('Loading started:', loadingText, `(${duration}ms)`);
            }
            
            // Set loading timeout with shorter duration to prevent stuck
            const actualDuration = Math.min(duration, 2000); // Max 2 seconds
            this.loadingTimeout = setTimeout(() => {
                try {
                    callback();
                } catch (error) {
                    console.error('Loading callback error:', error);
                    this.hideLoading(); // Auto-hide if callback fails
                }
            }, actualDuration);
            
            // Aggressive safety timeout (3 seconds max)
            setTimeout(() => {
                if (this.pageTransition.classList.contains('active')) {
                    if (window.loadingDebug) {
                        console.warn('Loading stuck detected, force hiding...');
                    }
                    this.hideLoading();
                }
            }, 3000);
        } else {
            if (window.loadingDebug) {
                console.warn('Page transition element not found!');
            }
            callback();
        }
    }

    removeInitialTransition() {
        // Multiple attempts to hide loading
        const attempts = [100, 300, 500, 1000];
        attempts.forEach(delay => {
            setTimeout(() => {
                this.hideLoading();
                // Also hide any other loading elements
                const initialLoading = document.getElementById('initialLoading');
                if (initialLoading) {
                    initialLoading.style.opacity = '0';
                    setTimeout(() => {
                        if (initialLoading.parentNode) {
                            initialLoading.parentNode.removeChild(initialLoading);
                        }
                    }, 300);
                }
            }, delay);
        });
    }
    
    hideLoading() {
        // Hide main transition overlay
        if (this.pageTransition) {
            this.pageTransition.classList.remove('active');
            this.pageTransition.style.opacity = '0';
            this.pageTransition.style.visibility = 'hidden';
            this.pageTransition.style.pointerEvents = 'none';
            this.pageTransition.style.display = 'none'; // Force hide
            
            if (window.loadingDebug) {
                console.log('Main loading hidden');
            }
        }
        
        // Hide any duplicate or stuck loading elements
        const allLoadings = document.querySelectorAll('[id*=\"Loading\"], [id*=\"Transition\"], .page-transition');
        allLoadings.forEach(loading => {
            if (loading && loading !== this.pageTransition) {
                loading.style.opacity = '0';
                loading.style.visibility = 'hidden';
                loading.style.pointerEvents = 'none';
                loading.classList.remove('active');
            }
        });
        
        // Clear timeout to prevent memory leaks
        if (this.loadingTimeout) {
            clearTimeout(this.loadingTimeout);
            this.loadingTimeout = null;
        }
        
        // Enable page interactions
        document.body.style.pointerEvents = 'auto';
        document.body.style.overflow = 'auto';
    }
    
    // Setup loading saat initial page load
    setupInitialPageLoad() {
        // Show loading immediately when page starts loading
        if (document.readyState === 'loading') {
            this.showLoadingImmediately('Memuat halaman...');
        }
        
        // Hide loading when page is fully loaded
        window.addEventListener('load', () => {
            setTimeout(() => {
                this.hideLoading();
            }, 200);
        });
        
        // Also handle DOMContentLoaded for faster hiding
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                this.hideLoading();
            }, 100);
        });
    }
    
    // Setup loading saat keluar halaman
    setupPageUnload() {
        // Show loading when user is leaving the page (only for actual navigation)
        window.addEventListener('beforeunload', (e) => {
            // Only show if user is actually navigating away, not just refreshing or closing tab
            if (e.returnValue !== undefined) {
                this.showLoadingImmediately('Keluar dari halaman...');
            }
        });
        
        // Remove visibilitychange loading - no more "beralih tab" loading
        // This was causing unwanted loading when switching tabs or minimizing browser
    }
    
    // Show loading immediately without timeout
    showLoadingImmediately(loadingText = 'Loading...') {
        if (this.pageTransition) {
            this.pageTransition.style.opacity = '1';
            this.pageTransition.style.visibility = 'visible';
            this.pageTransition.style.pointerEvents = 'all';
            this.pageTransition.classList.add('active');
            
            const loadingTextElement = this.pageTransition.querySelector('.loading-text');
            if (loadingTextElement) {
                loadingTextElement.textContent = loadingText;
            }
        }
    }
    
    // Setup CRUD operations loading
    setupCRUDOperations() {
        // Intercept all buttons for CRUD operations
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button');
            if (!button) return;
            
            const buttonText = button.textContent.trim().toLowerCase();
            const buttonClass = button.className.toLowerCase();
            const form = button.closest('form');
            
            let loadingText = 'Memproses...';
            let duration = 600;
            
            // Determine loading message based on button context
            if (buttonText.includes('simpan') || buttonText.includes('save')) {
                loadingText = 'Menyimpan data...';
                duration = 800;
            } else if (buttonText.includes('hapus') || buttonText.includes('delete')) {
                loadingText = 'Menghapus data...';
                duration = 600;
            } else if (buttonText.includes('edit') || buttonText.includes('update')) {
                loadingText = 'Memperbarui data...';
                duration = 700;
            } else if (buttonText.includes('tambah') || buttonText.includes('create') || buttonText.includes('add')) {
                loadingText = 'Menambah data...';
                duration = 700;
            } else if (buttonText.includes('login') || buttonText.includes('masuk')) {
                loadingText = 'Masuk ke sistem...';
                duration = 800;
            } else if (buttonText.includes('register') || buttonText.includes('daftar')) {
                loadingText = 'Mendaftarkan akun...';
                duration = 900;
            } else if (buttonText.includes('search') || buttonText.includes('cari')) {
                loadingText = 'Mencari data...';
                duration = 500;
            } else if (buttonText.includes('kirim') || buttonText.includes('submit')) {
                loadingText = 'Mengirim data...';
                duration = 600;
            } else if (buttonText.includes('upload')) {
                loadingText = 'Mengunggah file...';
                duration = 1000;
            } else if (buttonClass.includes('btn-danger') || buttonClass.includes('delete')) {
                loadingText = 'Menghapus...';
                duration = 500;
            } else if (buttonClass.includes('btn-success') || buttonClass.includes('save')) {
                loadingText = 'Menyimpan...';
                duration = 600;
            }
            
            // Show loading for form buttons
            if (form && button.type === 'submit') {
                this.showTransition(() => {
                    // Let form submit naturally
                }, loadingText, duration);
            }
            // Show loading for AJAX buttons (data-* attributes)
            else if (button.hasAttribute('data-action') || button.hasAttribute('data-url')) {
                this.showTransition(() => {
                    // Let button action proceed
                }, loadingText, duration);
            }
        });
        
        // Handle form submissions more specifically
        document.addEventListener('submit', (e) => {
            const form = e.target;
            const action = form.getAttribute('action') || '';
            const method = form.getAttribute('method') || 'GET';
            
            let loadingText = 'Mengirim data...';
            let duration = 700;
            
            if (action.includes('login')) {
                loadingText = 'Masuk ke sistem...';
                duration = 800;
            } else if (action.includes('register')) {
                loadingText = 'Mendaftarkan akun...';
                duration = 900;
            } else if (action.includes('comment')) {
                loadingText = 'Mengirim komentar...';
                duration = 600;
            } else if (action.includes('search')) {
                loadingText = 'Mencari...';
                duration = 400;
            } else if (method.toLowerCase() === 'post') {
                loadingText = 'Menyimpan data...';
                duration = 600;
            } else if (method.toLowerCase() === 'put' || method.toLowerCase() === 'patch') {
                loadingText = 'Memperbarui data...';
                duration = 600;
            } else if (method.toLowerCase() === 'delete') {
                loadingText = 'Menghapus data...';
                duration = 500;
            }
            
            this.showTransition(() => {
                // Let form submit naturally
            }, loadingText, duration);
        });
    }

    // Show loading for specific actions
    showLoadingForAction(action, callback) {
        const loadingMessages = {
            'form-submit': 'Mengirim data...',
            'ajax-request': 'Memuat data...',
            'file-upload': 'Mengunggah file...',
            'search': 'Mencari...',
            'login': 'Masuk ke sistem...',
            'logout': 'Keluar dari sistem...',
            'save': 'Menyimpan...',
            'delete': 'Menghapus...',
            'update': 'Memperbarui...',
            'default': 'Memproses...'
        };

        const message = loadingMessages[action] || loadingMessages['default'];
        this.showTransition(callback, message);
    }

    // Global loading functions for external use
    showLoading(message = 'Loading...') {
        if (this.pageTransition) {
            this.pageTransition.classList.add('active');
            const loadingTextElement = this.pageTransition.querySelector('.loading-text');
            if (loadingTextElement) {
                loadingTextElement.textContent = message;
            }
        }
    }

    hideLoading() {
        if (this.pageTransition) {
            this.pageTransition.classList.remove('active');
        }
    }

    // Setup form loading
    setupFormLoading() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                const action = form.getAttribute('action') || '';
                let loadingText = 'Mengirim data...';

                if (action.includes('login')) {
                    loadingText = 'Masuk ke sistem...';
                } else if (action.includes('register')) {
                    loadingText = 'Mendaftarkan akun...';
                } else if (action.includes('comment')) {
                    loadingText = 'Mengirim komentar...';
                } else if (action.includes('search')) {
                    loadingText = 'Mencari...';
                }

                this.showLoading(loadingText);
                
                // Hide loading after form submission (in case of errors)
                setTimeout(() => {
                    this.hideLoading();
                }, 5000);
            });
        });
    }

    // Setup AJAX loading
    setupAjaxLoading() {
        // Override fetch for automatic loading
        const originalFetch = window.fetch;
        const self = this;

        window.fetch = function(...args) {
            self.showLoading('Memuat data...');
            
            return originalFetch.apply(this, args)
                .then(response => {
                    self.hideLoading();
                    return response;
                })
                .catch(error => {
                    self.hideLoading();
                    throw error;
                });
        };

        // Override XMLHttpRequest for automatic loading
        const originalXHROpen = XMLHttpRequest.prototype.open;
        const originalXHRSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
            this._method = method;
            this._url = url;
            return originalXHROpen.apply(this, arguments);
        };

        XMLHttpRequest.prototype.send = function(data) {
            const xhr = this;
            
            xhr.addEventListener('loadstart', () => {
                self.showLoading('Memuat data...');
            });

            xhr.addEventListener('loadend', () => {
                self.hideLoading();
            });

            return originalXHRSend.apply(this, arguments);
        };
    }

    // Smooth scroll for anchor links
    setupSmoothScroll() {
        document.querySelectorAll('.smooth-scroll').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    
                    if (target) {
                        const headerOffset = 80;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    }

    // Intersection Observer for reveal animations
    setupRevealAnimations() {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    
                    // Add stagger effect to children if they exist
                    const children = entry.target.querySelectorAll('.stagger-child');
                    children.forEach((child, index) => {
                        setTimeout(() => {
                            child.classList.add('revealed');
                        }, index * 100);
                    });
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        document.querySelectorAll('.reveal').forEach(el => {
            revealObserver.observe(el);
        });
    }

    // Stagger animation for card grids
    setupStaggerAnimations() {
        const staggerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const staggerItems = entry.target.querySelectorAll('.stagger-item');
                    
                    staggerItems.forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('revealed');
                        }, index * 150);
                    });
                    
                    staggerObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        document.querySelectorAll('.stagger-container').forEach(container => {
            staggerObserver.observe(container);
        });
    }

    // Ripple effect for buttons
    setupRippleEffects() {
        document.querySelectorAll('.ripple').forEach(button => {
            button.addEventListener('click', (e) => {
                const ripple = document.createElement('span');
                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.6);
                    transform: scale(0);
                    animation: ripple-effect 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    pointer-events: none;
                `;
                
                button.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    }

    // Parallax effects for background elements
    setupParallaxEffects() {
        let ticking = false;
        
        const updateParallax = () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax');
            
            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
            
            ticking = false;
        };
        
        const requestParallaxUpdate = () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        };
        
        window.addEventListener('scroll', requestParallaxUpdate);
    }

    // Utility method to add CSS animations dynamically
    addAnimationCSS() {
        if (document.getElementById('blogger-animations-css')) return;
        
        const style = document.createElement('style');
        style.id = 'blogger-animations-css';
        style.textContent = `
            @keyframes ripple-effect {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .slide-in-up {
                animation: slideInUp 0.6s ease-out forwards;
            }
            
            .slide-in-left {
                animation: slideInLeft 0.6s ease-out forwards;
            }
            
            .slide-in-right {
                animation: slideInRight 0.6s ease-out forwards;
            }
        `;
        document.head.appendChild(style);
    }

    // Method to trigger custom animations
    triggerAnimation(element, animationType = 'slideInUp') {
        if (!element) return;
        
        element.classList.add(animationType);
        
        // Remove animation class after completion
        element.addEventListener('animationend', () => {
            element.classList.remove(animationType);
        }, { once: true });
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Small delay to avoid conflicts with initial loading
    setTimeout(() => {
        try {
            const bloggerAnimations = new BloggerAnimations();
            bloggerAnimations.addAnimationCSS();
            
            // Make it globally accessible
            window.BloggerAnimations = bloggerAnimations;
            
            // Setup form and AJAX loading
            bloggerAnimations.setupFormLoading();
            bloggerAnimations.setupAjaxLoading();
            
            if (window.loadingDebug) {
                console.log('✅ Blogger Animations initialized successfully');
            }
            
            // Force hide any remaining initial loading
            setTimeout(() => {
                const initialLoading = document.getElementById('initialLoading');
                if (initialLoading) {
                    initialLoading.remove();
                }
                // Ensure main transition is hidden
                bloggerAnimations.hideLoading();
            }, 500);
            
        } catch (error) {
            console.error('❌ Error initializing Blogger Animations:', error);
            // Force hide loading even if initialization fails
            if (window.forceHideLoading) {
                window.forceHideLoading();
            }
        }
    }, 200);
});

// Loading performance optimization
const preloadAnimations = () => {
    // Preload critical animation assets
    const criticalCSS = document.createElement('link');
    criticalCSS.rel = 'preload';
    criticalCSS.as = 'style';
    criticalCSS.href = '/css/animations.css';
    document.head.appendChild(criticalCSS);
};

// Initialize preloading
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', preloadAnimations);
} else {
    preloadAnimations();
}