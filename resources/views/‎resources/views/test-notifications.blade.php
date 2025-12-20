<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار Firebase Notifications - RentSY</title>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }

        .header h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2em;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .card h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.5em;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }

        .btn-info {
            background: #3b82f6;
            color: white;
        }

        .btn-info:hover {
            background: #2563eb;
        }

        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }

        .result.success {
            background: #d1fae5;
            border: 2px solid #10b981;
            color: #065f46;
            display: block;
        }

        .result.error {
            background: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
            display: block;
        }

        .result pre {
            margin-top: 10px;
            background: rgba(0,0,0,0.1);
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .info-box {
            background: #dbeafe;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            color: #1e40af;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #1e3a8a;
            margin-bottom: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-badge.online {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.offline {
            background: #fee2e2;
            color: #991b1b;
        }

        .notifications-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .notification-item.unread {
            background: #dbeafe;
            border-color: #3b82f6;
        }

        .notification-item h4 {
            color: #1f2937;
            margin-bottom: 5px;
        }

        .notification-item p {
            color: #6b7280;
            font-size: 14px;
        }

        .notification-item small {
            color: #9ca3af;
            font-size: 12px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .user-card {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .user-card.selected {
            border-color: #667eea;
            background: #dbeafe;
        }

        .user-card h4 {
            color: #1f2937;
            margin-bottom: 5px;
        }

        .user-card p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 3px;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔥 اختبار Firebase Notifications</h1>
            <p>RentSY Backend Testing Interface</p>
            <div style="margin-top: 15px;">
                <span id="firebaseStatus" class="status-badge offline">⚠️ غير متصل</span>
                <span id="authStatus" class="status-badge offline">🔒 غير مسجل</span>
            </div>
        </div>

        <div class="main-content">
            <!-- Login Card -->
            <div class="card">
                <h2>🔐 تسجيل الدخول</h2>

                <div class="info-box">
                    <h3>👥 المستخدمين التجريبيين:</h3>
                    <p><strong>أحمد محمد:</strong> 0911111111</p>
                    <p><strong>سارة علي:</strong> 0922222222</p>
                    <p><strong>خالد أحمد:</strong> 0933333333</p>
                    <p style="margin-top: 10px;"><strong>كلمة المرور:</strong> password123</p>
                </div>

                <div class="form-group">
                    <label>رقم الهاتف:</label>
                    <input type="text" id="phone" value="0911111111" placeholder="0911111111">
                </div>

                <div class="form-group">
                    <label>كلمة المرور:</label>
                    <input type="password" id="password" value="password123" placeholder="password123">
                </div>

                <button class="btn btn-primary" onclick="login()">تسجيل الدخول</button>

                <div id="loginResult" class="result"></div>
            </div>

            <!-- Firebase Status Card -->
            <div class="card">
                <h2>🔥 حالة Firebase</h2>

                <div class="info-box" style="background: #fef3c7; border-color: #f59e0b;">
                    <h3 style="color: #92400e;">⚠️ ملاحظة:</h3>
                    <p style="color: #78350f;">
                        <strong>للاختبار بدون Flutter:</strong><br>
                        يمكنك تحديث FCM Token يدوياً من الخانة أدناه.<br>
                        ضع أي نص طويل (محاكاة Real Token).
                    </p>
                </div>

                <button class="btn btn-info" onclick="checkFirebaseStatus()">فحص حالة Firebase</button>
                <button class="btn btn-info" onclick="checkMyToken()">فحص FCM Token الخاص بي</button>
                <button class="btn btn-info" onclick="getUsersWithTokens()">عرض المستخدمين مع Tokens</button>

                <div class="form-group" style="margin-top: 20px;">
                    <label>🔄 FCM Token:</label>
                    <div style="padding: 10px; background: #f0f0f0; border-radius: 5px; margin-bottom: 10px;">
                        <small id="tokenStatus" style="color: #666;">⏳ جاري جلب Token من Firebase...</small>
                    </div>
                    <textarea id="newFcmToken" rows="3" placeholder="ينتظر Token من Firebase..." readonly style="background: #f9f9f9;"></textarea>
                    <button class="btn btn-success" onclick="updateFcmToken()" id="updateTokenBtn" disabled style="margin-top: 10px;">
                        تحديث Token (ينتظر Token من Firebase...)
                    </button>
                </div>

                <div id="firebaseResult" class="result"></div>

                <div id="usersGrid" class="users-grid" style="display: none;"></div>
            </div>
        </div>

        <div class="main-content">
            <!-- Send Notification Card -->
            <div class="card">
                <h2>📤 إرسال إشعار</h2>

                <div class="form-group">
                    <label>نوع الإرسال:</label>
                    <select id="sendType" onchange="toggleRecipient()">
                        <option value="me">إرسال لنفسي</option>
                        <option value="user">إرسال لمستخدم محدد</option>
                        <option value="all">إرسال للجميع</option>
                    </select>
                </div>

                <div class="form-group" id="userIdGroup" style="display: none;">
                    <label>ID المستخدم:</label>
                    <input type="number" id="userId" value="2" placeholder="2">
                </div>

                <div class="form-group">
                    <label>عنوان الإشعار:</label>
                    <input type="text" id="notifTitle" value="إشعار تجريبي 🔔" placeholder="عنوان الإشعار">
                </div>

                <div class="form-group">
                    <label>محتوى الإشعار:</label>
                    <textarea id="notifBody" placeholder="محتوى الإشعار">هذا إشعار تجريبي من RentSY!</textarea>
                </div>

                <button class="btn btn-success" onclick="sendNotification()">إرسال الإشعار</button>

                <div id="sendResult" class="result"></div>
            </div>

            <!-- Notifications List Card -->
            <div class="card">
                <h2>📬 إشعاراتي</h2>

                <button class="btn btn-info" onclick="getMyNotifications()">تحديث الإشعارات</button>
                <button class="btn btn-primary" onclick="markAllAsRead()">تحديد الكل كمقروء</button>

                <div id="notificationsResult" class="result"></div>

                <div class="loading" id="notificationsLoading">
                    <div class="spinner"></div>
                    <p>جاري التحميل...</p>
                </div>

                <div id="notificationsList" class="notifications-list"></div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = 'http://localhost:8000/api';
        let authToken = null;
        let currentUserId = null;
        let messaging = null;
        let currentFCMToken = null;

        // Firebase Configuration (من Firebase Console)
        const firebaseConfig = {
            apiKey: "AIzaSyAKgu-SN0ztdBQ_GODbESu3WsZmXtAVexs",
            authDomain: "rent-sy-00.firebaseapp.com",
            projectId: "rent-sy-00",
            storageBucket: "rent-sy-00.firebasestorage.app",
            messagingSenderId: "422891128442",
            appId: "1:422891128442:web:a5bc0f8626a67922a51dc3",
            measurementId: "G-4S0EQMG44G"
        };

        // تهيئة Firebase عند تحميل الصفحة
        window.addEventListener('load', function() {
            try {
                if (typeof firebase !== 'undefined') {
                    firebase.initializeApp(firebaseConfig);
                    messaging = firebase.messaging();
                    console.log('✅ Firebase initialized');

                    // محاولة الحصول على Token تلقائياً
                    getFirebaseToken();
                } else {
                    console.log('⚠️ Firebase SDK not loaded - using manual token update');
                }
            } catch (error) {
                console.log('⚠️ Firebase init error:', error.message);
            }
        });

        // الحصول على Firebase Token
        async function getFirebaseToken() {
            const statusEl = document.getElementById('tokenStatus');
            const tokenEl = document.getElementById('newFcmToken');
            const btnEl = document.getElementById('updateTokenBtn');

            try {
                statusEl.textContent = '⏳ طلب إذن الإشعارات من المتصفح...';
                statusEl.style.color = '#f59e0b';

                const permission = await Notification.requestPermission();

                if (permission === 'granted') {
                    statusEl.textContent = '✅ تم منح الإذن! جاري جلب Token...';
                    statusEl.style.color = '#10b981';

                    const token = await messaging.getToken({
                        vapidKey: 'BHR62lbI3Cn1I8XGjEUoQjpZuqDM37-0H2eGRVbE0f4SL-rVMOYNc742V1OyoZz20W2wma7x5PojlESumIj8W54'
                    });

                    if (token) {
                        currentFCMToken = token;
                        tokenEl.value = token;
                        tokenEl.readOnly = false;
                        btnEl.disabled = false;
                        btnEl.textContent = 'تحديث Token في Backend';

                        statusEl.textContent = '✅ تم الحصول على Real FCM Token من Firebase!';
                        statusEl.style.color = '#10b981';

                        console.log('🔑 Got Firebase Token:', token.substring(0, 30) + '...');

                        showResult('firebaseResult',
                            '✅ تم الحصول على Real FCM Token من Firebase!<br>' +
                            '🔑 Token Length: ' + token.length + ' characters<br>' +
                            'الآن سجّل دخول وسيُرسل تلقائياً!',
                            true
                        );
                    } else {
                        throw new Error('No token received from Firebase');
                    }
                } else if (permission === 'denied') {
                    statusEl.textContent = '❌ تم رفض إذن الإشعارات';
                    statusEl.style.color = '#ef4444';
                    tokenEl.placeholder = 'Permission مرفوضة - لا يمكن الحصول على Token';
                    console.log('❌ Notification permission denied');

                    showResult('firebaseResult',
                        '❌ تم رفض إذن الإشعارات!<br>' +
                        'الحل:<br>' +
                        '1. اضغط 🔒 قبل localhost<br>' +
                        '2. Permissions → Notifications → Allow<br>' +
                        '3. Reload الصفحة',
                        false
                    );
                } else {
                    statusEl.textContent = '⚠️ لم يتم منح الإذن';
                    statusEl.style.color = '#f59e0b';
                    tokenEl.placeholder = 'اضغط Allow عندما يطلب المتصفح الإذن';
                }
            } catch (error) {
                console.error('❌ Error getting token:', error);
                statusEl.textContent = '❌ خطأ: ' + error.message;
                statusEl.style.color = '#ef4444';
                tokenEl.placeholder = 'فشل الحصول على Token - شوف Console (F12)';
                tokenEl.readOnly = false;
                btnEl.textContent = 'تحديث Token يدوياً (للاختبار)';
                btnEl.disabled = false;

                showResult('firebaseResult',
                    '❌ فشل الحصول على Token من Firebase!<br>' +
                    'الخطأ: ' + error.message + '<br><br>' +
                    '💡 يمكنك:<br>' +
                    '1. حط token يدوياً للاختبار<br>' +
                    '2. أو جرّب متصفح Chrome<br>' +
                    '3. أو شوف Console (F12) للتفاصيل',
                    false
                );
            }
        }

        // استماع للإشعارات
        if (messaging) {
            messaging.onMessage((payload) => {
                console.log('📩 Notification received:', payload);
                const title = payload.notification?.title || 'إشعار جديد';
                const body = payload.notification?.body || '';
                alert(`📩 ${title}\n${body}`);
                if (authToken) getMyNotifications();
            });
        }

        function showResult(elementId, message, isSuccess = true, data = null) {
            const element = document.getElementById(elementId);
            element.className = `result ${isSuccess ? 'success' : 'error'}`;
            element.innerHTML = `
                <strong>${isSuccess ? '✅ نجح' : '❌ خطأ'}:</strong> ${message}
                ${data ? `<pre>${JSON.stringify(data, null, 2)}</pre>` : ''}
            `;
        }

        function toggleRecipient() {
            const sendType = document.getElementById('sendType').value;
            const userIdGroup = document.getElementById('userIdGroup');
            userIdGroup.style.display = sendType === 'user' ? 'block' : 'none';
        }

        async function login() {
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;

            // جلب FCM Token إذا موجود
            const fcmToken = document.getElementById('newFcmToken').value.trim();

            try {
                const requestBody = {
                    phone,
                    password
                };

                // إضافة FCM Token إذا موجود
                if (fcmToken) {
                    requestBody.fcm_token = fcmToken;
                    console.log('📤 Sending FCM Token with login...');
                }

                const response = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(requestBody)
                });

                const data = await response.json();

                if (data.status === 'success') {
                    authToken = data.data.token;
                    currentUserId = data.data.user.id;
                    document.getElementById('authStatus').className = 'status-badge online';
                    document.getElementById('authStatus').textContent = `✅ مسجل (${data.data.user.first_name})`;

                    let successMessage = 'تم تسجيل الدخول بنجاح!';
                    if (data.data.fcm_token_updated) {
                        successMessage += '<br>✅ تم تحديث FCM Token تلقائياً!';
                        console.log('✅ FCM Token stored in backend');
                    }

                    showResult('loginResult', successMessage, true, {
                        user_id: currentUserId,
                        name: `${data.data.user.first_name} ${data.data.user.last_name}`,
                        token: authToken.substring(0, 20) + '...',
                        fcm_token_updated: data.data.fcm_token_updated || false
                    });

                    // Auto check Firebase status
                    setTimeout(checkFirebaseStatus, 500);
                } else {
                    showResult('loginResult', data.message || 'فشل تسجيل الدخول', false);
                }
            } catch (error) {
                showResult('loginResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        async function checkFirebaseStatus() {
            if (!authToken) {
                showResult('firebaseResult', 'يرجى تسجيل الدخول أولاً', false);
                return;
            }

            try {
                const response = await fetch(`${API_URL}/test-notifications/firebase-status`, {
                    headers: { 'Authorization': `Bearer ${authToken}` }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    document.getElementById('firebaseStatus').className = 'status-badge online';
                    document.getElementById('firebaseStatus').textContent = '✅ Firebase متصل';
                    showResult('firebaseResult', data.message, true);
                } else {
                    document.getElementById('firebaseStatus').className = 'status-badge offline';
                    document.getElementById('firebaseStatus').textContent = '❌ Firebase غير متصل';
                    showResult('firebaseResult', data.message, false);
                }
            } catch (error) {
                showResult('firebaseResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        async function checkMyToken() {
            if (!authToken) {
                showResult('firebaseResult', 'يرجى تسجيل الدخول أولاً', false);
                return;
            }

            try {
                const response = await fetch(`${API_URL}/test-notifications/check-my-token`, {
                    headers: { 'Authorization': `Bearer ${authToken}` }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    const hasToken = data.data.has_token;
                    const tokenPreview = data.data.token;

                    let message = `
                        <strong>معلومات FCM Token:</strong><br><br>
                        <strong>لديك Token:</strong> ${hasToken ? '✅ نعم' : '❌ لا'}<br>
                        <strong>Token Preview:</strong> ${tokenPreview || 'غير متوفر'}<br><br>
                        ${hasToken ?
                            '✅ <strong style="color: #10b981;">Token موجود - يمكنك إرسال الإشعارات!</strong><br>الإشعارات ستُحفظ في Database.'
                            : '⚠️ <strong style="color: #f59e0b;">لا يوجد Token - حدّث Token من الخانة أعلاه</strong>'}
                    `;

                    showResult('firebaseResult', message, hasToken);
                } else {
                    showResult('firebaseResult', data.message, false);
                }
            } catch (error) {
                showResult('firebaseResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        async function updateFcmToken() {
            if (!authToken) {
                showResult('firebaseResult', 'يرجى تسجيل الدخول أولاً', false);
                return;
            }

            const newToken = document.getElementById('newFcmToken').value.trim();

            if (!newToken) {
                showResult('firebaseResult', 'يرجى إدخال FCM Token', false);
                return;
            }

            try {
                const response = await fetch(`${API_URL}/fcm-token`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ fcm_token: newToken })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showResult('firebaseResult', '✅ تم تحديث FCM Token بنجاح! الآن يمكنك تلقي Push Notifications', true);
                    document.getElementById('newFcmToken').value = '';

                    // Auto check token
                    setTimeout(checkMyToken, 1000);
                } else {
                    showResult('firebaseResult', data.message, false);
                }
            } catch (error) {
                showResult('firebaseResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        async function getUsersWithTokens() {
            if (!authToken) {
                showResult('firebaseResult', 'يرجى تسجيل الدخول أولاً', false);
                return;
            }

            try {
                const response = await fetch(`${API_URL}/test-notifications/users-with-tokens`, {
                    headers: { 'Authorization': `Bearer ${authToken}` }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    const usersGrid = document.getElementById('usersGrid');
                    usersGrid.style.display = 'grid';
                    usersGrid.innerHTML = '';

                    data.data.users.forEach(user => {
                        const userCard = document.createElement('div');
                        userCard.className = 'user-card';
                        userCard.innerHTML = `
                            <h4>${user.first_name} ${user.last_name}</h4>
                            <p>📱 ${user.phone}</p>
                            <p>👤 ${user.role}</p>
                            <p><small>ID: ${user.id}</small></p>
                        `;
                        userCard.onclick = () => {
                            document.getElementById('userId').value = user.id;
                            document.getElementById('sendType').value = 'user';
                            toggleRecipient();
                            document.querySelectorAll('.user-card').forEach(c => c.classList.remove('selected'));
                            userCard.classList.add('selected');
                        };
                        usersGrid.appendChild(userCard);
                    });

                    showResult('firebaseResult', `تم جلب ${data.data.count} مستخدم`, true);
                } else {
                    showResult('firebaseResult', data.message, false);
                }
            } catch (error) {
                showResult('firebaseResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        async function sendNotification() {
            if (!authToken) {
                showResult('sendResult', 'يرجى تسجيل الدخول أولاً', false);
                return;
            }

            const sendType = document.getElementById('sendType').value;
            const title = document.getElementById('notifTitle').value;
            const body = document.getElementById('notifBody').value;

            let endpoint = '';
            let requestBody = {};

            if (sendType === 'me') {
                endpoint = `${API_URL}/test-notifications/send-to-me`;
            } else if (sendType === 'user') {
                const userId = document.getElementById('userId').value;
                endpoint = `${API_URL}/test-notifications/send-to-user`;
                requestBody = { user_id: parseInt(userId), title, body };
            } else if (sendType === 'all') {
                endpoint = `${API_URL}/test-notifications/send-to-all`;
                requestBody = { title, body };
            }

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json'
                    },
                    body: sendType !== 'me' ? JSON.stringify(requestBody) : null
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showResult('sendResult', 'تم إرسال الإشعار بنجاح! ✨', true, data.data);

                    // Auto refresh notifications if sending to self
                    if (sendType === 'me') {
                        setTimeout(getMyNotifications, 1000);
                    }
                } else {
                    showResult('sendResult', data.message, false);
                }
            } catch (error) {
                showResult('sendResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        async function getMyNotifications() {
            if (!authToken) {
                showResult('notificationsResult', 'يرجى تسجيل الدخول أولاً', false);
                return;
            }

            const loading = document.getElementById('notificationsLoading');
            const list = document.getElementById('notificationsList');

            loading.classList.add('active');
            list.innerHTML = '';

            try {
                const response = await fetch(`${API_URL}/notifications`, {
                    headers: { 'Authorization': `Bearer ${authToken}` }
                });

                const data = await response.json();
                loading.classList.remove('active');

                if (response.ok && data.data) {
                    const notifications = data.data;

                    if (notifications.length === 0) {
                        list.innerHTML = '<p style="text-align: center; color: #6b7280; padding: 20px;">لا توجد إشعارات</p>';
                        return;
                    }

                    notifications.forEach(notif => {
                        const notifItem = document.createElement('div');
                        notifItem.className = `notification-item ${!notif.read_at ? 'unread' : ''}`;
                        notifItem.innerHTML = `
                            <h4>${notif.title}</h4>
                            <p>${notif.body}</p>
                            <small>${notif.created_at || 'الآن'} ${!notif.read_at ? '• غير مقروء' : ''}</small>
                        `;
                        list.appendChild(notifItem);
                    });

                    showResult('notificationsResult', `تم جلب ${notifications.length} إشعار`, true);
                } else {
                    showResult('notificationsResult', 'فشل جلب الإشعارات', false);
                }
            } catch (error) {
                loading.classList.remove('active');
                showResult('notificationsResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        async function markAllAsRead() {
            if (!authToken) {
                showResult('notificationsResult', 'يرجى تسجيل الدخول أولاً', false);
                return;
            }

            try {
                const response = await fetch(`${API_URL}/notifications/mark-all-read`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${authToken}` }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showResult('notificationsResult', 'تم تحديد جميع الإشعارات كمقروءة', true);
                    setTimeout(getMyNotifications, 500);
                } else {
                    showResult('notificationsResult', data.message, false);
                }
            } catch (error) {
                showResult('notificationsResult', 'خطأ في الاتصال: ' + error.message, false);
            }
        }

        // Auto-login on page load (optional - for testing)
        // window.onload = () => login();
    </script>
</body>
</html>
