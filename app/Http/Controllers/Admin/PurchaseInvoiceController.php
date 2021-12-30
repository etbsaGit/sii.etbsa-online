<?php

namespace App\Http\Controllers\Admin;

use App\Components\Purchase\Repositories\PurchaseInvoiceRepository;

class PurchaseInvoiceController extends AdminController
{
    /**
     * @var purchaseInvoiceRepository
     */
    private $purchaseInvoiceRepository;

    public function __construct(PurchaseInvoiceRepository $purchaseInvoiceRepository)
    {
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
    }

    public function index()
    {
        $data = $this->purchaseInvoiceRepository->list(request()->all());
        return $this->sendResponseOk($data, "list purchases invoices ok.");
    }
}
