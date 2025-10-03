/**
 * Global Loading Fix
 * Mengatasi masalah loading animation yang stuck atau terus muncul
 */

// Global loading control object
window.LoadingControl = {
    isLoading: false,
    timeout: null,
    element: null,
    
    init() {
        this.element = document.getElementById('pageTransition');
        this.setupGlobalControls();
        this.setupEmergencyHide();
        console.log('Loading Control initialized');
    },
    
    show(message = 'Loading...', duration = 1000) {
        if (this.isLoading) {
            console.log('Loading already active, skipping...');
            return;
        }
        
        if (!this.element) {
            console.warn('Loading element not found');
            return;
        }
        
        this.isLoading = true;
        this.element.style.display = 'flex';
        this.element.style.opacity = '1';
        this.element.style.visibility = 'visible';
        this.element.style.pointerEvents = 'all';
        this.element.classList.add('active');
        
        const textEl = this.element.querySelector('.loading-text');
        if (textEl) {
            textEl.textContent = message;
        }
        
        // Clear any existing timeout
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
        
        // Auto-hide after duration
        this.timeout = setTimeout(() => {
            this.hide();
        }, Math.min(duration, 2000)); // Max 2 seconds
        
        console.log(`Loading shown: ${message} (${duration}ms)`);
    },
    
    hide() {
        if (!this.element) return;
        
        this.isLoading = false;
        this.element.style.opacity = '0';
        this.element.style.visibility = 'hidden';
        this.element.style.pointerEvents = 'none';
        this.element.classList.remove('active');
        
        setTimeout(() => {
            if (this.element) {
                this.element.style.display = 'none';
            }
        }, 300);
        
        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }
        
        console.log('Loading hidden');
    },
    
    forceHide() {
        console.log('Force hiding all loading elements...');
        
        // Hide main loading
        this.hide();
        
        // Hide any other loading elements
        const allLoadings = document.querySelectorAll(
            '[id*="Loading"], [id*="Transition"], .page-transition, [class*="loading"]'
        );
        
        allLoadings.forEach(el => {
            if (el) {
                el.style.opacity = '0';
                el.style.visibility = 'hidden';
                el.style.pointerEvents = 'none';
                el.classList.remove('active');
                setTimeout(() => {
                    el.style.display = 'none';
                }, 100);
            }
        });
        
        // Reset body styles
        document.body.style.pointerEvents = 'auto';
        document.body.style.overflow = 'auto';
        
        this.isLoading = false;
        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }
    },
    
    setupGlobalControls() {
        // Make functions globally accessible
        window.showLoading = (message, duration) => this.show(message, duration);
        window.hideLoading = () => this.hide();
        window.forceHideLoading = () => this.forceHide();
        
        // Auto-hide on page load
        window.addEventListener('load', () => {
            setTimeout(() => this.forceHide(), 300);
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => this.forceHide(), 100);
        });
    },
    
    setupEmergencyHide() {
        // Escape key to force hide
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.forceHide();
            }
        });
        
        // Click 4 times anywhere to force hide (emergency)
        let clickCount = 0;
        document.addEventListener('click', () => {
            clickCount++;
            if (clickCount >= 4) {
                this.forceHide();
                clickCount = 0;
            }
        });
        
        // Reset click count every 3 seconds
        setInterval(() => {
            clickCount = 0;
        }, 3000);
        
        // Auto force-hide any stuck loading every 10 seconds
        setInterval(() => {
            if (this.isLoading) {
                console.warn('Loading has been active too long, force hiding...');
                this.forceHide();
            }
        }, 10000);
        
        // Force hide on visibility change (tab switch)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                // Coming back to tab, hide any stuck loading
                setTimeout(() => this.forceHide(), 100);
            }
        });
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => LoadingControl.init(), 100);
    });
} else {
    setTimeout(() => LoadingControl.init(), 100);
}

// Also initialize on window load as backup
window.addEventListener('load', () => {
    if (!window.LoadingControl.element) {
        setTimeout(() => LoadingControl.init(), 100);
    }
});