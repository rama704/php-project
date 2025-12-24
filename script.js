// Slider Functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    if (n >= slides.length) currentSlide = 0;
    if (n < 0) currentSlide = slides.length - 1;
    
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}

function changeSlide(n) {
    currentSlide += n;
    showSlide(currentSlide);
}

function goToSlide(n) {
    currentSlide = n;
    showSlide(currentSlide);
}

// Auto slide every 5 seconds
setInterval(() => {
    currentSlide++;
    showSlide(currentSlide);
}, 5000);

// Auth Modal
const authModal = document.getElementById('authModal');
const userBtn = document.getElementById('userBtn');
const closeModal = document.querySelector('.close-modal');
const authTabs = document.querySelectorAll('.auth-tab');
const authForms = document.querySelectorAll('.auth-form');

userBtn.addEventListener('click', () => {
    authModal.classList.add('active');
});

closeModal.addEventListener('click', () => {
    authModal.classList.remove('active');
});

window.addEventListener('click', (e) => {
    if (e.target === authModal) {
        authModal.classList.remove('active');
    }
});

authTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const tabName = tab.getAttribute('data-tab');
        
        authTabs.forEach(t => t.classList.remove('active'));
        authForms.forEach(f => f.classList.remove('active'));
        
        tab.classList.add('active');
        document.getElementById(tabName + 'Form').classList.add('active');
    });
});

// Mobile Menu Toggle
const mobileToggle = document.getElementById('mobileToggle');
const navMenu = document.getElementById('navMenu');

mobileToggle.addEventListener('click', () => {
    if (navMenu.style.display === 'flex') {
        navMenu.style.display = 'none';
    } else {
        navMenu.style.display = 'flex';
        navMenu.style.position = 'absolute';
        navMenu.style.top = '100%';
        navMenu.style.left = '0';
        navMenu.style.width = '100%';
        navMenu.style.background = 'white';
        navMenu.style.flexDirection = 'column';
        navMenu.style.padding = '20px';
        navMenu.style.boxShadow = '0 5px 10px rgba(0,0,0,0.1)';
    }
});

// Product Tabs
const tabs = document.querySelectorAll('.tab');
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
    });
});

// Cart functionality
let cartCount = 0;
const cartBadge = document.querySelector('.cart-badge');

document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        cartCount++;
        cartBadge.textContent = cartCount;
    });
});

// Login Form Handler
document.getElementById('loginForm').addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Login functionality will be added with PHP backend');
    authModal.classList.remove('active');
});

// Register Form Handler
document.getElementById('registerForm').addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Registration functionality will be added with PHP backend');
    authModal.classList.remove('active');
});

// Search Button
document.querySelector('.search-btn').addEventListener('click', () => {
    alert('Search functionality coming soon!');
});