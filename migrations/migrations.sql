-- Create USER table
CREATE TABLE user (
  `id_user` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `usertype` varchar(20) NOT NULL
);

-- Create USER table
CREATE TABLE user (
  `id_user` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `usertype` varchar(20) NOT NULL
);

-- Create INFAQ table
CREATE TABLE infaq (
    id_infaq VARCHAR(10) PRIMARY KEY,
    jenis_infaq CHAR(30),
    tgl_infaq DATE,
    jml_infaq INT(12),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create DONASI table
CREATE TABLE donasi (
    id_donasi VARCHAR(10) PRIMARY KEY,
    nama_donatur CHAR(30),
    tgl_donasi DATE,
    jml_donasi INT(12),
    file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create KAS_MASUK table
CREATE TABLE kas_masuk (
    id_kasmasuk VARCHAR(10) PRIMARY KEY,
    tgl_kasmasuk DATE,
    jml_kasmasuk INT(12),
    ket_kasmasuk VARCHAR(100),
    jenis_kasmasuk VARCHAR(100),
    id_infaq VARCHAR(10),
    id_donasi VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create KAS_KELUAR table
CREATE TABLE kas_keluar (
    id_kaskeluar VARCHAR(10) PRIMARY KEY,
    tgl_kaskeluar DATE,
    jml_kaskeluar INT(12),
    ket_kaskeluar VARCHAR(30),
    jenis_kaskeluar VARCHAR(100),
    id_transaksi_keluar VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create KAS table
CREATE TABLE kas (
    id_kas VARCHAR(10) PRIMARY KEY,
    id_kasmasuk VARCHAR(10),
    jml_kasmasuk INT(12),
    id_kaskeluar VARCHAR(10),
    jml_kaskeluar INT(12),
    tgl_kas DATE,
    saldo_kas INT(12),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create DETAIL_TRANSAKSI_KELUAR table
CREATE TABLE detail_transaksi_keluar (
    id_transaksi_keluar VARCHAR(10) PRIMARY KEY,
    jenis_transaksi_keluar VARCHAR(100),
    tgl_transaksi_keluar DATE,
    jml_transaksi_keluar INT(12),
        file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `usertype`) VALUES
(1, 'admin', 'admin', '$2y$10$GWq1Nh5D2CGStBP50cx04.Qs7w59IypmxKT6BoGFOPnMr1VYgNjr.', 'admin'),
(2, 'user', 'user', '$2y$10$GWq1Nh5D2CGStBP50cx04.Qs7w59IypmxKT6BoGFOPnMr1VYgNjr.', 'user');