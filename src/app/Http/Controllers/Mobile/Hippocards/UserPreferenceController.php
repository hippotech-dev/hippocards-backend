<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Enums\ELanguageLevel;
use App\Enums\EUserPreferenceType;
use App\Enums\EUserPreferenceValue;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\UserPreferenceResource;
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
     * List user onboarding preferences
     */
    public function index(Request $request)
    {
        $requestUser = auth()->user();

        $preferences = $this->service->getUserPreferences($requestUser);

        return UserPreferenceResource::collection($preferences);
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
            EUserPreferenceType::LEARNING_PURPOSE->value => [
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
                    "text" => "Карьертаа хөрөнгө оруулах",
                    "value" => EUserPreferenceValue::OBJECTIVE_CARRER_ADVANCEMENT,
                ],
                [
                    "icon" => "",
                    "text" => "Өөрийгөө сайжруулах",
                    "value" => EUserPreferenceValue::OBJECTIVE_SELF_IMPROVEMENT,
                ],
                [
                    "icon" => "",
                    "text" => "Хүүхдүүдтэйгээ англиар харилцах",
                    "value" => EUserPreferenceValue::OBJECTIVE_KIDS_COMMUNICATION,
                ],
                [
                    "icon" => "",
                    "text" => "Бусад",
                    "value" => EUserPreferenceValue::OTHERS,
                ]
            ],
            EUserPreferenceType::HOW_HIPPO_WILL_HELP->value => [
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
                    "text" => "Дүрмийн чадвар",
                    "value" => EUserPreferenceValue::SKILL_GRAMMAR,
                ],
                [
                    "icon" => "",
                    "text" => "Үгийн баялаг",
                    "value" => EUserPreferenceValue::SKILL_VOCABULARY,
                ],
                [
                    "icon" => "",
                    "text" => "Бусад",
                    "value" => EUserPreferenceValue::OTHERS,
                ]
            ],
            EUserPreferenceType::LANGUAGE_LEVEL->value => [
                [
                    "icon" => "",
                    "text" => "A1 анхан",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::BEGINNER,
                ],
                [
                    "icon" => "",
                    "text" => "A2 ахисан анхан",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::UPPER_BEGINNER,
                ],
                [
                    "icon" => "",
                    "text" => "B1 дунд",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::INTERMIDIATE,
                ],
                [
                    "icon" => "",
                    "text" => "B2 ахисан дунд",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::UPPER_INTERMIDIATE,
                ],
                [
                    "icon" => "",
                    "text" => "C1 сайн",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::ADVANCED,
                ],
                [
                    "icon" => "",
                    "text" => "C2 нилээд сайн",
                    "sub_text" => "Хэдэн үгний мэдлэгтэй.",
                    "value" => ELanguageLevel::UPPER_ADVANCED,
                ]
            ],
            EUserPreferenceType::STUDY_TIME->value => [
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
            EUserPreferenceType::STUDY_REPETITION->value => [
                [
                    "icon" => "",
                    "text" => "Долоо хоногт цөөн өдөр",
                    "value" => EUserPreferenceValue::STUDY_REPETITION_WEEKLY
                ],
                [
                    "icon" => "",
                    "text" => "Долоо хоногт олон өдөр",
                    "value" => EUserPreferenceValue::STUDY_REPETITION_DAILY
                ],
                [
                    "icon" => "",
                    "text" => "Хэсэг идэвхитэй нүдэж байгаад завсарлана",
                    "value" => EUserPreferenceValue::STUDY_REPETITION_SELDOM
                ],
            ],
        ]);
    }
}
