<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * Create user onboarding preferences
     */
    public function store(Request $request)
    {

    }

    /**
     * Get onboarding data
     */
    public function getOnboardingPreferencesData()
    {
        return response()->success([
            "learning_purpose" => [],
            "how_will_hippo_help_you" => [],
            "language_levels" => [],
            "daily_study_time" => [],
            "study_repetition" => [],
        ]);
    }
}
