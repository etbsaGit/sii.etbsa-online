<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Components\Gps\Repositories\GpsRepository;
use App\Components\Marketing\Repositories\MarketingRepository;
use App\Components\Tracking\Repositories\TrackingRepository;
use App\Components\Gps\Repositories\GpsChipsRepository;
use App\Components\Gps\Repositories\GpsGroupRepository;
use App\Components\Product\Repositories\ProductRepository;
use App\Components\Purchase\Repositories\PurchaseInvoiceRepository;
use App\Components\Purchase\Repositories\SupplierRepository;
use App\Exports\GpsChipExport;
use App\Exports\GpsExport;
use App\Exports\GpsGroupExport;
use App\Exports\MarketingExport;
use App\Exports\ProductExport;
use App\Exports\PurchaseInvoiceExport;
use App\Exports\SupplierExport;
use App\Exports\TrackingExport;

class ExportController extends AdminController
{
    /**
     * @var GpsRepository
     */
    private $gpsRepository;
    private $gpsChipsRepository;
    private $gpsGroupRepository;
    private $marketingRepository;
    private $trackingRepository;
    private $supplierRepository;

    /**
     * FileGroupController constructor.
     * @param GpsRepository $gpsRepository
     */
    public function __construct(
        GpsRepository $gpsRepository,
        MarketingRepository $marketingRepository,
        TrackingRepository $trackingRepository,
        GpsChipsRepository $gpsChipsRepository,
        GpsGroupRepository $gpsGroupRepository,
        SupplierRepository $supplierRepository,
        PurchaseInvoiceRepository $purchaseInvoiceRepository,
        ProductRepository $productRepository

    ) {
        $this->gpsRepository = $gpsRepository;
        $this->gpsChipsRepository = $gpsChipsRepository;
        $this->gpsGroupRepository = $gpsGroupRepository;
        $this->marketingRepository = $marketingRepository;
        $this->trackingRepository = $trackingRepository;
        $this->supplierRepository = $supplierRepository;
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportGps(Request $request)
    {
        $data = $this->gpsRepository->index($request->all());

        return Excel::download(new GpsExport($data), 'gps.xlsx');
    }
    public function exportGpsChips(Request $request)
    {
        $data = $this->gpsChipsRepository->index($request->all());

        return Excel::download(new GpsChipExport($data), 'gps-chips.xlsx');
    }
    public function exportGpsGroups(Request $request)
    {
        $data = $this->gpsGroupRepository->index($request->all());

        return Excel::download(new GpsGroupExport($data), 'gps-chips.xlsx');
    }

    public function exportMarketing(Request $request)
    {
        $data = $this->marketingRepository->index($request->all());

        return Excel::download(new MarketingExport($data), 'maketing.xlsx');
    }

    public function exportTracking(Request $request)
    {
        $data = $this->trackingRepository->listTracking($request->all());

        return Excel::download(new TrackingExport($data), 'tracking.xlsx');
    }

    public function exportSupplier(Request $request)
    {
        $data = $this->supplierRepository->list($request->all());

        return Excel::download(new SupplierExport($data), 'supplier.xlsx');
    }
    public function exportInvoice(Request $request)
    {
        $data = $this->purchaseInvoiceRepository->list($request->all());

        return Excel::download(new PurchaseInvoiceExport($data), 'invoices.xlsx');
    }
    public function exportProducts(Request $request)
    {
        $data = $this->productRepository->list($request->all());
        // return dd($data);

        return Excel::download(new ProductExport($data), 'products.xlsx');
    }
}
