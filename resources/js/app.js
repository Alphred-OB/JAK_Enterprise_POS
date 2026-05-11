import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// PWA Installation Logic
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    // Notify Alpine or other listeners that the app can be installed
    window.dispatchEvent(new CustomEvent('pwa-installable', { detail: true }));
});

window.installPWA = async function() {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            console.log('User accepted the PWA prompt');
        }
        deferredPrompt = null;
        window.dispatchEvent(new CustomEvent('pwa-installable', { detail: false }));
    } else {
        alert("The app is already installed or your browser doesn't support automatic installation. Try installing from your browser menu (e.g., 'Add to Home Screen').");
    }
};

// Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then(registration => {
            console.log('ServiceWorker registration successful');
        }).catch(err => {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}
