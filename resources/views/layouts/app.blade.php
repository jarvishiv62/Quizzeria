<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'QuizBloom') — QuizBloom</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />

    {{-- TailwindCSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['DM Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        quiz: {
                            yellow:      '#EAB308',
                            'yellow-lt': '#FEF9C3',
                            'yellow-md': '#FDE68A',
                            violet:      '#7C3AED',
                            'violet-lt': '#EDE9FE',
                            'violet-md': '#C4B5FD',
                            rose:        '#F43F5E',
                            'rose-lt':   '#FFE4E6',
                            amber:       '#F59E0B',
                            slate:       '#64748B',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'DM Sans', sans-serif; background: #FAFAF7; color: #1C1917; }
        .font-display { font-family: 'Playfair Display', serif; }
        .sunflower-bg { pointer-events: none; position: absolute; z-index: 0; }
        [x-cloak] { display: none !important; }
        .card-hover { transition: all 0.2s ease; }
        .card-hover:hover { box-shadow: 0 8px 24px rgba(124,58,237,0.10), 0 2px 8px rgba(234,179,8,0.08); transform: translateY(-2px); border-color: #C4B5FD; }
        .btn-primary { background: #7C3AED; color: #fff; transition: background 0.18s; }
        .btn-primary:hover { background: #6D28D9; }
        .btn-yellow { background: #EAB308; color: #1C1917; transition: background 0.18s; }
        .btn-yellow:hover { background: #CA8A04; }
        .focus-ring:focus { outline: none; box-shadow: 0 0 0 3px rgba(124,58,237,0.25); border-color: #7C3AED; }
        input, textarea, select { font-family: 'DM Sans', sans-serif; }
        .gradient-badge { background: linear-gradient(135deg, #7C3AED 0%, #EAB308 100%); }
        .option-card { cursor: pointer; transition: all 0.15s; border: 2px solid #E7E5E4; border-radius: 10px; }
        .option-card:hover { border-color: #C4B5FD; background: #F5F3FF; }
        .option-card.selected { border-color: #7C3AED; background: #EDE9FE; }
        .option-card-yellow:hover { border-color: #FDE68A; background: #FEFCE8; }
        .option-card-yellow.selected { border-color: #EAB308; background: #FEF9C3; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Top Navigation --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-stone-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('quizzes.index') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg gradient-badge flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <span class="font-display text-xl text-stone-900 tracking-tight">QuizBloom</span>
                </a>

                {{-- Nav Links --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('quizzes.index') }}"
                       class="text-sm font-medium text-stone-600 hover:text-violet-700 transition-colors px-3 py-2 rounded-lg hover:bg-violet-50">
                        Dashboard
                    </a>
                    <a href="{{ route('quizzes.create') }}"
                       class="btn-primary text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Quiz
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-20 right-4 z-50 max-w-sm w-full">
            <div class="bg-white border-l-4 border-quiz-yellow rounded-lg shadow-lg px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-quiz-yellow mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-stone-700 font-medium">{{ session('success') }}</p>
                <button @click="show = false" class="ms-auto text-stone-400 hover:text-stone-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-20 right-4 z-50 max-w-sm w-full">
            <div class="bg-white border-l-4 border-rose-500 rounded-lg shadow-lg px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-stone-700 font-medium">{{ session('error') }}</p>
                <button @click="show = false" class="ms-auto text-stone-400 hover:text-stone-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-stone-200 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-stone-400">
                QuizBloom &copy; {{ date('Y') }} &mdash; Built with Laravel &amp; 🌻
            </p>
        </div>
    </footer>

</body>
</html>
