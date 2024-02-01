<?php

namespace App\Components\Product\Repositories;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Currency;
use App\Components\Core\BaseRepository;
use App\Components\Product\Models\Product;
use App\Components\Product\Models\ProductBrands;
use App\Components\Product\Models\ProductCategory;
use App\Components\Product\Models\ProductModel;
use App\Components\Product\Models\ProductSuppliers;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get(
            $params,
            ['category:id,name', 'model:id,name', 'agency:id,title', 'currency'],
            function ($query) use ($params) {
                $query->search($params['search'] ?? '')
                    ->filter($params)
                    ->priceType($params['price_type'] ?? '')
                    ->when($params['is_usado'] ?? null, function ($query, $is_usado) {
                        if ($is_usado === "with") {
                            $query->whereIn('is_usado', [0, 1]);
                        } elseif ($is_usado === 'only') {
                            $query->where('is_usado', '=', 1);
                        }
                    }, function ($query) {
                        $query->where('is_usado', '=', 0);
                    })
                    ->when($params['desactive'] ?? null, function ($query, $desactive) {
                        if ($desactive === "with") {
                            $query->whereIn('active', [0, 1]);
                        } elseif ($desactive === 'only') {
                            $query->where('active', '=', 0);
                        }
                    }, function ($query) {
                        $query->where('active', '=', 1);
                    });
                // if ($params['category_name'] ?? null) {
                //     $q->Categoryname($params);
                // }
                return $query;
            }
        );
    }

    public function options()
    {
        $category = ProductCategory::with('models')->get();
        $model = ProductModel::all('id', 'name');
        $agency = Agency::all('id', 'title');
        $currency = Currency::all('id', 'name');
        $brands = ProductBrands::all('id', 'name');
        $suppliers = ProductSuppliers::all('id', 'name');
        $prices_types = [
            ['text' => 'Por Definir', 'value' => 'por_definir']
        ];

        return compact(
            'category',
            'model',
            'agency',
            'currency',
            'brands',
            'suppliers'
        );
    }
}
