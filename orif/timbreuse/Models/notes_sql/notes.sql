SELECT id_badge FROM badge
WHERE id_user = '92';

SELECT * from log
where id_badge = 589402514225;

SELECT * from log
where id_badge in
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
);


SELECT * FROM log
WHERE (DAY(date) = '18') AND
(MONTH(date) = '05') AND
(YEAR(date) = '2022');


SELECT WEEKOFYEAR(date) from log;
/*
day
*/
SELECT * FROM log
WHERE id_badge IN
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
) 
AND
(DAY(date) = '17') AND
(MONTH(date) = '05') AND
(YEAR(date) = '2022');

/*
week
*/
SELECT * FROM log
WHERE id_badge IN
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
) 
AND
(WEEKOFYEAR(date) = '20') AND
(YEAR(date) = '2022');



/*
month
*/
SELECT * FROM log
WHERE id_badge IN
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
) 
AND
(MONTH(date) = '05') AND
(YEAR(date) = '2022');

------------------------------

SELECT * FROM log
WHERE id_badge IN
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
) 
AND date > '2022-05-18 07:00'
AND date < '2022-05-18 12:30';

SELECT * FROM log
WHERE id_badge IN
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
) 
AND date > '2022-05-18 12:30'
AND date < '2022-05-18 17:45';

-----------------------------------

SELECT * FROM log
WHERE id_badge IN
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
) 
AND date > '2022-05-18 12:30'
AND date < '2022-05-18 17:45'
AND inside = 1
LIMIT 1;

-----------------------------------

SELECT * FROM log
WHERE id_badge IN
(
    SELECT id_badge FROM badge
    WHERE id_user = '92'
) 
AND date > '2022-05-18 12:30'
AND date < '2022-05-18 17:45'
AND inside = 0
ORDER BY date DESC LIMIT 1;
----------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `access_tim_user`
(
    `id_access` int NOT NULL AUTO_INCREMENT,
    `id_user` int(11) NOT NULL,
    `id_ci_user` int(10) unsigned NOT NULL,
    PRIMARY KEY(`id_access`),
    FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`),
    FOREIGN KEY (`id_ci_user`) REFERENCES `ci_user`(`id`)
);

INSERT INTO `access_tim_user` (`id_user`, `id_ci_user`)
VALUES (92, 8);