/**
 * Lazy Loading Images with Intersection Observer API
 * 
 * Script ini mengimplementasikan lazy loading untuk gambar menggunakan
 * Intersection Observer API untuk performa yang lebih baik.
 */

class LazyLoader {
    constructor(options = {}) {
        this.options = {
            root: null, // viewport
            rootMargin: options.rootMargin || '50px',
            threshold: options.threshold || 0.1,
            ...options
        };

        this.observer = null;
        this.init();
    }

    /**
     * Inisialisasi Intersection Observer
     */
    init() {
        // Cek apakah browser mendukung Intersection Observer
        if (!('IntersectionObserver' in window)) {
            this.loadAllImages();
            return;
        }

        this.observer = new IntersectionObserver(
            this.handleIntersection.bind(this),
            this.options
        );

        this.observeImages();
    }

    /**
     * Observe semua gambar dengan data-src attribute
     */
    observeImages() {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => this.observer.observe(img));
    }

    /**
     * Handler ketika elemen masuk ke viewport
     */
    handleIntersection(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                this.loadImage(img);
                observer.unobserve(img);
            }
        });
    }

    /**
     * Load gambar dari data-src ke src
     */
    loadImage(img) {
        const src = img.getAttribute('data-src');
        const srcset = img.getAttribute('data-srcset');

        if (src) {
            // Tambahkan efek fade-in
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease-in-out';

            img.onload = () => {
                img.style.opacity = '1';
                img.removeAttribute('data-src');
            };

            img.onerror = () => {
                // Fallback ke placeholder jika gambar gagal load
                img.src = '/images/placeholder.jpg';
                img.style.opacity = '1';
            };

            img.src = src;

            if (srcset) {
                img.srcset = srcset;
                img.removeAttribute('data-srcset');
            }
        }
    }

    /**
     * Load semua gambar (fallback untuk browser yang tidak mendukung)
     */
    loadAllImages() {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => this.loadImage(img));
    }

    /**
     * Refresh observer untuk gambar baru yang ditambahkan secara dinamis
     */
    refresh() {
        if (this.observer) {
            this.observeImages();
        }
    }

    /**
     * Hancurkan observer
     */
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

// Inisialisasi lazy loader saat DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.lazyLoader = new LazyLoader({
        rootMargin: '100px', // Load gambar 100px sebelum masuk viewport
        threshold: 0.01 // Trigger ketika 1% elemen terlihat
    });
});

// Export untuk penggunaan di modul lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LazyLoader;
}
