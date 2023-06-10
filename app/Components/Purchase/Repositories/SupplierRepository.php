<?php

namespace App\Components\Purchase\Repositories;

use App\Components\Common\Models\Estatus;
use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Purchase\Models\Supplier;
use App\Components\Requirements\Models\RequirementDocuments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SupplierRepository extends BaseRepository
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    /**
     * list all users
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function list($params)
    {
        return $this->get($params, [], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '');
            });
            return $query;
        });
    }

    public function syncDocuments(Supplier $supplier, $documents = [])
    {
        $syncDocument = [];
        if ($documents) {
            foreach ($documents as $index => $document) {
                if ($document) {
                    $file = $document['file'] ?? null;
                    $pivot = [
                        'file_path' => $file != "null" && $file != null
                        ? $document['file']->store('supplier/id_' . $supplier->id . '/' . $document['key'], 's3')
                        : null,
                        'status_id' => Estatus::where('key', $document['status_key'])->first()->id,
                        'date_due' => Carbon::now()->addMonths(3),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $syncDocument[$document['id']] = $pivot;
                }
            }
            return $supplier->requirements()->sync($syncDocument);
        }
    }

    public function getSupplierRequirementDefault()
    {
        return RequirementDocuments::supplirsRequirements()->get(["id", "name", "description", "key"])->transform(function ($item) {
            return [
                'id' => $item->id ?? '',
                'name' => $item->name ?? '',
                'description' => $item->description ?? '',
                'key' => $item->key ?? '',
                'file_path' => '',
                'PATH' => '',
                'file' => '',
                'status_key' => 'documento.none',
                'date_due' => '',
            ];
        });
    }

    public function getSupplierDocuments(Supplier $supplier)
    {

        $documents = $supplier->requirements->transform(function ($item) {
            return [
                'id' => $item->id ?? '',
                'name' => $item->name ?? '',
                'description' => $item->description ?? '',
                'key' => $item->key ?? '',
                'file_path' => $item->pivot->file_path ? Storage::disk('s3')->url($item->pivot->file_path) : '',
                'PATH' => $item->pivot->file_path ?? '',
                'file' => '',
                'status_key' => Estatus::find($item->pivot->status_id)->key ?? '',
                'date_due' => $item->pivot->date_due ?? '',
            ];
        });
        $requirements = $this->getSupplierRequirementDefault();

        $mergedCollection = collect($documents)->concat($requirements);
        $groupedCollection = $mergedCollection->groupBy('id');
        $uniqueCollection = $groupedCollection->map(function ($items) {
            return $items->first();
        });

        return $uniqueCollection->values()->all();

    }
}