<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Utilities\Helpers;
use App\Components\Tracking\Repositories\SellerRepository;
use App\Components\User\Models\User;
use Illuminate\Http\Request;

class SellerController extends AdminController
{

    /**
     * @var SellerRepository
     */
    private $sellerRepository;

    /**
     * UserController constructor.
     * @param SellerRepository $sellerRepository
     */
    public function __construct(SellerRepository $sellerRepository)
    {
        $this->sellerRepository = $sellerRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = [];
        $data = $this->sellerRepository->listSellers(request()->all());
        return $this->sendResponseOk($data, "list sellers ok.");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = $this->sellerRepository->find($id, ['seller_type','agency','department']);

        if (!$seller) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($seller);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = validator($request->all(),[
            'seller_type' => 'array',
        ]);

        if($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $payload = $request->all();

        // if password field is present but has empty value or null value
        // we will remove it to avoid updating password with unexpected value
        if(!Helpers::hasValue($payload['password'])) unset($payload['password']);

        $updated = $this->sellerRepository->update($id,$payload);

        if(!$updated) return $this->sendResponseBadRequest("Failed update");

        // re-sync seller_type

        /** @var User $seller */
        $seller = $this->sellerRepository->find($id);

        $sellerTypeIds = [];

        if($seller_type = $request->get('seller_type',[]))
        {
            foreach ($seller_type as $sellerTypeId => $shouldAttach)
            {
                if($shouldAttach) $sellerTypeIds[] = $sellerTypeId;
            }
        }

        $seller->seller_type()->sync($sellerTypeIds);

        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function resource()
    {
        # code...
    }
}
