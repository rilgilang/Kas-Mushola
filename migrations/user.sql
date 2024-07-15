CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `usertype` varchar(20) NOT NULL,
   PRIMARY KEY (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  