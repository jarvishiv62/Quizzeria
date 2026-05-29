@extends('layouts.app')
@section('title', $quiz->title)
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="relative bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8 px-8 py-8">
        <x-sunflower class="top-[-20px] right-[-20px]" :size="170" :opacity="0.08" />
        <div class="relative z-10">
            <a href="{{ route('quizzes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-stone-400 hover:text-violet-600 mb-4 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Dashboard
            </a>
            <h1 class="font-display text-3xl text-stone-900 mb-2">{{ $quiz->title }}</h1>
            @if($quiz->description)<p class="text-stone-500 text-sm mb-4">{{ $quiz->description }}</p>@endif
            <div class="flex flex-wrap gap-3">
                <span class="text-xs bg-violet-50 text-violet-700 border border-violet-200 px-3 py-1.5 rounded-lg font-medium">{{ $quiz->questions_count }} Questions</span>
                <span class="text-xs bg-yellow-50 text-amber-700 border border-yellow-200 px-3 py-1.5 rounded-lg font-medium">{{ $quiz->total_marks }} Total Marks</span>
                <span class="text-xs bg-stone-50 text-stone-600 border border-stone-200 px-3 py-1.5 rounded-lg font-medium">{{ $quiz->attempts_count }} Attempts</span>
            </div>
        </div>
    </div>
    <div class="flex gap-3 flex-wrap">
        <a href="{{ route('attempts.create', $quiz) }}" class="btn-yellow px-6 py-2.5 rounded-xl font-semibold text-sm">Attempt Quiz</a>
        <a href="{{ route('quizzes.edit', $quiz) }}" class="btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm">Edit Quiz</a>
        <a href="{{ route('quizzes.leaderboard', $quiz) }}" class="border border-stone-300 text-stone-600 hover:bg-stone-50 px-6 py-2.5 rounded-xl font-semibold text-sm transition-all">Leaderboard</a>
    </div>
</div>
@endsection
