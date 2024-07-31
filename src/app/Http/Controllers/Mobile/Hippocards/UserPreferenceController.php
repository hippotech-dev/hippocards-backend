<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Enums\ELanguageLevel;
use App\Enums\EUserPreferenceValue;
use App\Http\Controllers\Controller;
use App\Http\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    public function __construct(private AccountService $service)
    {
        $this->middleware("jwt.auth");
    }

    /**
     * Create user onboarding preferences
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "preferences",
            ),
            [
                "preferences" => "required|array|max:10",
                "preferences.*.type" => "required|string|max:32",
                "preferences.*.value" => "nullable",

            ]
        )
            ->validate();

        $requestUser = auth()->user();

        $this->service->createUpdateUserPreferences($requestUser, $validatedData["preferences"]);

        return response()->success();
    }

    /**
     * Get onboarding data
     */
    public function getOnboardingPreferencesData()
    {
        return response()->success([
            "learning_purposes" => [
                [
                    "icon" => "",
                    "text" => "Аялал эсвэл гадаадад суралцах",
                    "value" => EUserPreferenceValue::OBJECTIVE_TRAVEL_STUDY,
                ],
                [
                    "icon" => "",
                    "text" => "Гадаад хүмүүстэй харилцах",
                    "value" => EUserPreferenceValue::OBJECTIVE_COMMUNICATION,
                ],
                [
                    "icon" => "",
                    "text" => "Өөрийгөө сайжруулах",
                    "value" => EUserPreferenceValue::OBJECTIVE_SELF_IMPROVEMENT,
                ],
                [
                    "icon" => "",
                    "text" => "Карьертаа хөрөнгө оруулах",
                    "value" => EUserPreferenceValue::OBJECTIVE_CARRER_ADVANCEMENT,
                ],
                [
                    "icon" => "",
                    "text" => "Аялал эсвэл гадаадад суралцах",
                    "value" => EUserPreferenceValue::OBJECTIVE_KIDS_COMMUNICATION,
                ],
                [
                    "icon" => "",
                    "text" => "Бусад",
                    "value" => EUserPreferenceValue::OTHERS,
                ]
            ],
            "how_will_hippo_help_you" => [
                [
                    "icon" => "",
                    "text" => "Ярианы чадвар",
                    "value" => EUserPreferenceValue::SKILL_SPEAKING,
                ],
                [
                    "icon" => "",
                    "text" => "Дуудлагаа сайжруулах",
                    "value" => EUserPreferenceValue::SKILL_PRONUNCIATION,
                ],
                [
                    "icon" => "",
                    "text" => "Сонсголын чадвар",
                    "value" => EUserPreferenceValue::SKILL_LISTENING,
                ],
                [
                    "icon" => "",
                    "text" => "Бичих чадвар",
                    "value" => EUserPreferenceValue::SKILL_WRITING,
                ],
                [
                    "icon" => "",
                    "text" => "Хэрэгтэй үгс",
                    "value" => EUserPreferenceValue::SKILL_VOCABULARY,
                ],
                [
                    "icon" => "",
                    "text" => "Бусад",
                    "value" => EUserPreferenceValue::OTHERS,
                ]
            ],
            "language_levels" => [
                [
                    "icon" => "",
                    "text" => "Түвшин 0",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::BEGINNER,
                ],
                [
                    "icon" => "",
                    "text" => "Түвшин 1",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::UPPER_BEGINNER,
                ],
                [
                    "icon" => "",
                    "text" => "Түвшин 2",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::INTERMIDIATE,
                ],
                [
                    "icon" => "",
                    "text" => "Түвшин 3",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::UPPER_INTERMIDIATE,
                ],
                [
                    "icon" => "",
                    "text" => "Түвшин 4",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::ADVANCED,
                ]
            ],
            "daily_study_time" => [
                [
                    "icon" => "",
                    "text" => "Өглөө",
                    "value" => EUserPreferenceValue::STUDY_TIME_MORNING
                ],
                [
                    "icon" => "",
                    "text" => "Өдөр",
                    "value" => EUserPreferenceValue::STUDY_TIME_AFTERNOON
                ],
                [
                    "icon" => "",
                    "text" => "Орой",
                    "value" => EUserPreferenceValue::STUDY_TIME_NIGHT
                ],
                [
                    "icon" => "",
                    "text" => "Бусад",
                    "value" => EUserPreferenceValue::OTHERS
                ],
            ],
            "study_repetition" => [
                [
                    "icon" => "",
                    "text" => "Өдөр бүр",
                    "value" => EUserPreferenceValue::STUDY_REPETITION_DAILY
                ],
                [
                    "icon" => "",
                    "text" => "Долоо хоног бүр",
                    "value" => EUserPreferenceValue::STUDY_REPETITION_WEEKLY
                ],
                [
                    "icon" => "",
                    "text" => "Сар бүр",
                    "value" => EUserPreferenceValue::STUDY_REPETITION_MONTHLY
                ],
                [
                    "icon" => "",
                    "text" => "Бусад",
                    "value" => EUserPreferenceValue::OTHERS
                ],
            ],
        ]);
    }
}
