@extends('layouts.app')
@section('title', 'Leaderboard — ' . $quiz->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="relative bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8 px-8 py-8">
        <x-sunflower class="top-[-30px] right-[-20px]" :size="200" :opacity="0.09" />
        <x-sunflower class="bottom-[-50px] left-[-40px]" :size="150" :opacity="0.06" />
        <div class="relative z-10">
            <a href="{{ route('quizzes.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-stone-400 hover:text-violet-600 mb-4 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </a>
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 bg-yellow-50 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-md mb-3">
                        🏆 Leaderboard
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl text-stone-900 mb-1">{{ $quiz->title }}</h1>
                    <p class="text-stone-500 text-sm">{{ $attempts->total() }} total attempt{{ $attempts->total() !== 1 ? 's' : '' }}</p>
                </div>
                <a href="{{ route('attempts.create', $quiz) }}"
                   class="btn-yellow px-5 py-2.5 rounded-xl font-semibold text-sm self-start">
                    Attempt This Quiz
                </a>
            </div>
        </div>
    </div>

    {{-- Top 3 Podium --}}
    @php $top3 = $attempts->getCollection()->take(3); @endphp
    @if($top3->count() >= 1)
    <div class="grid grid-cols-3 gap-3 mb-8 items-end">

        {{-- 2nd place --}}
        <div class="flex flex-col items-center">
            @if($top3->count() >= 2)
            <div class="w-11 h-11 rounded-full gradient-badge flex items-center justify-center text-white font-bold text-base mb-1.5 shadow">
                {{ strtoupper(substr($top3[1]->guest_name, 0, 1)) }}
            </div>
            <p class="text-xs font-semibold text-stone-700 text-center truncate w-full px-1 mb-0.5">{{ $top3[1]->guest_name }}</p>
            <p class="text-xs text-stone-400 mb-2">{{ $top3[1]->score }} pts</p>
            <div class="w-full bg-stone-100 border border-stone-200 rounded-t-xl flex items-center justify-center h-20">
                <span class="text-3xl">🥈</span>
            </div>
            @endif
        </div>

        {{-- 1st place --}}
        <div class="flex flex-col items-center">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold mb-1.5 shadow-lg"
                 style="background: linear-gradient(135deg,#EAB308,#F59E0B);">
                {{ strtoupper(substr($top3[0]->guest_name, 0, 1)) }}
            </div>
            <p class="text-sm font-bold text-stone-800 text-center truncate w-full px-1 mb-0.5">{{ $top3[0]->guest_name }}</p>
            <p class="text-xs font-semibold text-amber-600 mb-2">{{ $top3[0]->score }} pts · {{ $top3[0]->getPercentage() }}%</p>
            <div class="w-full rounded-t-xl flex items-center justify-center h-28"
                 style="background: linear-gradient(135deg,#FEF9C3,#FDE68A); border:1px solid #FDE68A;">
                <span class="text-4xl">🥇</span>
            </div>
        </div>

        {{-- 3rd place --}}
        <div class="flex flex-col items-center">
            @if($top3->count() >= 3)
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-800 text-sm font-bold mb-1.5">
                {{ strtoupper(substr($top3[2]->guest_name, 0, 1)) }}
            </div>
            <p class="text-xs font-semibold text-stone-700 text-center truncate w-full px-1 mb-0.5">{{ $top3[2]->guest_name }}</p>
            <p class="text-xs text-stone-400 mb-2">{{ $top3[2]->score }} pts</p>
            <div class="w-full bg-amber-50 border border-amber-200 rounded-t-xl flex items-center justify-center h-14">
                <span class="text-2xl">🥉</span>
            </div>
            @endif
        </div>

    </div>
    @endif

    {{-- Full Rankings Table --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100">
            <h2 class="font-semibold text-stone-800">All Rankings</h2>
        </div>

        @if($attempts->isEmpty())
        <div class="text-center py-16">
            <p class="text-4xl mb-3">🌻</p>
            <p class="text-stone-400 text-sm">No attempts yet. Be the first!</p>
            <a href="{{ route('attempts.create', $quiz) }}"
               class="mt-4 inline-block btn-yellow px-5 py-2.5 rounded-xl font-semibold text-sm">
                Attempt Now
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 text-stone-500 text-xs font-semibold uppercase tracking-wide">
                        <th class="text-start px-6 py-3">Rank</th>
                        <th class="text-start px-6 py-3">Name</th>
                        <th class="text-start px-6 py-3">Score</th>
                        <th class="text-start px-6 py-3">Result</th>
                        <th class="text-start px-6 py-3">Time</th>
                        <th class="text-start px-6 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($attempts as $i => $a)
                    @php $rank = (($attempts->currentPage() - 1) * $attempts->perPage()) + $i + 1; @endphp
                    <tr class="hover:bg-stone-50 transition-colors
                        {{ $rank === 1 ? 'border-l-4 border-l-yellow-400 bg-yellow-50/40' : '' }}
                        {{ $rank === 2 ? 'border-l-4 border-l-stone-300' : '' }}
                        {{ $rank === 3 ? 'border-l-4 border-l-amber-400' : '' }}">
                        <td class="px-6 py-3.5">
                            @if($rank === 1) <span class="text-xl">🥇</span>
                            @elseif($rank === 2) <span class="text-xl">🥈</span>
                            @elseif($rank === 3) <span class="text-xl">🥉</span>
                            @else <span class="text-stone-400 font-medium">#{{ $rank }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full gradient-badge flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($a->guest_name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-stone-800">{{ $a->guest_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-stone-800">
                            {{ $a->score }} / {{ $a->total_marks }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md
                                {{ $a->getPercentage() >= 50 ? 'bg-yellow-50 text-amber-700' : 'bg-rose-50 text-rose-600' }}">
                                {{ $a->getPercentage() }}%
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-stone-500 text-xs">{{ $a->getTimeTaken() ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-stone-400 text-xs">
                            {{ $a->submitted_at?->format('M d, Y') ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
        <div class="px-6 py-4 border-t border-stone-100">
            {{ $attempts->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
