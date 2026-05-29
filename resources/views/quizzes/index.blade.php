@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Hero Section --}}
    <div class="relative bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-10 px-8 py-10">
        {{-- Sunflower decoration top-right --}}
        <x-sunflower class="top-[-40px] right-[-40px]" :size="240" :opacity="0.10" />
        {{-- Small sunflower bottom-left --}}
        <x-sunflower class="bottom-[-50px] left-[-30px]" :size="160" :opacity="0.06" />

        <div class="relative z-10 max-w-xl">
            <div class="inline-flex items-center gap-2 bg-quiz-yellow-lt text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-md mb-4">
                🌻 Welcome to QuizBloom
            </div>
            <h1 class="font-display text-3xl sm:text-4xl text-stone-900 mb-3 leading-tight">
                Create, Share &amp; Master<br/>
                <span style="color:#7C3AED;">Any Topic</span> with Quizzes
            </h1>
            <p class="text-stone-500 text-base mb-6">
                Build interactive quizzes with multiple question types, track attempts, and celebrate top scorers on the leaderboard.
            </p>
            <a href="{{ route('quizzes.create') }}"
               class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Quiz
            </a>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
        @php
            $stats = [
                ['label' => 'Total Quizzes',   'value' => $totalQuizzes,  'color' => '#7C3AED', 'bg' => '#EDE9FE', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['label' => 'Total Attempts',  'value' => $totalAttempts, 'color' => '#EAB308', 'bg' => '#FEF9C3', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Avg. Score',      'value' => round($avgScore ?? 0, 1) . '%', 'color' => '#F43F5E', 'bg' => '#FFE4E6', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm px-6 py-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                 style="background:{{ $stat['bg'] }}">
                <svg class="w-5 h-5" fill="none" stroke="{{ $stat['color'] }}" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-stone-900">{{ $stat['value'] }}</p>
                <p class="text-xs text-stone-500 font-medium">{{ $stat['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quiz Cards Grid --}}
    @if($quizzes->isEmpty())
        <div class="text-center py-24 relative">
            <x-sunflower class="top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" :size="200" :opacity="0.06" />
            <div class="relative z-10">
                <p class="text-5xl mb-4">🌻</p>
                <h3 class="font-display text-xl text-stone-700 mb-2">No quizzes yet</h3>
                <p class="text-stone-400 text-sm mb-6">Create your first quiz and start blooming!</p>
                <a href="{{ route('quizzes.create') }}"
                   class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold text-sm">
                    Create First Quiz
                </a>
            </div>
        </div>
    @else
        <div class="mb-6 flex items-center justify-between">
            <h2 class="font-display text-xl text-stone-800">All Quizzes</h2>
            <span class="text-sm text-stone-400">{{ $quizzes->count() }} quiz{{ $quizzes->count() !== 1 ? 'zes' : '' }}</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
            @foreach($quizzes as $quiz)
                <x-quiz-card :quiz="$quiz" />
            @endforeach
        </div>
    @endif

    {{-- Recent Attempts Table --}}
    @if($recentAttempts->isNotEmpty())
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
            <h2 class="font-display text-lg text-stone-800">Recent Attempts</h2>
            <span class="text-xs text-stone-400">Last {{ $recentAttempts->count() }} submissions</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-500 text-xs font-semibold uppercase tracking-wide">
                        <th class="text-start px-6 py-3">Guest</th>
                        <th class="text-start px-6 py-3">Quiz</th>
                        <th class="text-start px-6 py-3">Score</th>
                        <th class="text-start px-6 py-3">Result</th>
                        <th class="text-start px-6 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($recentAttempts as $attempt)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full gradient-badge flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($attempt->guest_name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-stone-800">{{ $attempt->guest_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-stone-600">{{ $attempt->quiz->title ?? '—' }}</td>
                        <td class="px-6 py-3.5 font-semibold text-stone-800">
                            {{ $attempt->score ?? '—' }} / {{ $attempt->total_marks }}
                        </td>
                        <td class="px-6 py-3.5">
                            @if($attempt->score !== null)
                                @php $pct = $attempt->total_marks > 0 ? round(($attempt->score / $attempt->total_marks) * 100) : 0; @endphp
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md
                                    {{ $pct >= 50 ? 'bg-quiz-yellow-lt text-amber-700' : 'bg-rose-50 text-rose-600' }}">
                                    {{ $pct }}% {{ $pct >= 50 ? '✓' : '✗' }}
                                </span>
                            @else
                                <span class="text-stone-400 text-xs">In Progress</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-stone-400 text-xs">
                            {{ $attempt->submitted_at ? $attempt->submitted_at->diffForHumans() : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
