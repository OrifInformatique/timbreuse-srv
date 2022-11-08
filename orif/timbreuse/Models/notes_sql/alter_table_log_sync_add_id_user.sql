ALTER TABLE `log_sync` ADD `id_user` int(11);
ALTER TABLE `log_sync` ADD
FOREIGN KEY (`id_user`) REFERENCES `user_sync` (`id_user`)