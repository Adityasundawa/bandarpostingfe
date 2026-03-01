<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Bandarkode</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        'bk-dark': '#0b0f1a',    // Lebih pekat ala Bandarkode
                        'bk-card': '#161b26',    // Card navy gelap
                        'bk-accent': '#0ea5e9',  // Sky blue
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply bg-bk-dark text-gray-200 antialiased;
                background-image: radial-gradient(circle at top right, #1e293b 0%, #0b0f1a 40%);
            }
        }
        .bk-input {
            @apply w-full px-4 py-3 rounded-lg bg-[#0f1420] border border-gray-800 text-white transition-all duration-200 focus:outline-none focus:border-bk-accent/50 focus:ring-4 focus:ring-bk-accent/10 placeholder-gray-600;
        }
    </style>
</head>
<body class="font-sans">
    <div class="min-h-screen flex flex-col justify-center items-center p-6">
        <div class="w-full sm:max-w-[440px]">
            <div class="bg-bk-card shadow-2xl rounded-2xl border border-gray-800/60 p-8 sm:p-10 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-bk-accent/10 rounded-full blur-3xl"></div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
