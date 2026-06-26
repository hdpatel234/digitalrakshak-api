<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DigitalRakshak API</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#8b5cf6',
                        darkBg: '#0f172a',
                        cardBg: 'rgba(30, 41, 59, 0.7)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            overflow-x: hidden;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .gradient-text {
            background: linear-gradient(to right, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(15,23,42,0) 70%);
            top: -200px;
            left: -200px;
            z-index: -1;
            border-radius: 50%;
        }
        .glow-right {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, rgba(15,23,42,0) 70%);
            bottom: 0;
            right: -100px;
            z-index: -1;
            border-radius: 50%;
        }
        .code-block {
            font-family: 'Fira Code', monospace;
        }
    </style>
</head>
<body class="antialiased relative min-h-screen flex flex-col">
    <!-- Ambient glows -->
    <div class="glow"></div>
    <div class="glow-right"></div>

    <!-- Navigation -->
    <nav class="w-full z-50 glass-panel border-b-0 border-t-0 border-x-0 border-b border-white/10 sticky top-0">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-wide">Digital<span class="text-blue-400">Rakshak</span> API</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="/docs" class="text-sm text-slate-300 hover:text-white transition-colors hidden sm:block">Documentation</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium px-5 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all border border-white/5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/30">Developer Login</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center justify-center px-6 pt-20 pb-24 relative z-10">
        <div class="max-w-4xl w-full text-center mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-blue-500/30 text-blue-400 text-xs font-semibold mb-6">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                v1.0 is now live
            </div>
            <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight leading-tight">
                Secure. Fast. <br/>
                <span class="gradient-text">Intelligent API.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                The core backbone of the DigitalRakshak ecosystem. Integrate powerful security features, real-time threat detection, and seamless authentication into your applications with ease.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#endpoints" class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold transition-all shadow-[0_0_20px_rgba(37,99,235,0.4)] hover:shadow-[0_0_30px_rgba(37,99,235,0.6)] transform hover:-translate-y-1">
                    Explore Endpoints
                </a>
                <a href="https://github.com/your-repo" class="px-8 py-4 rounded-xl glass-panel text-white font-semibold hover:bg-white/10 transition-all border border-slate-700 hover:border-slate-500 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
                    View on GitHub
                </a>
            </div>
        </div>

        <!-- Code Preview Section -->
        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-8 items-center mt-12">
            <div class="order-2 md:order-1 glass-panel rounded-2xl p-6 border border-slate-700/50 shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500"></div>
                <div class="flex items-center gap-2 mb-4 border-b border-slate-700/50 pb-4">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span class="ml-2 text-xs text-slate-400 font-mono">POST /api/v1/auth/verify</span>
                </div>
                <pre class="text-sm text-slate-300 font-mono overflow-x-auto pb-2"><code class="code-block"><span class="text-purple-400">curl</span> -X POST https://api.digitalrakshak.com/v1/auth/verify \
  -H <span class="text-green-400">"Authorization: Bearer YOUR_TOKEN"</span> \
  -H <span class="text-green-400">"Content-Type: application/json"</span> \
  -d <span class="text-yellow-300">'{
    "device_id": "dr_83h92nd",
    "threat_level": "high"
  }'</span></code></pre>
            </div>

            <div class="order-1 md:order-2 space-y-6 text-left">
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center shrink-0 border border-blue-500/30">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2">Lightning Fast Response</h3>
                        <p class="text-slate-400 text-sm">Optimized infrastructure delivering sub-50ms latency globally, ensuring your security checks never block user experience.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center shrink-0 border border-purple-500/30">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2">Bank-grade Security</h3>
                        <p class="text-slate-400 text-sm">End-to-end encryption, OAuth2 integration (including DigiLocker SSO), and robust rate limiting to protect your services.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0 border border-emerald-500/30">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2">99.99% Uptime</h3>
                        <p class="text-slate-400 text-sm">Highly available architecture with automated failovers. The defense system you can rely on, 24/7.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/50 py-8 relative z-10 mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center gap-2 mb-4 md:mb-0">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span class="text-sm font-semibold text-slate-300">DigitalRakshak</span>
            </div>
            <div class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} DigitalRakshak. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
