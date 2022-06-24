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
