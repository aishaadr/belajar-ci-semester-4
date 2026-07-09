<?php

namespace App\Controllers;

use App\Models\DiscountModel;

class DiskonController extends BaseController
{
    protected $discountModel;

    function __construct()
    {
        helper(['number', 'form']);
        $this->discountModel = new DiscountModel();
    }

    public function index()
    {
        return view('diskon/index', [
            'discounts' => $this->discountModel->findAll(),
        ]);
    }

    public function create()
    {
        $tanggal = $this->request->getPost('tanggal');
        $nominal = $this->request->getPost('nominal');

        // Validasi: tanggal harus unik
        $existing = $this->discountModel->where('tanggal', $tanggal)->first();
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['tanggal' => 'The tanggal field must contain a unique value.']);
        }

        $this->discountModel->insert([
            'tanggal' => $tanggal,
            'nominal' => $nominal,
        ]);

        return redirect()->to('diskon')->with('success', 'Data diskon berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Hanya update nominal, tanggal tidak boleh diubah
        $nominal = $this->request->getPost('nominal');

        $this->discountModel->update($id, [
            'nominal' => $nominal,
        ]);

        return redirect()->to('diskon')->with('success', 'Data diskon berhasil diubah.');
    }

    public function delete($id)
    {
        $this->discountModel->delete($id);
        return redirect()->to('diskon')->with('success', 'Data diskon berhasil dihapus.');
    }
}