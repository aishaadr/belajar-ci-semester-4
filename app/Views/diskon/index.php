<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashData('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashData('errors')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach (session()->getFlashData('errors') as $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
    Tambah Data
</button>

<table class="table datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Nominal</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($discounts as $i => $item) : ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= $item['tanggal'] ?></td>
                <td><?= $item['nominal'] ?></td>
                <td>
                    <button class="btn btn-success btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEdit"
                        data-id="<?= $item['id'] ?>"
                        data-tanggal="<?= $item['tanggal'] ?>"
                        data-nominal="<?= $item['nominal'] ?>">
                        Ubah
                    </button>
                    <a href="diskon/delete/<?= $item['id'] ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus data diskon ini?')">
                        Hapus
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?= form_open('diskon') ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nominal</label>
                    <input type="number" name="nominal" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?= form_open('') ?>
            <input type="hidden" name="_method" value="POST">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal_display" id="editTanggal" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nominal</label>
                    <input type="number" name="nominal" id="editNominal" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    // Set action form edit dan isi data saat modal dibuka
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function (event) {
        const btn    = event.relatedTarget;
        const id      = btn.getAttribute('data-id');
        const tanggal = btn.getAttribute('data-tanggal');
        const nominal = btn.getAttribute('data-nominal');

        document.getElementById('editTanggal').value = tanggal;
        document.getElementById('editNominal').value  = nominal;

        const form = this.querySelector('form');
        form.action = 'diskon/edit/' + id;
    });
</script>

<?= $this->endSection() ?>