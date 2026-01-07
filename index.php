<?php
session_start();
// Auto-redirect logic based on role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'staff') {
        header("Location: dashboard.php");
    } else {
        header("Location: customer_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalonOS | Your Beauty Journey Starts Here</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a; 
        }

        .gradient-bg {
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(244, 63, 94, 0.15), transparent);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white overflow-x-hidden">

    <nav class="max-w-7xl mx-auto px-8 py-8 flex justify-between items-center relative z-50">
        <h1 class="text-3xl font-extrabold tracking-tighter italic">
            SALON<span class="text-indigo-500">OS</span>
        </h1>
        <a href="login.php" class="group flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-slate-400 hover:text-white transition">
            <span class="w-8 h-[1px] bg-slate-700 group-hover:bg-indigo-500 transition"></span>
            Staff Access
        </a>
    </nav>

    <header class="max-w-7xl mx-auto px-8 pt-20 pb-32 relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px] -z-10"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-rose-600/10 rounded-full blur-[120px] -z-10"></div>

        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card text-xs font-bold uppercase tracking-widest text-indigo-400">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Next-Gen Salon Management
                </div>

                <h2 class="text-7xl lg:text-8xl font-black leading-[0.9] tracking-tight text-white">
                    Elegance <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-rose-400 italic">Redefined.</span>
                </h2>

                <p class="text-xl text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium">
                    Experience world-class beauty services. Book your next appointment, explore premium products, and manage your aesthetic journey with SalonOS.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                    <a href="customer_login.php" class="relative group overflow-hidden bg-white text-slate-900 px-12 py-5 rounded-2xl font-black text-xl transition-all hover:scale-105 active:scale-95 shadow-2xl shadow-indigo-500/20">
                        <span class="relative z-10 flex items-center gap-3">
                            BOOK NOW <i class="fa fa-arrow-right text-sm"></i>
                        </span>
                    </a>
                </div>

                <div class="flex items-center justify-center lg:justify-start gap-8 pt-12 opacity-50 grayscale hover:grayscale-0 transition duration-500">
                    <i class="fab fa-instagram text-2xl"></i>
                    <i class="fab fa-facebook text-2xl"></i>
                    <i class="fab fa-google text-2xl"></i>
                    <span class="text-xs font-bold tracking-widest uppercase">Trusted by 10k+ Guests</span>
                </div>
            </div>

            <div class="hidden lg:block relative">
                <div class="glass-card p-4 rounded-[3rem] animate-float relative z-10 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&q=80&w=1000" 
                         alt="Luxury Salon" 
                         class="rounded-[2.5rem] w-full object-cover aspect-[4/5] shadow-2xl">
                    
                    <div class="absolute bottom-10 -left-10 glass-card p-6 rounded-3xl shadow-2xl animate-pulse">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center">
                                <i class="fa fa-check text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Status</p>
                                <p class="text-lg font-black text-white">Hair Spa Ready</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="absolute -top-10 -right-10 w-64 h-64 border border-slate-700 rounded-full -z-0"></div>
                <div class="absolute -bottom-20 -right-20 w-96 h-96 border border-indigo-500/20 rounded-full -z-0"></div>
            </div>
        </div>
    </header>

    <footer class="max-w-7xl mx-auto px-8 py-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
        <p class="text-slate-500 text-sm font-medium">© 2026 SalonOS. All rights reserved.</p>
        <div class="flex gap-8 text-xs font-bold uppercase tracking-widest text-slate-500">
            <a href="#" class="hover:text-white transition">Privacy</a>
            <a href="#" class="hover:text-white transition">Terms</a>
            <a href="#" class="hover:text-white transition">Contact</a>
        </div>
    </footer>
</body>
</html>