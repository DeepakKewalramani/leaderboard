<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('style')

</head>

<body class="bg-gray-100 p-6">

    <div class="container mx-auto">
        <div id="message-box" style="display:none; margin-bottom: 10px; padding: 10px; border-radius: 5px;"></div>
        @yield('content')
    </div>

    @yield('script')
    <script>
        function showMessage(message = 'Something went wrong', type = 'info') {
            const msgBox = document.getElementById("message-box");
            msgBox.textContent = message;

            let bgColor;
            switch (type) {
                case 'success':
                    bgColor = '#d4edda';
                    break;
                case 'error':
                    bgColor = '#f8d7da';
                    break;
                default:
                    bgColor = '#cce5ff';
            }

            msgBox.style.backgroundColor = bgColor;
            msgBox.style.opacity = '1';
            msgBox.style.display = 'block';
            msgBox.style.transition = 'opacity 0.5s ease-in-out';

            setTimeout(() => {
                msgBox.style.opacity = '0';
                setTimeout(() => {
                    msgBox.style.display = 'none';
                }, 500);
            }, 3000);
        }
    </script>
    </script>
</body>

</html>
