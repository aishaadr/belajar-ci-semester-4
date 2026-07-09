<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashData('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
$nominalDiskon = $todayDiscount ? $todayDiscount['nominal'] : 0;
?>

<?= form_open('keranjang/edit') ?>
<!-- Table with stripped rows -->
<table class="table datatable">
    <thead>
        <tr>
            <th scope="col">Nama</th>
            <th scope="col">Foto</th>
            <th scope="col">Harga</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Subtotal</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        $totalDiskon = 0;
        if (!empty($items)) :
            foreach ($items as $index => $item) :
                $harga_asli  = $item['options']['harga_asli'] ?? $item['price'];
                $harga_final = $harga_asli - $nominalDiskon;
                $subtotal    = $harga_final * $item['qty'];
                $totalDiskon += $subtotal;
        ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td><img src="<?= base_url() . "img/" . $item['options']['foto'] ?>" width="100px"></td>
                    <td>
                        <?php if ($todayDiscount) : ?>
                            <span class="text-danger" style="text-decoration: line-through;">
                                <?= number_to_currency($harga_asli, 'IDR') ?>
                            </span><br>
                            <?= number_to_currency($harga_final, 'IDR') ?>
                        <?php else : ?>
                            <?= number_to_currency($harga_asli, 'IDR') ?>
                        <?php endif; ?>
                    </td>
                    <td><input type="number" min="1" name="qty<?= $i++ ?>" class="form-control" value="<?= $item['qty'] ?>"></td>
                    <td><?= number_to_currency($subtotal, 'IDR') ?></td>
                    <td>
                        <a href="<?= base_url('keranjang/delete/' . $item['rowid']) ?>" class="btn btn-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
        <?php
            endforeach;
        endif;
        ?>
    </tbody>
</table>

<div class="alert alert-info">
    <?= "Total = " . number_to_currency($todayDiscount ? $totalDiskon : $total, 'IDR') ?>
</div>

<button type="submit" class="btn btn-primary">Perbarui Keranjang</button>
<a class="btn btn-warning" href="<?= base_url() ?>keranjang/clear">Kosongkan Keranjang</a>
<?php if (!empty($items)) : ?>
    <a class="btn btn-success" href="<?= base_url() ?>checkout">Selesai Belanja</a>
<?php endif; ?>
<?= form_close() ?>

<?= $this->endSection() ?>