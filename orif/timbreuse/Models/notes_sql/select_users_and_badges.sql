
DELIMITER //
CREATE PROCEDURE `select_users_and_badges`(start_id_user INT) 
BEGIN
    SELECT `id_badge`, `badge_sync`.`id_user`, `name`, `surname`
    FROM  `badge_sync` LEFT OUTER JOIN `user_sync` 
    ON `badge_sync`.`id_user` = `user_sync`.`id_user`
    WHERE `badge_sync`.`id_user` > start_id_user;
END //
DELIMITER ;

# test
CALL `select_users_and_badges`(43);