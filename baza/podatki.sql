INSERT INTO `predmeti` (`id`, `ime`, `koda`, `opis`, `status`) VALUES
(1, 'Matematika', 'MAT', 'Osnove matematike in linearna algebra', 'aktiven'),
(2, 'Slovenski jezik', 'SLO', 'Slovenska slovnica in književnost', 'aktiven'),
(3, 'Angleščina', 'ANG', 'Angleški jezik', 'aktiven'),
(4, 'Fizika', 'FIZ', 'Osnove sile fizike', 'aktiven'),
(5, 'Kemija', 'KEM', 'Kemijske reakcije in snovi', 'aktiven'),
(6, 'Zgodovina', 'ZGO', 'Svetovna in slovenska zgodovina', 'aktiven'),
(7, 'Geografija', 'GEO', 'Zemljepis in reliefi', 'aktiven'),
(8, 'Računalništvo', 'NRP', 'Programiranje in računalniške osnove', 'aktiven'),
(9, 'Biologija', 'BIO', 'Živi svet in ekosistemi', 'aktiven'),
(10, 'Stroka moderne vsebine', 'SMV', 'Racunalnistvo teorija in praksa', 'aktiven');

INSERT INTO `uporabniki` (`id`, `ime`, `priimek`, `uporabnisko_ime`, `email`, `geslo`, `vloga`, `datum_registracije`, `status`) VALUES
(1, 'Admin', 'Sistemski', 'admin', 'admin@sola.si', 'geslo123', 'administrator', NOW(), 'aktiven'),

(2, 'Tijan', 'Antunovic', 'tantunovic', 'Tijan.Antunovic@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(3, 'Valentina', 'Hrastnik', 'vhrastnik', 'Valentina.Hrastnik@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(4, 'David', 'Brezovnik', 'dbrezovnik', 'David.Brezovnik@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(5, 'Gal', 'Drnovsek', 'gdernovsek', 'Gal.Drnovsek@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(6, 'Helena', 'Viher', 'hviher', 'irena.zupan@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(7, 'Jaka', 'Decman', 'jdecman', 'Jaka.Decman@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(8, 'Matic', 'Fijavz', 'mfijavz', 'tanja.kralj@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(9, 'Lubej', 'Bostjan', 'lbostjan', 'Lubej.Bostjan@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(10, 'Nik', 'Gorenjec', 'ngorenjec', 'Nik.Gorenjec@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(11, 'Mico', 'Hrastnik', 'mhrastnik', 'Mico.Hrastnik@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(12, 'Urh', 'Kolar', 'ukolar', 'Urh.Kolar@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(13, 'Jost', 'Klancnik', 'jklancnik', 'Jost.Klancnik@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(14, 'Luka', 'Janecek', 'ljanecek', 'Luka.Janecek@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(15, 'Tim', 'Krusic', 'tkrusic', 'Tim.Krusic@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(16, 'Rosana', 'Breznik', 'rbreznik', 'Rosana.Breznik@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(17, 'Jaka', 'Koren', 'jkoren', 'Jaka.Koren@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(18, 'Andrej', 'Mamedjarovic', 'amamedjarovic', 'Andrej.Mamedjarovic@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(19, 'Borut', 'Slemensek', 'bslemensek', 'Borut.Slemensek@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(20, 'David', 'Lukman', 'dlukman', 'David.Lukman@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),
(21, 'Miha', 'Stramsak', 'mstramsak', 'Miha.Stramsak@sola.si', 'geslo123', 'ucitelj', NOW(), 'aktiven'),

(22, 'Miha', 'Znidarsic', 'mznidarsic', 'Miha.Znidarsic@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(23, 'Srecko', 'Kosovel', 'srkosovel', 'Srecko@Kosovel.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(24, 'Slavko', 'Grum', 'sgrum', 'Slavko.Grum@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(25, 'Lovro', 'Kuhar', 'lkuhar', 'Lovro.Kuhar@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(26, 'Franz', 'Kafka', 'fkafka', 'Franz.Kafka@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(27, 'Anthony', 'Burgess', 'aburgess', 'Anthony.Burgess@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(28, 'Rebeka', 'Kuang', 'rkuang', 'Rebeka.Kuang@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(29, 'Janez', 'Golob', 'jgolob', 'janez.golob@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(30, 'Robert', 'Jansa', 'rjansa', 'Robert.JAnsa@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven'),
(31, 'Slavko', 'Kosovel', 'slkosovel', 'Slavko.Kosovel@dijak.si', 'geslo123', 'ucenec', NOW(), 'aktiven');

INSERT INTO `ucitelji_predmeti` (`id_ucitelja`, `id_predmeta`) VALUES
(2, 1), (2, 2), (3, 3), (3, 7),(4, 4), (4, 5), (5, 6),(6, 8), (6, 9), (7, 10), (8, 1), (8, 4), (9, 2), (9, 3),(10, 5), (10, 9), (11, 6), (11, 7), (12, 8), (13, 10), (14, 1), (14, 5), (15, 2), (15, 6), (16, 3), (16, 7), (17, 4), (17, 8), (18, 9), (18, 10),(19, 1), (19, 3),(20, 2), (20, 4), (21, 5), (21, 6); 

INSERT INTO `ucenci_predmeti` (`id_ucenca`, `id_predmeta`, `datum_vpisa`, `status`) VALUES
(22, 1, '2024-09-01', 'vpisano'), (22, 2, '2024-09-01', 'vpisano'), (22, 3, '2024-09-01', 'vpisano'),
(23, 1, '2024-09-01', 'vpisano'), (23, 4, '2024-09-01', 'vpisano'), (23, 8, '2024-09-01', 'vpisano'),
(24, 2, '2024-09-01', 'vpisano'), (24, 5, '2024-09-01', 'vpisano'), (24, 9, '2024-09-01', 'vpisano'),
(25, 3, '2024-09-01', 'vpisano'), (25, 6, '2024-09-01', 'vpisano'), (25, 10, '2024-09-01', 'vpisano'),
(26, 1, '2024-09-01', 'vpisano'), (26, 7, '2024-09-01', 'vpisano'), (26, 8, '2024-09-01', 'vpisano'),
(27, 2, '2024-09-01', 'vpisano'), (27, 4, '2024-09-01', 'vpisano'), (27, 9, '2024-09-01', 'vpisano'),
(28, 3, '2024-09-01', 'vpisano'), (28, 5, '2024-09-01', 'vpisano'), (28, 10, '2024-09-01', 'vpisano'),
(29, 1, '2024-09-01', 'vpisano'), (29, 6, '2024-09-01', 'vpisano'), (29, 7, '2024-09-01', 'vpisano'),
(30, 2, '2024-09-01', 'vpisano'), (30, 8, '2024-09-01', 'vpisano'), (30, 9, '2024-09-01', 'vpisano'),
(31, 3, '2024-09-01', 'vpisano'), (31, 4, '2024-09-01', 'vpisano'), (31, 10, '2024-09-01', 'vpisano');

INSERT INTO `naloge` (`id`, `naslov`, `navodila`, `rok_addaje`, `maksimalna_ocena`, `id_predmeta`, `id_avtorja`, `datum_objave`, `status`) VALUES
(1, 'Algebraične enačbe', 'Rešite naslednje algebraične enačbe in prikažite postopek reševanja.', '2024-10-15 23:59:00', 10, 1, 2, NOW(), 'aktiven'),
(2, 'Analiza pesmi', 'Analizirajte pesem \"Sonetni venec\" in opišite njene tematske značilnosti.', '2024-10-20 23:59:00', 10, 2, 8, NOW(), 'aktiven'),
(3, 'English Essay', 'Write a 300-word essay about your favorite hobby.', '2024-10-18 23:59:00', 10, 3, 3, NOW(), 'aktiven'),
(4, 'Newtonovi zakoni', 'Razložite Newtonove zakone gibanja s primeri iz vsakdanjega življenja.', '2024-10-22 23:59:00', 10, 4, 4, NOW(), 'aktiven'),
(5, 'Kemijske reakcije', 'Opišite različne vrste kemijskih reakcij in navedite primere.', '2024-10-25 23:59:00', 10, 5, 10, NOW(), 'aktiven');

INSERT INTO `gradiva` (`id`, `naslov`, `vsebina`, `tip`, `pot_do_datoteke`, `id_predmeta`, `id_avtorja`, `datum_objave`, `status`) VALUES
(1, 'Uvod v algebro', 'Osnove algebraičnih izrazov in enačb.', 'dokument', '/gradiva/matematika/uvod_algebra.pdf', 1, 2, NOW(), 'aktiven'),
(2, 'Slovenska slovnica', 'Pregled slovničnih pravil in razlag.', 'dokument', '/gradiva/slovenscina/slovnica.pdf', 2, 8, NOW(), 'aktiven'),
(3, 'English Grammar', 'Basic English grammar rules and exercises.', 'video', 'https://youtube.com/watch?v=abc123', 3, 3, NOW(), 'aktiven'),
(4, 'Mehanika tekočin', 'Osnove mehanike tekočin in hidrodinamike.', 'dokument', '/gradiva/fizika/tekočine.pdf', 4, 4, NOW(), 'aktiven'),
(5, 'Periodni sistem', 'Interaktivni periodni sistem elementov.', 'povezava', 'https://www.rsc.org/periodic-table', 5, 10, NOW(), 'aktiven');

INSERT INTO `oddaje` (`id_naloge`, `id_ucenca`, `datum_oddaje`, `pot_do_datoteke`, `originalno_ime_datoteke`, `status`) VALUES
(1, 22, '2024-10-10 14:30:00', '/oddaje/naloga1/Mlakar_Ana_Algebraične_enačbe.pdf', 'algebra_resitve.pdf', 'oddano'),
(1, 23, '2024-10-11 09:15:00', '/oddaje/naloga1/Kos_Nejc_Algebraične_enačbe.docx', 'matematika.docx', 'v_ocenjevanju'),
(2, 24, '2024-10-12 16:45:00', '/oddaje/naloga2/Zupančič_Eva_Analiza_pesmi.pdf', 'analiza_sonetni_venec.pdf', 'ocenjeno'),
(3, 25, '2024-10-09 11:20:00', '/oddaje/naloga3/Jerman_Matic_English_Essay.docx', 'my_hobby_essay.docx', 'oddano');

-- Tabela za ocene
CREATE TABLE `ocene` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_ucenca` INT NOT NULL,
    `id_naloge` INT NOT NULL,
    `ocena` DECIMAL(5, 2) NOT NULL,
    `komentar` TEXT,
    `datum_ocenjevanja` DATETIME DEFAULT NOW(),
    FOREIGN KEY (`id_ucenca`) REFERENCES `uporabniki`(`id`),
    FOREIGN KEY (`id_naloge`) REFERENCES `naloge`(`id`)
);

-- Tabela za pravice uporabnikov
CREATE TABLE `pravice` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_uporabnika` INT NOT NULL,
    `pravica` ENUM('dodajanje_nalog', 'ocenjevanje', 'oddajanje_nalog') NOT NULL,
    FOREIGN KEY (`id_uporabnika`) REFERENCES `uporabniki`(`id`)
);

-- Dodajanje privzetih pravic za učitelje in učence
INSERT INTO `pravice` (`id_uporabnika`, `pravica`) 
SELECT `id`, 'dodajanje_nalog' FROM `uporabniki` WHERE `vloga` = 'ucitelj';
INSERT INTO `pravice` (`id_uporabnika`, `pravica`) 
SELECT `id`, 'ocenjevanje' FROM `uporabniki` WHERE `vloga` = 'ucitelj';
INSERT INTO `pravice` (`id_uporabnika`, `pravica`) 
SELECT `id`, 'oddajanje_nalog' FROM `uporabniki` WHERE `vloga` = 'ucenec';