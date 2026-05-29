@extends('layouts.app')
@section('title', 'Question ' . ($order + 1))

@section('content')
<div class="min-h-[85vh] flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl">

        {{-- Progress + Meta --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2 text-sm">
                <span class="text-stone-500 font-medium">{{ $attempt->guest_name }}</span>
                <span class="text-stone-400">Question {{ $order + 1 }} of {{ $total }}</span>
            </div>
            {{-- Progress bar --}}
            <div class="h-2 bg-stone-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500"
                     style="width: {{ round((($order + 1) / $total) * 100) }}%; background: linear-gradient(90deg, #7C3AED, #EAB308);">
                </div>
            </div>
            <div class="flex items-center justify-between mt-1.5">
                <span class="text-xs text-violet-600 font-semibold">{{ round((($order + 1) / $total) * 100) }}% complete</span>
                <span class="text-xs text-stone-400">{{ $question->marks }} mark{{ $question->marks !== 1 ? 's' : '' }}</span>
            </div>
        </div>

        {{-- Question Card --}}
        <div class="relative bg-white rounded-2xl border border-stone-200 shadow-md overflow-hidden">
            <div class="h-1.5 w-full" style="background: linear-gradient(90deg, #7C3AED, #EAB308);"></div>

            {{-- Sunflower bottom-left decoration --}}
            <x-sunflower class="bottom-[-40px] left-[-30px]" :size="160" :opacity="0.07" />

            <div class="relative z-10 p-6 sm:p-8">

                {{-- Question type badge --}}
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md bg-violet-50 text-violet-700">
                        @php $typeLabels = ['binary'=>'Binary','single_choice'=>'Single Choice','multiple_choice'=>'Multiple Choice','number_input'=>'Number Input','text_input'=>'Text Input']; @endphp
                        {{ $typeLabels[$question->type] ?? $question->type }}
                    </span>
                    <span class="text-xs text-stone-400">Q{{ $order + 1 }}</span>
                </div>

                {{-- Question Content --}}
                <div class="mb-5">
                    <div class="text-stone-900 text-base sm:text-lg leading-relaxed font-medium">
                        {!! $question->content !!}
                    </div>
                </div>

                {{-- Media --}}
                @if($question->image_path)
                <div class="mb-5">
                    <img src="{{ Storage::url($question->image_path) }}"
                         alt="Question image"
                         class="max-h-64 w-auto rounded-xl border border-stone-200 object-contain" />
                </div>
                @endif

                @if($question->video_url)
                <div class="mb-5 aspect-video w-full rounded-xl overflow-hidden border border-stone-200">
                    <iframe src="{{ $question->getEmbedVideoUrl() }}"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                </div>
                @endif

                {{-- Answer Form --}}
                <form action="{{ route('attempts.answer', ['attempt' => $attempt->id, 'order' => $order]) }}"
                      method="POST" id="answer-form">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $question->id }}" />

                    @php $existingOptionIds = $existingAnswer?->selected_option_ids ?? []; @endphp

                    {{-- BINARY --}}
                    @if($question->type === 'binary')
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @foreach($question->options as $option)
                        <label class="cursor-pointer">
                            <input type="radio"
                                   name="selected_option_ids[]"
                                   value="{{ $option->id }}"
                                   class="sr-only peer"
                                   {{ in_array($option->id, $existingOptionIds) ? 'checked' : '' }} />
                            <div class="option-card option-card-yellow text-center py-6 px-4 font-semibold text-stone-700 text-lg peer-checked:selected
                                        flex flex-col items-center gap-2">
                                <span class="text-3xl">{{ $option->label === 'Yes' || $option->label === 'True' ? '✅' : '❌' }}</span>
                                {{ $option->label }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    {{-- SINGLE CHOICE --}}
                    @if($question->type === 'single_choice')
                    <div class="space-y-3 mb-6">
                        @foreach($question->options as $option)
                        <label class="cursor-pointer block">
                            <input type="radio"
                                   name="selected_option_ids[]"
                                   value="{{ $option->id }}"
                                   class="sr-only peer"
                                   {{ in_array($option->id, $existingOptionIds) ? 'checked' : '' }} />
                            <div class="option-card peer-checked:selected px-4 py-3.5 flex items-center gap-4">
                                <div class="w-5 h-5 rounded-full border-2 border-stone-300 flex items-center justify-center shrink-0 peer-checked:border-violet-500 transition-all">
                                    <div class="w-2.5 h-2.5 rounded-full bg-violet-600 hidden peer-checked:block"></div>
                                </div>
                                <div class="flex items-center gap-3 flex-1">
                                    @if($option->image_path)
                                    <img src="{{ Storage::url($option->image_path) }}" class="w-12 h-12 object-cover rounded-lg" />
                                    @endif
                                    <span class="text-stone-700 text-sm font-medium">{{ $option->label }}</span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    {{-- MULTIPLE CHOICE --}}
                    @if($question->type === 'multiple_choice')
                    <p class="text-xs text-stone-400 mb-3">Select all that apply.</p>
                    <div class="space-y-3 mb-6">
                        @foreach($question->options as $option)
                        <label class="cursor-pointer block">
                            <input type="checkbox"
                                   name="selected_option_ids[]"
                                   value="{{ $option->id }}"
                                   class="sr-only peer"
                                   {{ in_array($option->id, $existingOptionIds) ? 'checked' : '' }} />
                            <div class="option-card peer-checked:selected px-4 py-3.5 flex items-center gap-4">
                                <div class="w-5 h-5 rounded border-2 border-stone-300 flex items-center justify-center shrink-0 peer-checked:border-violet-500 peer-checked:bg-violet-600 transition-all">
                                    <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-3 flex-1">
                                    @if($option->image_path)
                                    <img src="{{ Storage::url($option->image_path) }}" class="w-12 h-12 object-cover rounded-lg" />
                                    @endif
                                    <span class="text-stone-700 text-sm font-medium">{{ $option->label }}</span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    {{-- NUMBER INPUT --}}
                    @if($question->type === 'number_input')
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-stone-600 mb-2">Your Answer</label>
                        <input type="number"
                               name="number_answer"
                               step="any"
                               placeholder="Enter a number..."
                               value="{{ $existingAnswer?->number_answer }}"
                               class="w-full sm:w-64 px-4 py-3 rounded-xl border-2 border-stone-200 text-stone-900 text-base focus-ring transition-all" />
                    </div>
                    @endif

                    {{-- TEXT INPUT --}}
                    @if($question->type === 'text_input')
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-stone-600 mb-2">Your Answer</label>
                        <input type="text"
                               name="text_answer"
                               placeholder="Type your answer..."
                               value="{{ $existingAnswer?->text_answer }}"
                               class="w-full px-4 py-3 rounded-xl border-2 border-stone-200 text-stone-900 text-base focus-ring transition-all" />
                    </div>
                    @endif

                    {{-- Navigation Buttons --}}
                    <div class="flex items-center justify-between pt-4 border-t border-stone-100">
                        {{-- Previous --}}
                        @if($order > 0)
                        <a href="{{ route('attempts.question', ['attempt' => $attempt->id, 'order' => $order - 1]) }}"
                           class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-stone-300 text-stone-600 hover:bg-stone-50 text-sm font-medium transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Previous
                        </a>
                        @else
                        <div></div>
                        @endif

                        {{-- Next / Submit --}}
                        @if($order + 1 < $total)
                        <button type="submit"
                                class="btn-primary flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        @else
                        <button type="submit"
                                formaction="{{ route('attempts.submit', $attempt) }}"
                                class="btn-yellow flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm"
                                onclick="return confirm('Submit your quiz? You cannot go back after this.')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Submit Quiz
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Quiz title below card --}}
        <p class="text-center text-xs text-stone-400 mt-4">{{ $attempt->quiz->title }}</p>
    </div>
</div>

<style>
    /* Make peer-checked state work visually for custom option cards */
    input.sr-only:checked + .option-card { border-color: #7C3AED; background: #EDE9FE; }
    input.sr-only:checked + .option-card-yellow { border-color: #EAB308; background: #FEF9C3; }
    /* Inner radio dot */
    label:has(input[type=radio]:checked) .option-card .w-2\.5 { display: block; }
    label:has(input[type=radio]:checked) .option-card { border-color: #7C3AED; background: #EDE9FE; }
    label:has(input[type=checkbox]:checked) .option-card { border-color: #7C3AED; background: #EDE9FE; }
</style>
@endsection
