<?php

namespace App\Http\Services;

use App\Enums\EConfirmationType;
use App\Enums\EStatus;
use App\Jobs\SMSJob;
use App\Mail\EmailConfirmationMail;
use App\Models\Utility\EmailConfirmation;
use Illuminate\Support\Facades\Mail;

class ConfirmationService
{
    public function __construct(private MessageService $messageService) {}

    public function getConfirmationByFilter($filters)
    {
        $filterModel = [
            "user_id" => [ "where", "user_id" ],
            "code" => [ "where", "code" ],
            "value" => [ "where", "email" ],
            "status" => [ "where", "status" ],
            "created_at_lte" => [ "where", ">=", "created_at" ],
        ];

        return filter_query_with_model(EmailConfirmation::query(), $filterModel, $filters)->first();
    }

    public function generateUniqueOTPCode(string $value)
    {
        $code = random_int(100000, 999999);
        do {
            $emailVerification = $this->getConfirmationByFilter([
                "code" => $code,
                "email" => $value,
                "status" => EStatus::PENDING
            ]);
            if (!is_null($emailVerification)) {
                $code = random_int(100000, 999999);
            }
        } while (!is_null($emailVerification));

        return $code;
    }

    public function createConfirmation(EConfirmationType $type, string $value)
    {
        $confirmation = EmailConfirmation::create([
            "email" => $value,
            "status" => EStatus::PENDING,
            "code" => $this->generateUniqueOTPCode($value),
            "type" => $type,
            "created_at" => date("Y-m-d H:i:s"),
            "email_token" => "none",
            "body" => "none"
        ]);

        switch ($type) {
            case EConfirmationType::EMAIL:
                Mail::to($value)->send(new EmailConfirmationMail($confirmation->code));
                break;
            case EConfirmationType::PHONE:
                SMSJob::dispatch($value, "Таны Hippocards баталгаажуулах код: " . $confirmation->code);
                break;
        }

        return $confirmation;
    }

    public function checkConfirmationFrequency(string $value)
    {
        $minuteBefore = date("Y-m-d H:i:s", strtotime("-1 minute"));
        $check = $this->getConfirmationByFilter([
            "email" => $value,
            "created_at_lte" => $minuteBefore
        ]);

        return !is_null($check);
    }
}
