<?php

namespace App\Http\Controllers\Utility;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\PromoResource;
use App\Http\Services\PromoService;
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
        $validateData = Validator::make(
            $request->only([
                "code"
            ]),
            [
                "code" => "required|string|max:10"
            ]
        )
            ->validate();

        $promo = $this->service->getPromo([ "code" => $validateData["code"] ]);

        if (is_null($promo)) {
            throw new NotFoundHttpException("Промо код олдсонгүй!");
        }

        $promo = $this->service->checkAndGetPromo($promo->id, [ "object" ]);

        return new PromoResource($promo);
    }
}
