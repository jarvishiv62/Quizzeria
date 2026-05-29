@extends('layouts.app')
@section('title', 'Quiz Result')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Score Hero --}}
    <div class="relative bg-white rounded-2xl border border-stone-200 shadow-md overflow-hidden mb-8 text-center px-8 py-10">
        <div class="h-2 w-full absolute top-0 left-0" style="background: linear-gradient(90deg, #7C3AED, #EAB308, #F43F5E);"></div>

        {{-- Sunflower behind score ring --}}
        <x-sunflower class="top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" :size="280" :opacity="0.06" />

        <div class="relative z-10">
            {{-- Score Ring --}}
            <div class="relative inline-block mb-5">
                @php
                    $pct = $attempt->getPercentage();
                    $r = 54;
                    $circ = 2 * M_PI * $r;
                    $dash = round(($pct / 100) * $circ, 2);
                    $gap  = round($circ - $dash, 2);
                    $passed = $attempt->isPassed();
                @endphp
                <svg width="160" height="160" viewBox="0 0 160 160" class="rotate-[-90deg]">
                    <circle cx="80" cy="80" r="{{ $r }}" fill="none" stroke="#F1F5F9" stroke-width="12"/>
                    <circle cx="80" cy="80" r="{{ $r }}" fill="none"
                            stroke="{{ $passed ? '#EAB308' : '#F43F5E' }}"
                            stroke-width="12"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $dash }} {{ $gap }}"
                            style="transition: stroke-dasharray 1s ease;"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="font-display text-3xl font-bold text-stone-900">{{ $pct }}%</span>
                    <span class="text-xs font-medium {{ $passed ? 'text-amber-600' : 'text-rose-500' }}">
                        {{ $passed ? 'Passed ✓' : 'Failed ✗' }}
                    </span>
                </div>
            </div>

            <h1 class="font-display text-2xl text-stone-900 mb-1">{{ $attempt->guest_name }}'s Result</h1>
            <p class="text-stone-500 text-sm mb-5">{{ $attempt->quiz->title }}</p>

            <div class="flex flex-wrap justify-center gap-4 mb-6">
                <div class="bg-violet-50 border border-violet-200 rounded-xl px-5 py-3 text-center">
                    <p class="text-2xl font-bold text-violet-700">{{ $attempt->score }} / {{ $attempt->total_marks }}</p>
                    <p class="text-xs text-stone-500 mt-0.5">Score</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-3 text-center">
                    @php
                        $correct = $attempt->answers->where('is_correct', true)->count();
                        $total   = $attempt->answers->count();
                    @endphp
                    <p class="text-2xl font-bold text-amber-700">{{ $correct }} / {{ $total }}</p>
                    <p class="text-xs text-stone-500 mt-0.5">Correct</p>
                </div>
                @if($attempt->getTimeTaken())
                <div class="bg-stone-50 border border-stone-200 rounded-xl px-5 py-3 text-center">
                    <p class="text-2xl font-bold text-stone-700">{{ $attempt->getTimeTaken() }}</p>
                    <p class="text-xs text-stone-500 mt-0.5">Time Taken</p>
                </div>
                @endif
            </div>

            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('attempts.create', $attempt->quiz) }}"
                   class="btn-yellow px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2">
                    🔁 Retry Quiz
                </a>
                <a href="{{ route('quizzes.index') }}"
                   class="border border-stone-300 text-stone-600 hover:bg-stone-50 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all">
                    ← Dashboard
                </a>
                <a href="{{ route('quizzes.leaderboard', $attempt->quiz) }}"
                   class="btn-primary px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2">
                    🏆 Leaderboard
                </a>
            </div>
        </div>
    </div>

    {{-- Answer Breakdown --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-stone-100">
            <h2 class="font-display text-lg text-stone-800">Answer Breakdown</h2>
        </div>
        <div class="divide-y divide-stone-100">
            @foreach($attempt->answers->sortBy('question.order') as $index => $answer)
            @php $q = $answer->question; @endphp
            <div class="px-6 py-4 {{ $answer->is_correct ? 'bg-yellow-50/40' : 'bg-rose-50/30' }}">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $answer->is_correct ? 'bg-quiz-yellow-lt text-amber-700' : 'bg-rose-100 text-rose-600' }}">
                        {{ $answer->is_correct ? '✓' : '✗' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-stone-800 mb-1">
                            Q{{ $index + 1 }}: {!! $q ? Str::limit(strip_tags($q->content), 120) : 'Question deleted' !!}
                        </p>

                        {{-- User Answer --}}
                        <div class="text-xs text-stone-500 mb-1">
                            <span class="font-semibold">Your answer: </span>
                            @if($q && $q->hasOptions())
                                @php
                                    $selectedIds = $answer->selected_option_ids ?? [];
                                    $selectedLabels = $q->options->whereIn('id', $selectedIds)->pluck('label');
                                @endphp
                                {{ $selectedLabels->join(', ') ?: '(no answer)' }}
                            @elseif($q && $q->type === 'number_input')
                                {{ $answer->number_answer ?? '(no answer)' }}
                            @elseif($q && $q->type === 'text_input')
                                {{ $answer->text_answer ?? '(no answer)' }}
                            @endif
                        </div>

                        {{-- Correct Answer (only if wrong) --}}
                        @if(!$answer->is_correct && $q)
                        <div class="text-xs text-stone-500">
                            <span class="font-semibold text-emerald-600">Correct: </span>
                            @if($q->hasOptions())
                                {{ $q->options->where('is_correct', true)->pluck('label')->join(', ') }}
                            @elseif($q->type === 'number_input')
                                {{ $q->correct_number }}
                            @elseif($q->type === 'text_input')
                                {{ $q->correct_text }}
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="text-sm font-bold {{ $answer->is_correct ? 'text-amber-600' : 'text-rose-500' }}">
                            +{{ $answer->marks_awarded }}
                        </span>
                        <p class="text-xs text-stone-400">/ {{ $q?->marks ?? '?' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Leaderboard Preview --}}
    @if($leaderboard->count())
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
            <h2 class="font-display text-lg text-stone-800">🏆 Top Scorers</h2>
            <a href="{{ route('quizzes.leaderboard', $attempt->quiz) }}"
               class="text-xs text-violet-600 hover:text-violet-800 font-medium">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-500 text-xs font-semibold uppercase tracking-wide">
                        <th class="text-start px-6 py-3">Rank</th>
                        <th class="text-start px-6 py-3">Name</th>
                        <th class="text-start px-6 py-3">Score</th>
                        <th class="text-start px-6 py-3">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($leaderboard as $i => $lb)
                    <tr class="transition-colors {{ $lb->id === $attempt->id ? 'bg-yellow-50 border-l-4 border-l-yellow-400' : 'hover:bg-stone-50' }}">
                        <td class="px-6 py-3">
                            @if($i === 0) <span class="text-xl">🥇</span>
                            @elseif($i === 1) <span class="text-xl">🥈</span>
                            @elseif($i === 2) <span class="text-xl">🥉</span>
                            @else <span class="text-stone-400 font-medium text-xs">#{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 font-medium text-stone-800">
                            {{ $lb->guest_name }}
                            @if($lb->id === $attempt->id)
                            <span class="ms-1 text-xs text-amber-600 font-semibold">(You)</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 font-semibold text-stone-700">{{ $lb->score }} / {{ $lb->total_marks }}</td>
                        <td class="px-6 py-3">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md
                                {{ $lb->getPercentage() >= 50 ? 'bg-yellow-50 text-amber-700' : 'bg-rose-50 text-rose-600' }}">
                                {{ $lb->getPercentage() }}%
                            </span>
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
