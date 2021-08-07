<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container">
    <div class="row">
        <div class="col">

            <h2 class="mt-3">Detail Komik</h2>
            <div class="card my-3" style="width: 18rem;">
                <img src="/img/<?= $komik['sampul'] ?>" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title"><?= $komik['judul']; ?></h5>
                    <p class="card-text"><b>Penulis: </b><?= $komik['penulis']; ?></p>
                    <p class="card-text"><b>Penerbit: </b><?= $komik['penerbit']; ?></p>
                    <a href="/komik/edit/<?= $komik['slug']?>" class="btn btn-primary"><i class="fas fa-edit"></i></a>

                    <form action="/komik/<?= $komik['id']?>" method="post" class="d-inline">
                    <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah anda yakin akan menghapus data ini?');">Delete</button>
                    </form>
                
                    <a href="<?= base_url('/komik') ?>" class="btn btn-success">Cancel</a>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>