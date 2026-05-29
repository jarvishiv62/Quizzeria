@extends('layouts.app')
@section('title', 'Create Quiz')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Page Header --}}
    <div class="relative bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8 px-8 py-8">
        <x-sunflower class="top-[-30px] right-[-30px]" :size="180" :opacity="0.09" />
        <div class="relative z-10">
            <a href="{{ route('quizzes.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-stone-400 hover:text-violet-600 mb-4 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="font-display text-3xl text-stone-900 mb-1">Create New Quiz</h1>
            <p class="text-stone-500 text-sm">Add questions, set correct answers, and publish your quiz.</p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl p-4">
        <p class="font-semibold text-rose-700 text-sm mb-2">Please fix the following errors:</p>
        <ul class="list-disc list-inside text-sm text-rose-600 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('quizzes.store') }}" method="POST" enctype="multipart/form-data"
          x-data="quizBuilder()" x-cloak>
        @csrf

        {{-- Quiz Info Card --}}
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-6 mb-6">
            <h2 class="font-semibold text-stone-800 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-md gradient-badge flex items-center justify-center text-white text-xs">1</span>
                Quiz Details
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Quiz Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="e.g. General Knowledge Quiz 2024"
                           class="w-full px-4 py-2.5 rounded-lg border border-stone-300 text-sm focus-ring transition-all"
                           required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3"
                              placeholder="Brief description of what this quiz covers..."
                              class="w-full px-4 py-2.5 rounded-lg border border-stone-300 text-sm focus-ring transition-all resize-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Questions Builder --}}
        <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-stone-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md gradient-badge flex items-center justify-center text-white text-xs">2</span>
                    Questions
                    <span class="text-xs font-normal text-stone-400 bg-stone-100 px-2 py-0.5 rounded-full" x-text="questions.length + ' added'"></span>
                </h2>
                <button type="button" @click="addQuestion()"
                        class="btn-yellow text-sm font-semibold px-4 py-2 rounded-lg flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Question
                </button>
            </div>

            <template x-if="questions.length === 0">
                <div class="text-center py-12 border-2 border-dashed border-stone-200 rounded-xl">
                    <p class="text-3xl mb-2">🌻</p>
                    <p class="text-stone-400 text-sm">No questions yet. Click "Add Question" to start building.</p>
                </div>
            </template>

            <div class="space-y-5">
                <template x-for="(q, qi) in questions" :key="q.id">
                    <div class="border border-stone-200 rounded-xl overflow-hidden">
                        {{-- Question Header --}}
                        <div class="flex items-center justify-between bg-stone-50 px-5 py-3 border-b border-stone-200">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-violet-100 text-violet-700 text-xs font-bold flex items-center justify-center"
                                      x-text="qi + 1"></span>
                                <span class="text-sm font-semibold text-stone-700">Question</span>
                                <span class="text-xs text-stone-400" x-text="'(' + getTypeLabel(q.type) + ')'"></span>
                            </div>
                            <button type="button" @click="removeQuestion(qi)"
                                    class="text-stone-400 hover:text-rose-500 transition-colors text-xs flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Remove
                            </button>
                        </div>

                        <div class="p-5 space-y-4">
                            {{-- Type Selector --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($questionTypes as $typeKey => $typeLabel)
                                <label class="cursor-pointer">
                                    <input type="radio"
                                           :name="'questions[' + qi + '][type]'"
                                           value="{{ $typeKey }}"
                                           x-model="q.type"
                                           @change="onTypeChange(q)"
                                           class="sr-only peer" />
                                    <div class="text-center border-2 border-stone-200 rounded-lg py-2 px-2 text-xs font-medium text-stone-600
                                                peer-checked:border-violet-500 peer-checked:bg-violet-50 peer-checked:text-violet-700 transition-all cursor-pointer hover:border-violet-300">
                                        {{ $typeLabel }}
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            {{-- Question Content --}}
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1.5">Question Content <span class="text-rose-500">*</span></label>
                                <textarea :name="'questions[' + qi + '][content]'"
                                          x-model="q.content"
                                          rows="3"
                                          placeholder="Enter your question here. HTML is supported."
                                          class="w-full px-4 py-2.5 rounded-lg border border-stone-300 text-sm focus-ring transition-all resize-none"
                                          required></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                {{-- Marks --}}
                                <div>
                                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Marks</label>
                                    <input type="number"
                                           :name="'questions[' + qi + '][marks]'"
                                           x-model="q.marks"
                                           min="1" value="1"
                                           class="w-full px-4 py-2.5 rounded-lg border border-stone-300 text-sm focus-ring" />
                                </div>
                                {{-- Image Upload --}}
                                <div>
                                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Image (optional)</label>
                                    <input type="file"
                                           :name="'questions[' + qi + '][image]'"
                                           accept="image/*"
                                           class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" />
                                </div>
                                {{-- Video URL --}}
                                <div>
                                    <label class="block text-sm font-medium text-stone-700 mb-1.5">YouTube URL (optional)</label>
                                    <input type="url"
                                           :name="'questions[' + qi + '][video_url]'"
                                           placeholder="https://youtube.com/..."
                                           class="w-full px-4 py-2.5 rounded-lg border border-stone-300 text-sm focus-ring" />
                                </div>
                            </div>

                            {{-- Binary-specific --}}
                            <template x-if="q.type === 'binary'">
                                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                    <p class="text-sm font-medium text-amber-800 mb-3">Binary Question Settings</p>
                                    <div class="flex flex-wrap gap-4">
                                        <div>
                                            <label class="text-xs font-medium text-stone-600 mb-1 block">Option Style</label>
                                            <select :name="'questions[' + qi + '][binary_style]'"
                                                    x-model="q.binary_style"
                                                    class="px-3 py-1.5 rounded-lg border border-stone-300 text-sm focus-ring">
                                                <option value="yes_no">Yes / No</option>
                                                <option value="true_false">True / False</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-stone-600 mb-1 block">Correct Answer</label>
                                            <select :name="'questions[' + qi + '][binary_correct]'"
                                                    x-model="q.binary_correct"
                                                    class="px-3 py-1.5 rounded-lg border border-stone-300 text-sm focus-ring">
                                                <option value="0" x-text="q.binary_style === 'yes_no' ? 'Yes' : 'True'"></option>
                                                <option value="1" x-text="q.binary_style === 'yes_no' ? 'No' : 'False'"></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Single / Multiple Choice Options --}}
                            <template x-if="q.type === 'single_choice' || q.type === 'multiple_choice'">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm font-medium text-stone-700">
                                            Answer Options
                                            <span class="text-xs text-stone-400 font-normal ms-1">
                                                (<span x-text="q.type === 'single_choice' ? 'select one correct' : 'select all correct'"></span>)
                                            </span>
                                        </label>
                                        <button type="button" @click="addOption(q)"
                                                class="text-xs text-violet-600 hover:text-violet-800 font-medium flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Option
                                        </button>
                                    </div>
                                    <template x-for="(opt, oi) in q.options" :key="opt.id">
                                        <div class="flex items-start gap-3 bg-stone-50 p-3 rounded-lg border border-stone-200">
                                            {{-- Correct toggle --}}
                                            <template x-if="q.type === 'single_choice'">
                                                <input type="radio"
                                                       :name="'questions[' + qi + '][options][' + oi + '][is_correct]'"
                                                       value="1"
                                                       @change="setSingleCorrect(q, oi)"
                                                       :checked="opt.is_correct"
                                                       class="mt-2.5 accent-violet-600" />
                                            </template>
                                            <template x-if="q.type === 'multiple_choice'">
                                                <input type="checkbox"
                                                       :name="'questions[' + qi + '][options][' + oi + '][is_correct]'"
                                                       value="1"
                                                       x-model="opt.is_correct"
                                                       class="mt-2.5 accent-violet-600" />
                                            </template>
                                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <input type="text"
                                                       :name="'questions[' + qi + '][options][' + oi + '][label]'"
                                                       x-model="opt.label"
                                                       placeholder="Option text"
                                                       class="px-3 py-2 rounded-lg border border-stone-300 text-sm focus-ring" />
                                                <input type="file"
                                                       :name="'questions[' + qi + '][options][' + oi + '][image]'"
                                                       accept="image/*"
                                                       class="text-xs text-stone-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-yellow-50 file:text-amber-700" />
                                            </div>
                                            <button type="button" @click="removeOption(q, oi)"
                                                    class="text-stone-300 hover:text-rose-400 mt-1.5 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Number Input correct answer --}}
                            <template x-if="q.type === 'number_input'">
                                <div>
                                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Correct Number Answer <span class="text-rose-500">*</span></label>
                                    <input type="number"
                                           :name="'questions[' + qi + '][correct_number]'"
                                           step="any"
                                           placeholder="e.g. 42 or 3.14"
                                           class="w-48 px-4 py-2.5 rounded-lg border border-stone-300 text-sm focus-ring" />
                                </div>
                            </template>

                            {{-- Text Input correct answer --}}
                            <template x-if="q.type === 'text_input'">
                                <div>
                                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Correct Text Answer <span class="text-rose-500">*</span></label>
                                    <input type="text"
                                           :name="'questions[' + qi + '][correct_text]'"
                                           placeholder="Exact correct answer (case-insensitive)"
                                           class="w-full px-4 py-2.5 rounded-lg border border-stone-300 text-sm focus-ring" />
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Add more --}}
            <template x-if="questions.length > 0">
                <div class="mt-4 text-center">
                    <button type="button" @click="addQuestion()"
                            class="text-sm text-violet-600 hover:text-violet-800 font-medium flex items-center gap-1.5 mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Another Question
                    </button>
                </div>
            </template>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('quizzes.index') }}"
               class="px-5 py-2.5 rounded-lg border border-stone-300 text-stone-600 hover:bg-stone-50 text-sm font-medium transition-all">
                Cancel
            </a>
            <button type="submit"
                    class="btn-primary px-8 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Quiz
            </button>
        </div>
    </form>
</div>

<script>
function quizBuilder() {
    return {
        questions: [],
        types: {
            binary: 'Binary (Yes/No)',
            single_choice: 'Single Choice',
            multiple_choice: 'Multiple Choice',
            number_input: 'Number Input',
            text_input: 'Text Input',
        },
        nextId: 1,
        addQuestion() {
            this.questions.push({
                id: this.nextId++,
                type: 'single_choice',
                content: '',
                marks: 1,
                binary_style: 'yes_no',
                binary_correct: '0',
                options: [],
            });
        },
        removeQuestion(index) {
            this.questions.splice(index, 1);
        },
        getTypeLabel(type) {
            return this.types[type] || type;
        },
        onTypeChange(q) {
            if (q.type === 'single_choice' || q.type === 'multiple_choice') {
                if (q.options.length === 0) {
                    this.addOption(q);
                    this.addOption(q);
                }
            }
        },
        addOption(q) {
            q.options.push({ id: Date.now() + Math.random(), label: '', is_correct: false });
        },
        removeOption(q, index) {
            q.options.splice(index, 1);
        },
        setSingleCorrect(q, selectedIndex) {
            q.options.forEach((opt, i) => { opt.is_correct = i === selectedIndex; });
        },
    };
}
</script>
@endsection
