<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\RajaOngkirService;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\DiscountModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $transactionModel;
    protected $transactionDetailModel;
    protected $discountModel;

    public function __construct()
    {
        helper(['number', 'form']);
        $this->cart                   = service('cart');
        $this->transactionModel       = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
        $this->discountModel          = new DiscountModel();
    }

    // Ambil diskon hari ini (helper internal)
    private function getTodayDiscount()
    {
        return $this->discountModel->where('tanggal', date('Y-m-d'))->first();
    }

    public function index()
    {
        $todayDiscount = $this->getTodayDiscount();

        $data = [
            'items'         => $this->cart->contents(),
            'total'         => $this->cart->total(),
            'todayDiscount' => $todayDiscount,
        ];

        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert([
            'id'      => $this->request->getPost('id'),
            'qty'     => 1,
            'price'   => $this->request->getPost('harga'),
            'name'    => $this->request->getPost('nama'),
            'options' => [
                'foto'          => $this->request->getPost('foto'),
                'harga_asli'    => $this->request->getPost('harga'), // simpan harga asli sebelum diskon
            ]
        ]);

        session()->setFlashdata(
            'success',
            'Produk berhasil ditambahkan ke keranjang.
            <a href="' . base_url('keranjang') . '">Lihat</a>'
        );

        return redirect()->to(base_url('/'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $item) {
            $qty = $this->request->getPost('qty' . $i++);

            $this->cart->update([
                'rowid' => $item['rowid'],
                'qty'   => $qty
            ]);
        }

        session()->setFlashdata('success', 'Keranjang berhasil diperbarui');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);
        session()->setFlashdata('success', 'Produk berhasil dihapus dari keranjang');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();
        session()->setFlashdata('success', 'Keranjang berhasil dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
    {
        $todayDiscount = $this->getTodayDiscount();
        $items         = $this->cart->contents();

        // Hitung total dengan diskon
        $nominalDiskon = $todayDiscount ? $todayDiscount['nominal'] : 0;
        $total = 0;
        foreach ($items as $item) {
            $harga_asli  = $item['options']['harga_asli'] ?? $item['price'];
            $harga_final = $harga_asli - $nominalDiskon;
            $total      += $harga_final * $item['qty'];
        }

        $data = [
            'items'         => $items,
            'total'         => $total,
            'todayDiscount' => $todayDiscount,
        ];

        return view('v_checkout', $data);
    }

    public function destinations()
    {
        $search  = $this->request->getGet('q');
        $service = new RajaOngkirService();
        $response = $service->getDestination($search);

        $results = [];
        $data    = $response['data'] ?? [];

        foreach ($data as $item) {
            $results[] = [
                'id'   => $item['id'],
                'text' => $item['label']
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }

    public function costs()
    {
        $origin      = '64999';
        $destination = $this->request->getGet('destination');
        $weight      = '1000';
        $courier     = 'jne';

        $service  = new RajaOngkirService();
        $response = $service->getCost($origin, $destination, $weight, $courier);

        $results = [];
        $data    = $response['data'] ?? [];

        foreach ($data as $item) {
            $results[] = [
                'service'     => $item['service'],
                'description' => $item['description'],
                'cost'        => $item['cost'],
                'etd'         => $item['etd']
            ];
        }

        return $this->response->setJSON($results);
    }

    public function buy()
    {
        $cartItems = $this->cart->contents();

        if (empty($cartItems)) {
            return redirect()->back();
        }

        $todayDiscount = $this->getTodayDiscount();
        $nominalDiskon = $todayDiscount ? $todayDiscount['nominal'] : 0;

        $db = \Config\Database::connect();
        $db->transStart();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $harga_asli  = $item['options']['harga_asli'] ?? $item['price'];
            $harga_final = $harga_asli - $nominalDiskon;
            $subtotal   += $harga_final * $item['qty'];
        }

        $ongkir = (int) $this->request->getPost('ongkir');

        $transaction = [
            'username'    => $this->request->getPost('username'),
            'alamat'      => $this->request->getPost('alamat'),
            'ongkir'      => $ongkir,
            'total_harga' => $subtotal + $ongkir,
            'status'      => 0,
        ];

        if (!$this->transactionModel->insert($transaction)) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal membuat transaksi');
        }

        $transactionId = $this->transactionModel->getInsertID();

        foreach ($cartItems as $item) {
            $harga_asli  = $item['options']['harga_asli'] ?? $item['price'];
            $harga_final = $harga_asli - $nominalDiskon;

            $this->transactionDetailModel->insert([
                'transaction_id' => $transactionId,
                'product_id'     => $item['id'],
                'jumlah'         => $item['qty'],
                'diskon'         => $nominalDiskon,                  // simpan nominal diskon
                'subtotal_harga' => $harga_final * $item['qty'],     // subtotal sudah terpotong diskon
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal membuat transaksi');
        }

        $this->cart->destroy();
        return redirect()->to(base_url());
    }

    public function history()
    {
        $username = session()->get('username');

        $transactions   = $this->transactionModel->where('username', $username)->findAll();
        $transactionIds = array_column($transactions, 'id');
        $products       = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);

        $data = [
            'username'     => $username,
            'transactions' => $transactions,
            'products'     => $products
        ];

        return view('v_history', $data);
    }
}