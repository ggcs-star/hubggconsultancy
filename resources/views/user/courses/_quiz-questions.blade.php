{{-- Shared question-rendering block for both the mid-video checkpoint modal
     and the module-quiz pane — no per-question submit button; the caller
     provides one "Submit Answers" button that batches every question in
     $questionsExpr via submitAllAnswers(). --}}
<template x-for="question in {{ $questionsExpr }}" :key="question.id">
    <div class="rounded-lg border border-app-border p-4">
        <p class="text-sm font-medium text-secondary-dark">
            <span x-text="question.text"></span>
            <span class="font-normal text-secondary" x-text="'(' + question.points + ' ' + (question.points === 1 ? 'pt' : 'pts') + ')'"></span>
        </p>

        <template x-if="!answers[question.id]?.submitted">
            <div class="mt-3 space-y-3">
                <template x-if="question.type === 'radio'">
                    <div class="space-y-1.5">
                        <template x-for="option in question.options" :key="option.id">
                            <label class="flex items-center gap-2 text-sm text-secondary-dark">
                                <input type="radio" :name="'q-' + question.id" :value="option.id"
                                    x-on:change="setSingleSelection(question.id, option.id)"
                                    class="border-app-border text-primary focus:ring-primary">
                                <span x-text="option.text"></span>
                            </label>
                        </template>
                    </div>
                </template>

                <template x-if="question.type === 'checkbox'">
                    <div class="space-y-1.5">
                        <template x-for="option in question.options" :key="option.id">
                            <label class="flex items-center gap-2 text-sm text-secondary-dark">
                                <input type="checkbox" :value="option.id"
                                    x-on:change="toggleSelection(question.id, option.id, $event.target.checked)"
                                    class="rounded border-app-border text-primary focus:ring-primary">
                                <span x-text="option.text"></span>
                            </label>
                        </template>
                    </div>
                </template>

                <template x-if="question.type === 'text'">
                    <textarea rows="2" x-on:input="setText(question.id, $event.target.value)"
                        class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Type your answer..."></textarea>
                </template>
            </div>
        </template>

        <template x-if="answers[question.id]?.submitted">
            <div class="mt-3">
                <template x-if="question.type === 'text' && answers[question.id].pointsAwarded === null">
                    <p class="inline-flex items-center gap-1.5 text-xs font-medium text-secondary">
                        <x-icon name="help-circle" class="w-3.5 h-3.5" />
                        Submitted — pending manual review.
                    </p>
                </template>

                {{-- Text questions can be graded with partial credit, so they get their own "X/Y points" wording instead of a binary Correct/Not quite. --}}
                <template x-if="question.type === 'text' && answers[question.id].pointsAwarded !== null">
                    <p class="inline-flex items-center gap-1.5 text-xs font-medium"
                        :class="answers[question.id].pointsAwarded >= question.points ? 'text-success' : (answers[question.id].pointsAwarded > 0 ? 'text-warning' : 'text-danger')">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                        <span>Graded: <span x-text="answers[question.id].pointsAwarded"></span>/<span x-text="question.points"></span> points</span>
                    </p>
                </template>

                <template x-if="question.type !== 'text' && answers[question.id].isCorrect !== null">
                    <div>
                        <p class="inline-flex items-center gap-1.5 text-xs font-medium"
                            :class="answers[question.id].isCorrect ? 'text-success' : 'text-danger'">
                            <span x-show="answers[question.id].isCorrect"><x-icon name="check" class="w-3.5 h-3.5" /></span>
                            <span x-show="!answers[question.id].isCorrect"><x-icon name="x" class="w-3.5 h-3.5" /></span>
                            <span x-text="answers[question.id].isCorrect ? 'Correct!' : 'Not quite.'"></span>
                        </p>
                        <template x-if="!answers[question.id].isCorrect">
                            <ul class="mt-1.5 space-y-0.5">
                                <template x-for="option in question.options.filter(o => answers[question.id].correctOptionIds.includes(o.id))" :key="option.id">
                                    <li class="flex items-center gap-1.5 text-xs text-success">
                                        <x-icon name="check" class="w-3 h-3" />
                                        <span x-text="option.text"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>
