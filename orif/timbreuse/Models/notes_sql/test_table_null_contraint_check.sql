CREATE TABLE t1
(
  c1 INT CHECK (c1 < c2),
  c2 INT CHECK (c2 > c1)
);
