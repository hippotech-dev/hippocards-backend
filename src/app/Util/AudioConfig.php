<?php

namespace App\Util;

use App\Enums\ELocale;
use App\Models\Utility\Language;

class AudioConfig
{
    private mixed $neural = null;
    private Language|null $language = null;

    public function __construct(private ELocale $locale, private int $gender = 1)
    {
        $this->language = Language::where("azure", $locale->value)->first();

        if (!is_null($this->language)) {
            $this->neural = $this->gender === 1 ? $this->language->neural_name : $this->language->neural_name_female;
        }
    }

    public function getNeural()
    {
        return $this->neural;
    }

    public function getLocale()
    {
        return $this->locale->value ?? "none";
    }

    public function getGender()
    {
        return $this->gender;
    }
}
