# stored procedure for remote database
# insert badge and user with procedure is like a atomic transaction
# bug on infomaniak with collate
DELIMITER //
CREATE PROCEDURE `insert_badge_and_user`(_id_badge bigint,
    _name text COLLATE utf8mb4_unicode_ci,
    _surname text COLLATE utf8mb4_unicode_ci) 
 MODIFIES SQL DATA
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;
    INSERT INTO `user_sync` (`name`, `surname`) VALUES (_name, _surname);
    INSERT INTO `badge_sync` (`id_badge`, `id_user`) VALUES
    (_id_badge, (SELECT `id_user` FROM `user_sync` WHERE `name` = _name AND
    `surname` = _surname));
    COMMIT;
END //
DELIMITER ;

# test
# CALL `insert_badge_and_user`(43, 'a', 'b');