<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == '') ? "" : "collapsed" ?>" href="/">
                <i class="bi bi-grid"></i>
                <span>Home</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'keranjang') ? "" : "collapsed" ?>" href="keranjang">
                <i class="bi bi-cart-check"></i>
                <span>Keranjang</span>
            </a>
        </li>

        <?php if (session()->get('role') == 'admin') : ?>
        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'produk') ? "" : "collapsed" ?>" href="produk">
                <i class="bi bi-receipt"></i>
                <span>Produk</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'diskon') ? "" : "collapsed" ?>" href="diskon">
                <i class="bi bi-tag"></i>
                <span>Diskon</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'pembelian') ? "" : "collapsed" ?>" href="pembelian">
                <i class="bi bi-bag-check"></i>
                <span>Pembelian</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'history') ? "" : "collapsed" ?>" href="history">
                <i class="bi bi-person"></i>
                <span>History</span>
            </a>
        </li>

    </ul>

</aside><!-- End Sidebar-->