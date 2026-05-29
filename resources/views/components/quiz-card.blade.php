@props(['quiz'])

<div class="bg-white rounded-xl border border-stone-200 shadow-sm card-hover relative overflow-hidden flex flex-col">
    {{-- Top accent stripe --}}
    <div class="h-1.5 w-full" style="background: linear-gradient(90deg, #7C3AED, #EAB308);"></div>

    <div class="p-5 flex flex-col flex-1">
        {{-- Status badge --}}
        <div class="flex items-start justify-between mb-3">
            <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-md
                {{ $quiz->is_published
                    ? 'bg-quiz-yellow-lt text-amber-700'
                    : 'bg-stone-100 text-stone-500' }}">
                <span class="w-1.5 h-1.5 rounded-full inline-block
                    {{ $quiz->is_published ? 'bg-quiz-amber' : 'bg-stone-400' }}"></span>
                {{ $quiz->is_published ? 'Published' : 'Draft' }}
            </span>
            <span class="text-xs text-stone-400">{{ $quiz->created_at->format('M d, Y') }}</span>
        </div>

        {{-- Title --}}
        <h3 class="font-display text-lg text-stone-900 mb-2 leading-snug line-clamp-2">
            {{ $quiz->title }}
        </h3>

        {{-- Description --}}
        @if($quiz->description)
            <p class="text-sm text-stone-500 mb-4 line-clamp-2 leading-relaxed">
                {{ $quiz->description }}
            </p>
        @endif

        {{-- Meta --}}
        <div class="flex items-center gap-4 text-xs text-stone-500 mb-5 mt-auto">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-quiz-violet" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $quiz->questions_count ?? $quiz->questions->count() }} Questions
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-quiz-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $quiz->attempts_count ?? $quiz->attempts->count() }} Attempts
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                {{ $quiz->total_marks }} Marks
            </span>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 pt-4 border-t border-stone-100">
            <a href="{{ route('attempts.create', $quiz) }}"
               class="flex-1 btn-yellow text-sm font-semibold text-center py-2 rounded-lg">
                Attempt Quiz
            </a>
            <a href="{{ route('quizzes.edit', $quiz) }}"
               class="px-3 py-2 rounded-lg border border-stone-200 text-stone-500 hover:text-violet-700 hover:border-violet-300 hover:bg-violet-50 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </a>
            <a href="{{ route('quizzes.leaderboard', $quiz) }}"
               class="px-3 py-2 rounded-lg border border-stone-200 text-stone-500 hover:text-yellow-600 hover:border-yellow-300 hover:bg-yellow-50 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </a>
        </div>
    </div>
</div>
