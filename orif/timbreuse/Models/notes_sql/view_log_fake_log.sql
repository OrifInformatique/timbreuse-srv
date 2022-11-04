-- 20221101154958

-- before
-- CREATE VIEW log_fake_log AS 
-- SELECT `date`, `id_user`, `inside`, `id_fake_log` FROM `fake_log`
-- UNION
-- SELECT `date`, `id_user`, `inside`, NULL FROM `log`, `badge` WHERE `badge`.`id_badge` = `log`.`id_badge` 
-- ORDER BY `date`;

-- new

DROP VIEW IF EXISTS `log_fake_log`;
CREATE VIEW `log_fake_log` AS 
SELECT `date`, `id_user`, `inside`, `id_fake_log` FROM `fake_log`
UNION
SELECT `date`, COALESCE(`log_sync`.`id_user`, `badge_sync`.`id_user`),
    `inside`, NULL
FROM `log_sync` LEFT OUTER JOIN `badge_sync`
ON `log_sync`.`id_badge` = `badge_sync`.`id_badge`;