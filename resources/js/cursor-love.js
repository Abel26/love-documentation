/**
 * Cursor Love Effect with Gravity and Explosion
 * 
 * Creates love-love elements when cursor moves with gravity effect.
 * Love-love can be clicked to explode into particles.
 */

// ========================================
// KONFIGURASI EFEK LOVE-LOVE
// ========================================

const LOVE_CONFIG = {
    emoji: '❤️',
    minSize: 16,          // Ukuran minimum (px)
    maxSize: 32,          // Ukuran maksimum (px)
    gravity: 3,            // Kecepatan gravitasi
    rotation: 45,          // Rotasi maksimum (derajat)
    opacity: 0.8,          // Opasitas awal
    fadeDuration: 2000,    // Durasi fade out (ms)
    spawnRate: 3,          // Spawn rate (1 love per X moves)
    maxLifetime: 3000,     // Maksimum lifetime love-love (ms) - love-love akan hilang setelah 3 detik
    colors: ['#ff6b9d', '#c44569', '#ff8a80', '#ff5252', '#e91e63']
};

// ========================================
// KONFIGURASI EFEK LEDAKAN
// ========================================

const EXPLOSION_CONFIG = {
    particleCount: 15,     // Jumlah partikel per ledakan
    particleSize: 8,       // Ukuran partikel (px)
    explosionSpeed: 300,   // Kecepatan ledakan (px)
    explosionDuration: 800, // Durasi animasi ledakan (ms)
    particleColors: ['#ff6b9d', '#c44569', '#ff8a80', '#ff5252', '#e91e63']
};

// ========================================
// VARIABEL GLOBAL
// ========================================

let moveCounter = 0;
let activeLoves = new Set();
let activeParticles = new Set();

// ========================================
// FUNGSI UTAMA
// ========================================

/**
 * Inisialisasi efek love-love
 */
function initCursorLoveEffect() {
    // Event listener untuk mousemove
    document.addEventListener('mousemove', handleMouseMove);
    
    // Cleanup periodik untuk mencegah memory leak
    setInterval(cleanupElements, 5000);
}

/**
 * Handle event mousemove
 */
function handleMouseMove(e) {
    moveCounter++;
    
    // Cek spawn rate
    if (moveCounter % LOVE_CONFIG.spawnRate !== 0) {
        return;
    }
    
    // Buat love-love
    createLove(e.clientX, e.clientY);
}

/**
 * Membuat elemen love-love
 */
function createLove(x, y) {
    const love = document.createElement('div');
    love.className = 'cursor-love';
    love.textContent = LOVE_CONFIG.emoji;
    
    // Set posisi
    love.style.left = `${x}px`;
    love.style.top = `${y}px`;
    
    // Set ukuran acak
    const size = randomBetween(LOVE_CONFIG.minSize, LOVE_CONFIG.maxSize);
    love.style.fontSize = `${size}px`;
    
    // Set rotasi acak
    const rotation = randomBetween(-LOVE_CONFIG.rotation, LOVE_CONFIG.rotation);
    love.style.setProperty('--love-rotation', `${rotation}deg`);
    
    // Set opasitas
    love.style.setProperty('--love-opacity', LOVE_CONFIG.opacity);
    
    // Set durasi animasi berdasarkan posisi
    const distanceToBottom = window.innerHeight - y;
    const gravityDuration = (distanceToBottom / LOVE_CONFIG.gravity) * 1000;
    
    // Gunakan durasi yang lebih pendek antara gravitasi dan maxLifetime
    const duration = Math.min(gravityDuration, LOVE_CONFIG.maxLifetime);
    love.style.animationDuration = `${duration}ms`;
    
    // Tambahkan event listener untuk klik
    love.addEventListener('click', (e) => handleLoveClick(e, love));
    
    // Tambahkan ke DOM
    document.body.appendChild(love);
    activeLoves.add(love);
    
    // Hapus setelah durasi selesai (gravitasi atau maxLifetime)
    setTimeout(() => {
        removeLove(love);
    }, duration);
}

/**
 * Handle klik pada love-love
 */
function handleLoveClick(e, love) {
    e.stopPropagation();
    
    // Hentikan animasi gravitasi
    love.style.animation = 'none';
    
    // Dapatkan posisi love-love
    const rect = love.getBoundingClientRect();
    const x = rect.left + rect.width / 2;
    const y = rect.top + rect.height / 2;
    
    // Buat ledakan
    createExplosion(x, y);
    
    // Hapus love-love
    removeLove(love);
}

/**
 * Membuat efek ledakan
 */
function createExplosion(x, y) {
    for (let i = 0; i < EXPLOSION_CONFIG.particleCount; i++) {
        createParticle(x, y);
    }
}

/**
 * Membuat partikel ledakan
 */
function createParticle(x, y) {
    const particle = document.createElement('div');
    particle.className = 'explosion-particle';
    
    // Set posisi awal
    particle.style.left = `${x}px`;
    particle.style.top = `${y}px`;
    
    // Set ukuran
    particle.style.width = `${EXPLOSION_CONFIG.particleSize}px`;
    particle.style.height = `${EXPLOSION_CONFIG.particleSize}px`;
    
    // Set warna acak
    const color = EXPLOSION_CONFIG.particleColors[
        Math.floor(Math.random() * EXPLOSION_CONFIG.particleColors.length)
    ];
    particle.style.backgroundColor = color;
    
    // Hitung arah dan kecepatan
    const angle = (Math.PI * 2 * i) / EXPLOSION_CONFIG.particleCount;
    const distance = EXPLOSION_CONFIG.explosionSpeed * (0.5 + Math.random() * 0.5);
    const tx = Math.cos(angle) * distance;
    const ty = Math.sin(angle) * distance;
    
    // Set CSS variables untuk animasi
    particle.style.setProperty('--tx', `${tx}px`);
    particle.style.setProperty('--ty', `${ty}px`);
    
    // Tambahkan ke DOM
    document.body.appendChild(particle);
    activeParticles.add(particle);
    
    // Hapus setelah animasi selesai
    setTimeout(() => {
        removeParticle(particle);
    }, EXPLOSION_CONFIG.explosionDuration);
}

/**
 * Hapus love-love
 */
function removeLove(love) {
    if (activeLoves.has(love)) {
        activeLoves.delete(love);
        if (love.parentNode) {
            love.parentNode.removeChild(love);
        }
    }
}

/**
 * Hapus partikel
 */
function removeParticle(particle) {
    if (activeParticles.has(particle)) {
        activeParticles.delete(particle);
        if (particle.parentNode) {
            particle.parentNode.removeChild(particle);
        }
    }
}

/**
 * Cleanup elemen yang tersisa
 */
function cleanupElements() {
    // Cleanup love-love
    activeLoves.forEach(love => {
        if (!document.body.contains(love)) {
            activeLoves.delete(love);
        }
    });
    
    // Cleanup partikel
    activeParticles.forEach(particle => {
        if (!document.body.contains(particle)) {
            activeParticles.delete(particle);
        }
    });
}

/**
 * Generate angka acak antara min dan max
 */
function randomBetween(min, max) {
    return Math.random() * (max - min) + min;
}

// ========================================
// INISIALISASI
// ========================================

// Jalankan saat DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCursorLoveEffect);
} else {
    initCursorLoveEffect();
}
