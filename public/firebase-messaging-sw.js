// Firebase Messaging Service Worker
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Firebase Configuration (من Firebase Console)
firebase.initializeApp({
    apiKey: "AIzaSyAKgu-SN0ztdBQ_GODbESu3WsZmXtAVexs",
    authDomain: "rent-sy-00.firebaseapp.com",
    projectId: "rent-sy-00",
    storageBucket: "rent-sy-00.firebasestorage.app",
    messagingSenderId: "422891128442",
    appId: "1:422891128442:web:a5bc0f8626a67922a51dc3",
    measurementId: "G-4S0EQMG44G"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('📩 Background message received:', payload);

    const notificationTitle = payload.notification?.title || 'إشعار جديد';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: '/firebase-logo.png',
        badge: '/firebase-logo.png',
        data: payload.data
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('🔔 Notification clicked:', event);
    event.notification.close();

    // Open the app
    event.waitUntil(
        clients.openWindow('http://localhost:8000/test-notifications')
    );
});
