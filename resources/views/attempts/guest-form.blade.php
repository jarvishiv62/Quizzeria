@extends('layouts.app')
@section('title', 'Start Quiz')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">

        {{-- Card --}}
        <div class="relative bg-white rounded-2xl border border-stone-200 shadow-md overflow-hidden">
            {{-- Top accent stripe --}}
            <div class="h-2 w-full" style="background: linear-gradient(90deg, #7C3AED, #EAB308, #F43F5E);"></div>

            {{-- Sunflower decoration --}}
            <x-sunflower class="top-[-20px] right-[-20px]" :size="160" :opacity="0.08" />

            <div class="relative z-10 p-8">
                {{-- Quiz badge --}}
                <div class="inline-flex items-center gap-1.5 bg-quiz-violet-lt text-violet-700 text-xs font-semibold px-3 py-1.5 rounded-md mb-5">
                    🌻 Ready to attempt
                </div>

                {{-- Quiz title --}}
                <h1 class="font-display text-2xl sm:text-3xl text-stone-900 mb-2 leading-snug">
                    {{ $quiz->title }}
                </h1>

                @if($quiz->description)
                <p class="text-stone-500 text-sm mb-6 leading-relaxed">{{ $quiz->description }}</p>
                @endif

                {{-- Quiz meta --}}
                <div class="flex flex-wrap gap-3 mb-8">
                    <div class="flex items-center gap-1.5 bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-sm">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium text-stone-700">{{ $quiz->questions_count }}</span>
                        <span class="text-stone-500">Questions</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-sm">
                        <svg class="w-4 h-4 text-quiz-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span class="font-medium text-stone-700">{{ $quiz->total_marks }}</span>
                        <span class="text-stone-500">Total Marks</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-sm">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-stone-500">No time limit</span>
                    </div>
                </div>

                {{-- Guest Form --}}
                <form action="{{ route('attempts.store', $quiz) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-stone-700 mb-2">
                            Your Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="guest_name"
                               placeholder="Enter your full name"
                               value="{{ old('guest_name') }}"
                               class="w-full px-4 py-3 rounded-xl border-2 border-stone-200 text-stone-900 text-sm focus-ring transition-all placeholder:text-stone-400"
                               required autofocus />
                        @error('guest_name')
                            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-stone-400">This will appear on the leaderboard.</p>
                    </div>

                    <button type="submit"
                            class="w-full btn-primary py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Start Quiz
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('quizzes.index') }}"
                       class="text-sm text-stone-400 hover:text-stone-600 transition-colors">
                        ← Back to all quizzes
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
