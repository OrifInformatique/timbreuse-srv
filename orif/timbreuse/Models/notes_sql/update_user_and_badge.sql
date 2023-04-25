DELIMITER //
CREATE PROCEDURE `update_user_and_badge`(new_id_badge bigint, _id_user int,
    _name text CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci, 
    _surname text CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci)
MODIFIES SQL DATA
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE `user_sync`
    SET `name`=_name `surname`=_surname;

    UPDATE `badge_sync`
    SET `id_user`=NULL WHERE `id_user`=_id_user;

    UPDATE `badge_sync`
    SET `id_user`=_id_user; WHERE `id_badge`=new_id_badge;

    COMMIT;
END //
DELIMITER ;
