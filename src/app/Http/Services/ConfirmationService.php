<?php

namespace App\Http\Services;

use App\Enums\EConfirmationType;
use App\Enums\EStatus;
use App\Exceptions\AppException;
use App\Jobs\SMSJob;
use App\Mail\EmailConfirmationMail;
use App\Models\Utility\EmailConfirmation;
use Illuminate\Support\Facades\Mail;

class ConfirmationService
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function getConfirmationByFilter($filters)
    {
        $filterModel = [
            "id" => [ "where", "id" ],
            "user_id" => [ "where", "user_id" ],
            "code" => [ "where", "code" ],
            "value" => [ "where", "email" ],
            "status" => [ "where", "status" ],
            "email" => [ "where", "email" ],
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

    public function createConfirmation(EConfirmationType $type, string $value, int $confirmationId = null)
    {
        if (is_null($confirmationId)) {
            $confirmation = EmailConfirmation::create([
                "email" => $value,
                "status" => EStatus::PENDING,
                "code" => $this->generateUniqueOTPCode($value),
                "type" => $type,
                "created_at" => date("Y-m-d H:i:s"),
                "email_token" => "none",
                "body" => "none"
            ]);
        } else {
            $confirmation = $this->getConfirmationByFilter([
                "id" => $confirmationId
            ]);

            $confirmation->update([
                "code" => $this->generateUniqueOTPCode($value),
                "created_at" => date("Y-m-d H:i:s"),
            ]);
        }

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

    public function updateConfirmation(int $id, array $data)
    {
        return EmailConfirmation::where("id", $id)->update($data);
    }

    public function approveConfirmation(int $comfirmationId, string $code)
    {
        $confirmation = $this->getConfirmationByFilter([
            "id" => $comfirmationId,
            "code" => $code
        ]);

        if (is_null($confirmation)) {
            throw new AppException("Invalid confirmation code!");
        }

        $this->updateConfirmation($confirmation->id, [
            "status" => EStatus::SUCCESS
        ]);
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

    public function checkConfirmationValidity(string $confirmationId)
    {
        $minuteBefore = date("Y-m-d H:i:s", strtotime("-500 minute"));

        $check = $this->getConfirmationByFilter([
            "id" => $confirmationId,
            "created_at_lte" => $minuteBefore
        ]);

        return $check;
    }
}
