<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashData('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

History Transaksi Pembelian
<hr>

<div class="table-responsive">
    <table class="table datatable">
        <thead>
            <tr>
                <th>#</th>
                <th>ID Pembelian</th>
                <th>Pembeli</th>
                <th>Waktu Pembelian</th>
                <th>Total Bayar</th>
                <th>Alamat</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)) : foreach ($transactions as $index => $item) : ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $item['id'] ?></td>
                    <td><?= $item['username'] ?></td>
                    <td><?= $item['created_at'] ?></td>
                    <td><?= number_to_currency($item['total_harga'], 'IDR') ?></td>
                    <td><?= $item['alamat'] ?></td>
                    <td>
                        <?= ($item['status'] == 1)
                            ? '<span class="badge bg-success">Sudah Selesai</span>'
                            : '<span class="badge bg-warning">Belum Selesai</span>' ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-success btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#detailModal-<?= $item['id'] ?>">
                            Detail
                        </button>
                        <a href="pembelian/ubah-status/<?= $item['id'] ?>"
                            class="btn btn-info btn-sm"
                            onclick="return confirm('Ubah status pesanan #<?= $item['id'] ?>?')">
                            Ubah Status
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Detail -->
<?php if (!empty($transactions)) : foreach ($transactions as $item) : ?>
    <div class="modal fade" id="detailModal-<?= $item['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi #<?= $item['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($products[$item['id']])) : ?>
                        <?php foreach ($products[$item['id']] as $index2 => $item2) : ?>
                            <?= ($index2 + 1) . ")" ?>
                            <?php if (!empty($item2['foto']) && file_exists(FCPATH . 'img/' . $item2['foto'])) : ?>
                                <div class="my-2">
                                    <img src="<?= base_url('img/' . $item2['foto']) ?>" width="100" class="img-thumbnail">
                                </div>
                            <?php endif; ?>
                            <strong><?= $item2['nama'] ?></strong>
                            <?= number_to_currency($item2['harga'], 'IDR') ?><br>
                            (<?= $item2['jumlah'] ?> pcs)<br>
                            <?= number_to_currency($item2['subtotal_harga'], 'IDR') ?>
                            <hr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    Ongkir <?= number_to_currency($item['ongkir'], 'IDR') ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; endif; ?>

<?= $this->endSection() ?>