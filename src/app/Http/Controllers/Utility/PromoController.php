<?php

namespace App\Http\Controllers\Utility;

use App\Enums\EPromoType;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\PromoResource;
use App\Http\Services\PromoService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoController extends Controller
{
    public function __construct(private PromoService $service)
    {
    }

    /**
     * Check and get promo code
     */
    public function validateAndGetPromoCode(Request $request)
    {
        $validatedData = Validator::make(
            $request->only([
                "object_id",
                "code",
                "amount",
                "type"
            ]),
            [
                "object_id" => "sometimes|integer",
                "code" => "required|string|max:10",
                "amount" => "required|integer",
                "type" => "required|integer"
            ]
        )
            ->validate();

        $promo = $this->service->getPromo([ "code" => $validatedData["code"] ]);

        $promo = $this->service->checkAndGetPromo($promo->id, [ "object" ], $validatedData);

        return response()->success([
            "promo" => new PromoResource($promo),
            "amount" => $this->service->getDiscountPrice($promo, $validatedData["amount"])
        ]);
    }
}
