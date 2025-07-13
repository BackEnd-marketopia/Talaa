<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyACaR4Znix8Tw3YiT_FE2DasPluCEXmtoE",
        authDomain: "e-commerce-fb183.firebaseapp.com",
        projectId: "e-commerce-fb183",
        storageBucket: "e-commerce-fb183.firebasestorage.app",
        messagingSenderId: "683824038399",
        appId: "1:683824038399:web:f30701460945005c3b27ca",
        vapidKey: "BPGhkBk9BGX6drBprPVuq79uM98kF4DRk7qo23k9Rz43z-rMeL-iSKUUH4nX20-X9ryyO9Oec8_kMRFPjAZP0Ck",
    };
   

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    // جلب FCM Token
    getToken(messaging, { vapidKey: firebaseConfig.vapidKey }).then((token) => {
        if (token) {
            console.log("FCM Token:", token);
            fetch("{{ route('admin.save-fcm-token') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ 
                    fcm_token: token,
                    user_id: {{ auth('web')->user()?->id }} 
                 })
            });
        }
    });    // استقبال Notification
    onMessage(messaging, (payload) => {
        console.log("New order notification", payload);
        Swal.fire({
            title:  '{{ __("message.New Order") }}',
            text:  `{{ __("message.You have a new order") }}`,
            icon: 'success',
            confirmButtonText: '{{ __("message.View Orders") }}',
            confirmButtonColor: '#9333EA',
            showCancelButton: true,
            cancelButtonText: '{{ __("message.Close") }}',
            cancelButtonColor: '#EEEEEE',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("filament.admin.resources.orders.index") }}';
            }
        });
    });
</script>
    