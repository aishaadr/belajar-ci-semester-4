<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class PembelianController extends BaseController
{
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        helper(['number', 'form']);
        $this->transactionModel       = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
    }

    public function index()
    {
        $transactions   = $this->transactionModel->findAll();
        $transactionIds = array_column($transactions, 'id');
        $products       = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);

        return view('v_pembelian', [
            'transactions' => $transactions,
            'products'     => $products,
        ]);
    }

    public function ubahStatus($id)
    {
        $transaction = $this->transactionModel->find($id);

        if (!$transaction) {
            return redirect()->to('pembelian');
        }

        // Toggle status: 0 -> 1, 1 -> 0
        $statusBaru = $transaction['status'] == 1 ? 0 : 1;

        $this->transactionModel->update($id, ['status' => $statusBaru]);

        return redirect()->to('pembelian')->with('success', 'Status pesanan berhasil diubah.');
    }
}