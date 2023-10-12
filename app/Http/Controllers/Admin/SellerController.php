<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Utilities\Helpers;
use App\Components\Tracking\Repositories\SellerRepository;
use App\Components\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $data = $this->sellerRepository->listSellers(request()->all());
        return $this->sendResponseOk($data, "list sellers ok.");

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = $this->sellerRepository->find($id, ['seller_type', 'seller_agency', 'seller_category']);

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
    public function update(Request $request, User $seller)
    {
        $validate = validator($request->all(), [
            'seller_key' => ['required', 'max:50', Rule::unique('users', 'seller_key')->ignore($seller)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($seller)],
            'photo' => 'file',

        ]);



        if ($validate->fails())
            return $this->sendResponseBadRequest($validate->errors()->first());


        if ($request->file("photo") && !!$seller->photo_path)
            $this->deletePhoto($seller->photo_path);

        $payload = array_merge(
            $request->all(),
            [
                'photo_path' => $request->file("photo") ? $request->file("photo")->store($seller->getFolderPath(), 's3') : null,
            ]
        );

        if (!Helpers::hasValue($payload['password']))
            unset($payload['password']);

        // $updated = $this->sellerRepository->update($id, $payload);
        $updated = $seller->update($payload);

        if (!$updated)
            return $this->sendResponseBadRequest("Failed update");

        return $this->sendResponseUpdated(compact('seller'));
    }

    public function updateSellerType(Request $request, User $seller)
    {
        $validate = validator($request->all(), [
            'seller_type' => 'array',
        ]);

        if ($validate->fails())
            return $this->sendResponseBadRequest($validate->errors()->first());


        // $seller = $this->sellerRepository->find($seller);

        $sellerTypeIds = [];

        if ($seller_type = $request->get('seller_type', [])) {
            foreach ($seller_type as $sellerTypeId => $shouldAttach) {
                if ($shouldAttach)
                    $sellerTypeIds[] = $sellerTypeId;
            }
        }

        $seller->seller_type()->sync($sellerTypeIds);
        return $this->sendResponseUpdated([], "Departamentos Configurados");

    }
    public function updateSellerAgency(Request $request, User $seller)
    {

        $validate = validator($request->all(), [
            'seller_agency' => 'array',
        ]);

        if ($validate->fails())
            return $this->sendResponseBadRequest($validate->errors()->first());


        // $seller = $this->sellerRepository->find($seller);

        $sellerAgencyIds = [];

        if ($seller_agency = $request->get('seller_agency', [])) {
            foreach ($seller_agency as $sellerAgencyId => $shouldAttach) {
                if ($shouldAttach)
                    $sellerAgencyIds[] = $sellerAgencyId;
            }
        }

        $seller->seller_agency()->sync($sellerAgencyIds);

        return $this->sendResponseUpdated([], 'Sucursales Configuradas');

    }
    public function updateSellerCategory(Request $request, User $seller)
    {

        $validate = validator($request->all(), [
            'seller_category' => 'array',
        ]);

        if ($validate->fails())
            return $this->sendResponseBadRequest($validate->errors()->first());


        // $seller = $this->sellerRepository->find($seller);

        $sellerCategoryIds = [];

        if ($seller_category = $request->get('seller_category', [])) {
            foreach ($seller_category as $sellerCategoryId => $shouldAttach) {
                if ($shouldAttach)
                    $sellerCategoryIds[] = $sellerCategoryId;
            }
        }
        $seller->seller_category()->sync($sellerCategoryIds);
        return $this->sendResponseUpdated([], 'Categorias Configuradas');
    }
   
    public function deleteSellerPhoto(Request $request, User $seller)
    {

        if ($this->deletePhoto($seller->photo_path)) {
            $seller->photo_path = null;
            $seller->save();
            return $this->sendResponseDeleted();
        } else
            return $this->sendResponseBadRequest();

    }

    function deletePhoto($path)
    {
        if (Storage::exists($path)) {

            Storage::disk('s3')->delete($path);
            return true;
        }

        return false;
    }
}