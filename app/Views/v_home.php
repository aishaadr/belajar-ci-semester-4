<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashData('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Table with stripped rows -->
<div class="row">
    <?php foreach ($products as $key => $item) : ?>
        <div class="col-lg-6">
            <?= form_open('keranjang') ?>
            <?php
            $harga_final = $item['harga'];
            if ($todayDiscount) {
                $harga_final = $item['harga'] - $todayDiscount['nominal'];
            }
            ?>
            <?= form_hidden([
                'id'    => (string)$item['id'],
                'nama'  => (string)$item['nama'],
                'harga' => (string)$harga_final,
                'foto'  => (string)$item['foto']]) ?>
            <div class="card">
                <div class="card-body">
                    <img src="<?= base_url() . "img/" . $item['foto'] ?>" alt="..." width="50%">
                    <h5 class="card-title">
                        <?= $item['nama'] ?><br>
                        <?php if ($todayDiscount) : ?>
                            <span class="text-danger" style="text-decoration: line-through;"><?= number_to_currency($item['harga'], 'IDR') ?></span>
                            <span><?= number_to_currency($harga_final, 'IDR') ?></span>
                        <?php else : ?>
                            <?= number_to_currency($item['harga'], 'IDR') ?>
                        <?php endif; ?>
                    </h5>
                    <button type="submit" class="btn btn-info rounded-pill">Beli</button>
                </div>
            </div>
            <?= form_close() ?>
        </div>
    <?php endforeach ?>
</div>
<!-- End Table with stripped rows -->

<?= $this->endSection() ?>