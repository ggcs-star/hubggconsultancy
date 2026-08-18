<?php

namespace Database\Seeders;

use App\Models\OnboardingAssessmentOption;
use App\Models\OnboardingAssessmentQuestion;
use App\Models\OnboardingAssessmentQuiz;
use App\Models\OnboardingAssessmentSetting;
use Illuminate\Database\Seeder;

class OnboardingAssessmentQuizSeeder extends Seeder
{
    public function run(): void
    {
        $quizzes = [
            [
                'title' => 'Sales Skills Quiz',
                'description' => 'Core sales fundamentals — prospecting, objection handling, and closing techniques.',
                'questions' => [
                    [
                        'type' => 'radio',
                        'text' => 'What is the primary goal of a discovery call?',
                        'points' => 2,
                        'options' => [
                            ["Understand the prospect's needs and pain points", true],
                            ['Close the deal immediately', false],
                            ['Talk about your product features only', false],
                            ['Ask for referrals', false],
                        ],
                    ],
                    [
                        'type' => 'checkbox',
                        'text' => 'Which of the following are effective ways to handle price objections? (Select all that apply)',
                        'points' => 3,
                        'options' => [
                            ['Reiterate the value and ROI', true],
                            ['Immediately offer a discount', false],
                            ['Ask what budget they were expecting', true],
                            ["Compare against a competitor's higher price", true],
                            ['Ignore the objection and move on', false],
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'text' => "What does 'BANT' stand for in sales qualification?",
                        'points' => 2,
                        'options' => [
                            ['Budget, Authority, Need, Timeline', true],
                            ['Budget, Attitude, Need, Trust', false],
                            ['Belief, Authority, Negotiation, Timing', false],
                            ['Budget, Authority, Negotiation, Trust', false],
                        ],
                    ],
                    [
                        'type' => 'checkbox',
                        'text' => 'Which signals typically indicate a prospect is ready to buy? (Select all that apply)',
                        'points' => 3,
                        'options' => [
                            ['They ask about pricing and contract terms', true],
                            ['They ask who else uses the product', true],
                            ['They stop responding to emails', false],
                            ['They ask about onboarding/implementation timelines', true],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'text' => "In 2-3 sentences, describe how you would handle a prospect who says \"we're happy with our current solution.\"",
                        'points' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Communication Skills Quiz',
                'description' => 'Listening, tone, and clarity in client-facing communication.',
                'questions' => [
                    [
                        'type' => 'radio',
                        'text' => "What is 'active listening'?",
                        'points' => 2,
                        'options' => [
                            ['Fully concentrating on and understanding the speaker before responding', true],
                            ['Waiting for your turn to speak', false],
                            ['Repeating everything the speaker says word for word', false],
                            ['Taking notes without eye contact', false],
                        ],
                    ],
                    [
                        'type' => 'checkbox',
                        'text' => 'Which of these are signs of good non-verbal communication during a client call? (Select all that apply)',
                        'points' => 3,
                        'options' => [
                            ['Maintaining a steady, engaged tone of voice', true],
                            ['Frequently interrupting the client', false],
                            ['Pausing to let the client finish their thought', true],
                            ['Summarizing what the client said', true],
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'text' => 'When a client is upset, what should you do first?',
                        'points' => 2,
                        'options' => [
                            ['Acknowledge their frustration and listen', true],
                            ["Defend your company's position immediately", false],
                            ['Transfer them to another department', false],
                            ['End the call and follow up later', false],
                        ],
                    ],
                    [
                        'type' => 'checkbox',
                        'text' => 'Which of the following improve clarity in written client communication? (Select all that apply)',
                        'points' => 3,
                        'options' => [
                            ['Using short, simple sentences', true],
                            ['Using jargon to sound professional', false],
                            ['Structuring emails with clear headings/bullet points', true],
                            ['Confirming next steps at the end of the message', true],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'text' => 'Describe a time you had to explain a complex idea to someone with no technical background. How did you simplify it?',
                        'points' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Product Knowledge Quiz',
                'description' => 'Understanding of our product lineup, features, and ideal customer fit.',
                'questions' => [
                    [
                        'type' => 'radio',
                        'text' => "Why is it important to understand a prospect's industry before a demo?",
                        'points' => 2,
                        'options' => [
                            ['To tailor the demo to their specific use case', true],
                            ['To make small talk', false],
                            ["It isn't important", false],
                            ['To find out their budget only', false],
                        ],
                    ],
                    [
                        'type' => 'checkbox',
                        'text' => "Which of these are examples of 'value-based selling'? (Select all that apply)",
                        'points' => 3,
                        'options' => [
                            ['Connecting product features to business outcomes', true],
                            ['Leading with a long list of technical specs', false],
                            ['Quantifying time or cost savings', true],
                            ["Tailoring the pitch to the prospect's stated goals", true],
                        ],
                    ],
                    [
                        'type' => 'radio',
                        'text' => "What should you do if a prospect asks about a feature you're unsure of?",
                        'points' => 2,
                        'options' => [
                            ['Be honest and follow up with the correct answer', true],
                            ['Guess an answer to sound confident', false],
                            ['Change the subject', false],
                            ["Tell them it doesn't exist", false],
                        ],
                    ],
                    [
                        'type' => 'checkbox',
                        'text' => 'Which of these belong in a strong product demo? (Select all that apply)',
                        'points' => 3,
                        'options' => [
                            ['A clear agenda shared up front', true],
                            ['Focusing only on features the prospect cares about', true],
                            ['Reading every slide word for word', false],
                            ['Leaving time for questions', true],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'text' => "In your own words, summarize our product's main value proposition for a first-time prospect.",
                        'points' => 5,
                    ],
                ],
            ],
        ];

        foreach ($quizzes as $quizIndex => $quizData) {
            $quiz = OnboardingAssessmentQuiz::firstOrCreate(
                ['title' => $quizData['title']],
                [
                    'description' => $quizData['description'],
                    'is_published' => true,
                    'sort_order' => $quizIndex + 1,
                ]
            );

            if ($quiz->questions()->exists()) {
                continue;
            }

            foreach ($quizData['questions'] as $questionIndex => $questionData) {
                $question = $quiz->questions()->create([
                    'type' => $questionData['type'],
                    'question_text' => $questionData['text'],
                    'points' => $questionData['points'],
                    'sort_order' => $questionIndex + 1,
                ]);

                foreach ($questionData['options'] ?? [] as $optionIndex => [$optionText, $isCorrect]) {
                    OnboardingAssessmentOption::create([
                        'onboarding_assessment_question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => $isCorrect,
                        'sort_order' => $optionIndex + 1,
                    ]);
                }
            }
        }

        OnboardingAssessmentSetting::current()->update([
            'passing_score_percent' => 60,
            'is_published' => true,
        ]);
    }
}
