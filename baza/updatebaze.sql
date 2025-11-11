-- 1. USTVARITEV NOVE TABELE ZA RAZREDE
-- --------------------------------------------------------
CREATE TABLE `razredi` (
  `id` int(11) NOT NULL,
  `ime_razreda` varchar(10) NOT NULL, -- Ime razreda, npr. 'r1a', 'e2a'
  `opis` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dodajanje primarnega ključa in auto-increment
ALTER TABLE `razredi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ime_razreda_UNIQUE` (`ime_razreda`);

ALTER TABLE `razredi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


-- 2. DODAJANJE STOLPCA `id_razreda` V TABELO `uporabniki`
-- --------------------------------------------------------
ALTER TABLE `uporabniki`
  ADD `id_razreda` int(11) NULL COMMENT 'Povezava na tabelo razredi' AFTER `vloga`;

-- Dodajanje tujega ključa za povezavo z razredi
ALTER TABLE `uporabniki`
  ADD CONSTRAINT `uporabniki_ibfk_razred` FOREIGN KEY (`id_razreda`) REFERENCES `razredi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;


-- 3. VSTAVITEV ZAHTEVANIH RAZREDOV
-- --------------------------------------------------------
INSERT INTO `razredi` (`ime_razreda`, `opis`) VALUES
('r1a', 'Redni program, 1. letnik, oddelek A'),
('r1b', 'Redni program, 1. letnik, oddelek B'),
('r2a', 'Redni program, 2. letnik, oddelek A'),
('r2b', 'Redni program, 2. letnik, oddelek B'),
('r3a', 'Redni program, 3. letnik, oddelek A'),
('r3b', 'Redni program, 3. letnik, oddelek B'),
('r4a', 'Redni program, 4. letnik, oddelek A'),
('r4b', 'Redni program, 4. letnik, oddelek B'),
('e1a', 'Izredni program, 1. letnik, oddelek A'),
('e2a', 'Izredni program, 2. letnik, oddelek A'),
('e3a', 'Izredni program, 3. letnik, oddelek A'),
('e4a', 'Izredni program, 4. letnik, oddelek A');


-- 4. POSODOBITEV OBSTOJEČIH UČENCEV V RAZREDE (na podlagi ID-jev iz baze.sql)
-- Predpostavljamo, da so razredi vstavljeni z ID 1 do 12 (kot v točki 3)
-- in da so učenci v bazi s ID 22 do 108.
-- --------------------------------------------------------
-- Učenci 22-31 v r1a (ID 1)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r1a') WHERE `id` BETWEEN 22 AND 31;
-- Učenci 32-41 v r1b (ID 2)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r1b') WHERE `id` BETWEEN 32 AND 41;
-- Učenci 42-51 v r2a (ID 3)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r2a') WHERE `id` BETWEEN 42 AND 51;
-- Učenci 52-61 v r2b (ID 4)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r2b') WHERE `id` BETWEEN 52 AND 61;
-- Učenci 62-71 v r3a (ID 5)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r3a') WHERE `id` BETWEEN 62 AND 71;
-- Učenci 72-81 v r3b (ID 6)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r3b') WHERE `id` BETWEEN 72 AND 81;
-- Učenci 82-91 v r4a (ID 7)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r4a') WHERE `id` BETWEEN 82 AND 91;
-- Učenci 92-101 v r4b (ID 8)
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'r4b') WHERE `id` BETWEEN 92 AND 101;
-- Ostale učence razporedimo v izredne programe
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'e1a') WHERE `id` BETWEEN 102 AND 103;
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'e2a') WHERE `id` BETWEEN 104 AND 105;
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'e3a') WHERE `id` BETWEEN 106 AND 107;
UPDATE `uporabniki` SET `id_razreda` = (SELECT id FROM `razredi` WHERE `ime_razreda` = 'e4a') WHERE `id` = 108;
