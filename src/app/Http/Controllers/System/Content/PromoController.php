<?php

namespace App\Http\Controllers\System\Content;

use App\Enums\EPromoAmountType;
use App\Enums\EPromoContextType;
use App\Enums\EPromoType;
use App\Enums\EPromoUsageType;
use App\Enums\EStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\PromoResource;
use App\Http\Services\PromoService;
use App\Models\Utility\PromoCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    public function __construct(private PromoService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only("name", "dwq");

        $promos = $this->service->getPromosWithPage($filters);

        return PromoResource::collection($promos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "object_id",
                "code",
                "type",
                "usage_type",
                "total_quantity",
                "description",
                "amount",
                "amount_type",
                "context_type"
            ),
            [
                "object_id" => "nullable|integer",
                "type" => [
                    "required",
                    Rule::in([ EPromoType::SUBSCIPRIPTION->value, EPromoType::ACADEMY_COURSE->value ])
                ],
                "description" => "required|string",
                "usage_type" => [
                    "required",
                    Rule::in([ EPromoUsageType::MULTIPLE->value, EPromoUsageType::SINGLE->value ])
                ],
                "total_quantity" => "required|integer",
                "amount" => "required|integer",
                "amount_type" => [
                    "required",
                    Rule::in([ EPromoAmountType::DEFAULT->value, EPromoAmountType::PERCENT->value ])
                ],
                "context_type" => [
                    "required",
                    Rule::in([ EPromoContextType::HIPPOCARDS->value, EPromoContextType::PROMOTIONAL->value, EPromoContextType::INFLUENCER->value ]),
                ],
            ]
        )
            ->validate();

        $promo = $this->service->createPromo($validatedData);

        return new PromoResource($promo);
    }

    /**
     * Create batch promo
     */
    public function createBatchPromo(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "batch_data",
                "data",
            ),
            [
                "batch_data.batch_quantity" => "required|integer|max:1000",
                "batch_data.context_type" => [
                    "required",
                    Rule::in([ EPromoContextType::HIPPOCARDS->value, EPromoContextType::PROMOTIONAL->value, EPromoContextType::INFLUENCER->value ]),
                ],
                "data.object_id",
                "data.type",
                "data.usage_type",
                "data.total_quantity",
                "data.description",
                "data.amount",
                "data.amount_type",
            ]
        )
            ->validate();

        $this->service->createBatchPromo($validatedData["batch_data"], $validatedData["data"]);

        return response()->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promo = $this->service->getPromoByIdThrow($id);

        return new PromoResource($promo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromoCode $promo)
    {
        $validatedData = Validator::make(
            $request->only(
                "description",
                "total_quantity",
                "status"
            ),
            [
                "description" => "sometimes|string",
                "total_quantity" => "sometimes|integer",
                "status" => [
                    "sometimes",
                    Rule::in([ EStatus::PENDING->value, EStatus::SUCCESS->value ])
                ],
            ]
        )
            ->validate();

        $this->service->updatePromo($promo, $validatedData);

        return response()->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoCode $promo)
    {
        $this->service->deletePromo($promo);
        return response()->success();
    }
}
