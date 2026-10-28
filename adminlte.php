<?php
// koneksi ke databases
require_once __DIR__ . "/koneksi.php";

// ambil kata pencarian, filter predikat, urutan, dan halaman dari URL
$q        = isset($_GET['q']) ? trim($_GET['q']) : '';
$predikat = isset($_GET['predikat']) ? trim($_GET['predikat']) : 'all';
$sort     = (isset($_GET['sort']) && in_array($_GET['sort'], ['asc', 'desc'])) ? $_GET['sort'] : '';
$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit    = 9; // jumlah data per halaman

// bikin link yang tetap membawa pencarian, filter, urutan, dan halaman
function urlHal($ubah = []) {
  $p = array_merge($_GET, $ubah);
  foreach ($p as $k => $v) {
    if ($v === '' || $v === null || ($k === 'predikat' && $v === 'all')) unset($p[$k]);
  }
  return '?' . http_build_query($p);
}

// syarat pencarian nama dan filter predikat
$where = [];
if ($q !== '') {
  $where[] = "nama LIKE '%" . mysqli_real_escape_string($conn, $q) . "%'";
}
if ($predikat !== '' && $predikat !== 'all') {
  $where[] = "LOWER(predikat) = '" . mysqli_real_escape_string($conn, strtolower($predikat)) . "'";
}
$whereSql = $where ? " WHERE " . implode(" AND ", $where) : "";

// hitung total data untuk pagination
$resCount   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM nilai_mhsw" . $whereSql);
$total      = (int) mysqli_fetch_assoc($resCount)['total'];
$totalPages = max(1, (int) ceil($total / $limit));
if ($page > $totalPages) { $page = $totalPages; }
$offset     = ($page - 1) * $limit;

// urutkan nilai akhir: desc = besar ke kecil, asc = kecil ke besar
// kalau tidak diurutkan, data urut berdasarkan tanggal dibuat
$orderSql = $sort
  ? " ORDER BY CAST(nilai_akhir AS DECIMAL(10,2)) " . strtoupper($sort) . ", reg_date ASC"
  : " ORDER BY reg_date ASC";

// ambil data sesuai halaman yang dibuka
$sql = "SELECT * FROM nilai_mhsw" . $whereSql . $orderSql . " LIMIT $limit OFFSET $offset";
// Jalankan query SQL
$result = mysqli_query($conn, $sql);

// ambil daftar foto di folder assets/img untuk pilihan foto
$daftarFoto = [];
foreach (glob(__DIR__ . '/assets/img/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) as $file) {
  $daftarFoto[] = 'assets/img/' . basename($file);
}


?>

<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Users</title>

    <!--begin::Theme Init (prevents flash of incorrect theme on load, #6043)-->
    <script>
      (() => {
        'use strict';
        const root = document.documentElement;

        // Applications with their own theming opt out of AdminLTE's color mode
        // entirely, here as well as in the bundle.
        if (root.getAttribute('data-lte-color-mode') === 'off') {
          return;
        }

        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try {
          stored = localStorage.getItem(STORAGE_KEY);
        } catch {
          // localStorage may be unavailable (private mode, sandboxed iframe).
        }
        // Mirror the precedence in color-mode.ts: the visitor's stored choice
        // wins, then a theme this page declared itself, then the OS preference.
        const authored = root.getAttribute('data-bs-theme');
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') {
          resolved = stored;
        } else if (authored === 'dark' || authored === 'light') {
          resolved = authored;
        } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
          resolved = 'dark';
        }
        root.setAttribute('data-bs-theme', resolved);
        root.style.colorScheme = resolved;
        // Flag values computed here, so the bundle does not mistake them for a
        // theme the page declared and stop following the OS preference.
        if (resolved !== authored) {
          root.setAttribute('data-lte-theme-resolved', '');
        }
      })();
    </script>
    <!--end::Theme Init-->

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE 4 | Users" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a free Bootstrap 5 admin dashboard template with almost 50 example pages, built with vanilla JS and designed with accessibility in mind."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel"
    />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="./css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="./css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        
      </nav>
      <!--end::Header-->
      
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Rekap Nilai Mahasiswa</h1>
              </div>
              <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nilai</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-12">
                <!--begin::Card-->
                <div class="card mb-4">
                  <!--begin::Card Header-->
                  <div class="card-header">
                    <div class="row g-2 align-items-center">
                      <div class="col-12 col-md-4">
                        <h3 class="card-title">Directory Nilai</h3>
                      </div>
                      <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                          <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text">
                              <i class="bi bi-search" aria-hidden="true"></i>
                            </span>
                            <input
                              type="search"
                              id="user-search"
                              class="form-control"
                              placeholder="Cari Berdasarkan nama"
                              aria-label="Search name"
                              style="width: 180px"
                            />
                          </div>
                          <select
                            id="user-role-filter"
                            class="form-select form-select-sm w-auto"
                            aria-label="Filter by role"
                          >
                            <option value="all" selected>Semua Predikat</option>
                            <option value="sangat memuaskan">Sangat Memuaskan</option>
                            <option value="memuaskan">Memuaskan</option>
                            <option value="cukup">Cukup</option>
                            <option value="kurang">Kurang</option>
                            <option value="sangat kurang">Sangat Kurang</option>
                          </select>
                          <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-add-user"
                          >
                            <i class="bi bi-person-plus-fill me-1" aria-hidden="true"> </i>
                            Tambah Data Nilai
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--end::Card Header-->

<?php
if (mysqli_num_rows($result) > 0) {

?>

                  <!--begin::Card Body-->
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle m-0">
                        <thead>
                          <tr>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <!-- klik judul kolom nilai akhir untuk mengurutkan -->
                            <th>
                              <a
                                href="<?php echo urlHal(['sort' => ($sort === 'desc') ? 'asc' : 'desc', 'page' => 1]); ?>"
                                class="text-decoration-none text-reset"
                                title="Urutkan nilai (besar ke kecil / sebaliknya)"
                              >
                                Nilai Akhir
                                <i
                                  class="bi <?php echo ($sort === 'asc') ? 'bi-sort-numeric-down' : (($sort === 'desc') ? 'bi-sort-numeric-up-alt' : 'bi-arrow-down-up'); ?>"
                                  aria-hidden="true"
                                ></i>
                              </a>
                            </th>
                            <th>Nilai Huruf</th>
                            <th>Predikat</th>
                            <th>Tgl dibuat</th>
                            <th class="text-end">Actions</th>
                          </tr>
                        </thead>
                        
                        <?php
                        while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tbody>
                          <tr>
                            <td>
                              
                                <span class="fw-medium">
                                    <?php echo $row["nim"]; ?>
                                </span>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <!-- foto dari database, kalau kosong pakai foto bawaan -->
                                <img
                                  src="<?php echo !empty($row['foto']) ? htmlspecialchars($row['foto']) : './assets/img/user1-128x128.jpg'; ?>"
                                  alt="Foto <?php echo htmlspecialchars($row['nama']); ?>"
                                  class="img-size-32 rounded-circle me-2"
                                />
                                <span class="fw-medium"><?php echo $row["nama"]; ?></span>
                              </div>
                            </td>
                            <td>
                                <span class="fw-medium"><?php echo $row["nilai_akhir"]; ?></span>
                            </td>
                            <td>
                              <span class="fw-medium"><?php echo $row["nilai_huruf"]; ?></span>
                            </td>
                            <td>
                              <span class="fw-medium"><?php echo $row["predikat"]; ?></span>
                            </td>
                            <td>
                                <span class="fw-medium">T<?php echo $row["reg_date"]; ?></span>
                            </td>
                            <td class="text-end">
                              <div class="btn-group btn-group-sm">
                                <!-- data baris dikirim ke popup edit lewat atribut data- -->
                                <button
                                  type="button"
                                  class="btn btn-outline-secondary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#modal-edit-user"
                                  data-nim="<?php echo htmlspecialchars($row['nim']); ?>"
                                  data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
                                  data-nilai="<?php echo htmlspecialchars($row['nilai_akhir']); ?>"
                                  data-foto="<?php echo htmlspecialchars($row['foto'] ?? ''); ?>"
                                  aria-label="Edit <?php echo htmlspecialchars($row['nama']); ?>"
                                >
                                  <i class="bi bi-pencil" aria-hidden="true"> </i>
                                </button>
                                <button
                                  type="button"
                                  class="btn btn-outline-danger"
                                  data-bs-toggle="modal"
                                  data-bs-target="#modal-delete-user"
                                  data-nim="<?php echo htmlspecialchars($row['nim']); ?>"
                                  data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
                                  aria-label="Hapus <?php echo htmlspecialchars($row['nama']); ?>"
                                >
                                  <i class="bi bi-trash" aria-hidden="true"> </i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        </tbody>

                        <?php
                        }
                        ?>
                    </table>

<?php
} else {
  echo '<div class="card-body"><div class="table-responsive"><p class="text-center text-body-secondary p-4 m-0">0 results - Data tidak ditemukan</p>';
}
mysqli_close($conn);
?>
                    </div>
                    <!-- /.table-responsive -->
                  </div>
                  <!--end::Card Body-->
                  <!--begin::Card Footer-->
                  <div class="card-footer clearfix">
                    <div class="float-start pt-1 fs-7 text-body-secondary">
                      Showing <?php echo $total ? $offset + 1 : 0; ?> to <?php echo min($offset + $limit, $total); ?> of <?php echo $total; ?> users
                    </div>
                    <!-- pagination: tombol sebelumnya, nomor halaman, dan berikutnya -->
                    <ul class="pagination pagination-sm m-0 float-end">
                      <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo urlHal(['page' => max(1, $page - 1)]); ?>" aria-label="Previous"> &laquo; </a>
                      </li>
                      <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                      <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo urlHal(['page' => $i]); ?>"><?php echo $i; ?></a>
                      </li>
                      <?php } ?>
                      <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo urlHal(['page' => min($totalPages, $page + 1)]); ?>" aria-label="Next"> &raquo; </a>
                      </li>
                    </ul>
                  </div>
                  <!--end::Card Footer-->
                </div>
                <!--end::Card-->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->

            <!--begin::Add User Modal-->
            <div
              class="modal fade"
              id="modal-add-user"
              tabindex="-1"
              aria-labelledby="modal-add-user-label"
              aria-hidden="true"
            >
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="post" action="tambahnilai.php">
                    <input type="hidden" name="mode" value="tambah" />
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-add-user-label">Tambah Data Nilai</h5>
                      <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                      ></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="new-nim" class="form-label">
                          NIM <span class="text-danger">*</span>
                        </label>
                        <input
                          type="text"
                          class="form-control"
                          id="new-nim"
                          name="nim"
                          placeholder="e.g. 3202416001"
                          required
                        />
                      </div>
                      <div class="mb-3">
                        <label for="new-nama" class="form-label">
                          Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input
                          type="text"
                          class="form-control"
                          id="new-nama"
                          name="nama"
                          placeholder="e.g. Jane Doe"
                          required
                        />
                      </div>
                      <div class="mb-3">
                        <label for="new-foto" class="form-label">Foto</label>
                        <div class="d-flex align-items-center gap-3">
                          <img
                            id="new-foto-preview"
                            src="./assets/img/user1-128x128.jpg"
                            alt="Pratinjau foto"
                            class="rounded-circle"
                            style="width: 48px; height: 48px; object-fit: cover"
                          />
                          <select class="form-select" id="new-foto" name="foto">
                            <option value="">Foto bawaan</option>
                            <?php foreach ($daftarFoto as $f) { ?>
                            <option value="<?php echo htmlspecialchars($f); ?>">
                              <?php echo htmlspecialchars(basename($f)); ?>
                            </option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="new-nilai" class="form-label">
                          Nilai Akhir <span class="text-danger">*</span>
                        </label>
                        <input
                          type="number"
                          class="form-control"
                          id="new-nilai"
                          name="nilai_akhir"
                          step="0.01"
                          min="0"
                          max="100"
                          placeholder="0 - 100"
                          required
                        />
                        <div class="form-text">
                          Nilai huruf dan predikat dihitung otomatis dari nilai akhir.
                        </div>
                      </div>
                      <div class="form-text"><span class="text-danger">*</span> wajib diisi</div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                      </button>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!--end::Add User Modal-->

            <!-- popup edit data nilai, NIM tidak bisa diubah -->
            <div
              class="modal fade"
              id="modal-edit-user"
              tabindex="-1"
              aria-labelledby="modal-edit-user-label"
              aria-hidden="true"
            >
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="post" action="editnilai.php">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-edit-user-label">Edit Data Nilai</h5>
                      <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                      ></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="edit-nim" class="form-label">NIM</label>
                        <input
                          type="text"
                          class="form-control"
                          id="edit-nim"
                          name="nim"
                          readonly
                        />
                        <div class="form-text">NIM tidak dapat diubah.</div>
                      </div>
                      <div class="mb-3">
                        <label for="edit-nama" class="form-label">
                          Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input
                          type="text"
                          class="form-control"
                          id="edit-nama"
                          name="nama"
                          required
                        />
                      </div>
                      <div class="mb-3">
                        <label for="edit-foto" class="form-label">Foto</label>
                        <div class="d-flex align-items-center gap-3">
                          <img
                            id="edit-foto-preview"
                            src="./assets/img/user1-128x128.jpg"
                            alt="Pratinjau foto"
                            class="rounded-circle"
                            style="width: 48px; height: 48px; object-fit: cover"
                          />
                          <select class="form-select" id="edit-foto" name="foto">
                            <option value="">Foto bawaan</option>
                            <?php foreach ($daftarFoto as $f) { ?>
                            <option value="<?php echo htmlspecialchars($f); ?>">
                              <?php echo htmlspecialchars(basename($f)); ?>
                            </option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="edit-nilai" class="form-label">
                          Nilai Akhir <span class="text-danger">*</span>
                        </label>
                        <input
                          type="number"
                          class="form-control"
                          id="edit-nilai"
                          name="nilai_akhir"
                          step="0.01"
                          min="0"
                          max="100"
                          placeholder="0 - 100"
                          required
                        />
                        <div class="form-text">
                          Nilai huruf dan predikat dihitung otomatis dari nilai akhir.
                        </div>
                      </div>
                      <div class="form-text"><span class="text-danger">*</span> wajib diisi</div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                      </button>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!--begin::Delete User Modal-->
            <div
              class="modal fade"
              id="modal-delete-user"
              tabindex="-1"
              aria-labelledby="modal-delete-user-label"
              aria-hidden="true"
            >
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modal-delete-user-label">Hapus Data Nilai</h5>
                    <button
                      type="button"
                      class="btn-close"
                      data-bs-dismiss="modal"
                      aria-label="Close"
                    ></button>
                  </div>
                  <div class="modal-body">
                    <p class="mb-0">
                      Yakin ingin menghapus data nilai
                      <strong id="hapus-nama"></strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      Batal
                    </button>
                    <a href="#" id="btn-konfirmasi-hapus" class="btn btn-danger">Ya, Hapus</a>
                  </div>
                </div>
              </div>
            </div>
            <!--end::Delete User Modal-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">
            Pemrograman Web Semester 5 - Desain menggunakan
            <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Hak Cipta &copy; Pada Prodi TI 2026&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        Seluruh Hak Cipta Dilindungi.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="./js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // isi kolom cari dan filter sesuai isi URL
        const params = new URLSearchParams(window.location.search);
        const search = document.getElementById('user-search');
        const filter = document.getElementById('user-role-filter');

        search.value = params.get('q') || '';
        filter.value = params.get('predikat') || 'all';

        // pindah halaman dengan membawa kata pencarian dan filter predikat
        function terapkan() {
          const q = search.value.trim();
          if (q === (params.get('q') || '') && filter.value === (params.get('predikat') || 'all')) {
            return;
          }
          const p = new URLSearchParams(window.location.search);
          if (q) { p.set('q', q); } else { p.delete('q'); }
          if (filter.value !== 'all') { p.set('predikat', filter.value); } else { p.delete('predikat'); }
          p.delete('page');
          window.location.search = p.toString();
        }

        search.addEventListener('keydown', function (e) {
          if (e.key === 'Enter') { e.preventDefault(); terapkan(); }
        });
        search.addEventListener('search', terapkan);
        filter.addEventListener('change', terapkan);
      });
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // kolom NIM langsung aktif saat popup tambah dibuka
        const modalTambah = document.getElementById('modal-add-user');
        modalTambah.addEventListener('shown.bs.modal', function () {
          document.getElementById('new-nim').focus();
        });

        // foto bawaan kalau mahasiswa belum punya foto
        const fotoBawaan = './assets/img/user1-128x128.jpg';
        // ubah gambar pratinjau saat foto dipilih
        const pilihFoto = document.getElementById('edit-foto');
        const pratinjauFoto = document.getElementById('edit-foto-preview');
        pilihFoto.addEventListener('change', function () {
          pratinjauFoto.src = pilihFoto.value || fotoBawaan;
        });

        const pilihFotoBaru = document.getElementById('new-foto');
        const pratinjauFotoBaru = document.getElementById('new-foto-preview');
        pilihFotoBaru.addEventListener('change', function () {
          pratinjauFotoBaru.src = pilihFotoBaru.value || fotoBawaan;
        });

        // isi popup edit sesuai data baris yang diklik
        const modalEdit = document.getElementById('modal-edit-user');
        modalEdit.addEventListener('show.bs.modal', function (e) {
          const tombol = e.relatedTarget;
          document.getElementById('edit-nim').value = tombol.getAttribute('data-nim');
          document.getElementById('edit-nama').value = tombol.getAttribute('data-nama');
          document.getElementById('edit-nilai').value = tombol.getAttribute('data-nilai');
          pilihFoto.value = tombol.getAttribute('data-foto') || '';
          pratinjauFoto.src = pilihFoto.value || fotoBawaan;
        });
        modalEdit.addEventListener('shown.bs.modal', function () {
          document.getElementById('edit-nama').focus();
        });

        // isi nama dan link hapus sesuai baris yang diklik
        const modalHapus = document.getElementById('modal-delete-user');
        modalHapus.addEventListener('show.bs.modal', function (e) {
          const tombol = e.relatedTarget;
          const nim = tombol.getAttribute('data-nim');
          const nama = tombol.getAttribute('data-nama');
          document.getElementById('hapus-nama').textContent = nama + ' (' + nim + ')';
          document.getElementById('btn-konfirmasi-hapus').href =
            'hapusnilai.php?nim=' + encodeURIComponent(nim);
        });
      });
    </script>
    <!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

        // Disable OverlayScrollbars on mobile devices to prevent touch interference
        const isMobile = window.innerWidth <= 992;

        if (
          sidebarWrapper &&
          OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
          !isMobile
        ) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!--begin::Color Mode Toggle-->
    <!-- The light/dark/auto switcher ships in adminlte.js as the ColorMode
     module (since 4.1) — no page script needed. Only the no-flash snippet
     in <head> stays inline, because it must run before first paint. -->
    <!--end::Color Mode Toggle-->

    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>