UPDATE game SET current_turn_player_id = NULL WHERE id > 0;

DELETE FROM game_building WHERE id > 0;
DELETE FROM game_action_field WHERE id > 0;
DELETE FROM game_card WHERE id > 0;
DELETE FROM player WHERE id > 0;
DELETE FROM game WHERE id > 0;
DELETE FROM card WHERE id > 0;
DELETE FROM building WHERE id > 0;
DELETE FROM action_field WHERE id > 0;
DELETE FROM street WHERE id > 0;

INSERT INTO street (id, color, house_cost)
VALUES (1,'#5e4228',50), -- Brown
       (2,'#79e4fc',50), -- Light Blue
       (3,'#8f2581',100), -- Pink
       (4,'#e3a514',100), -- Orange
       (5,'#e32514',150), -- Red
       (6,'#f7ef05',150), -- Yellow
       (7,'#3e852a',200), -- Green
       (8,'#2c2a85',200); -- Blue

INSERT INTO building (id, street_id, price, name, unit_rent, street_rent, single_house_rent, double_house_rent, triple_house_rent, quadruple_house_rent, hotel_rent, mortgage, position, img)
VALUES (1,1,60,'Parkanlage Martinsberggut',2,4,10,30,90,160,250,30,1, 'parkanlage_martinsberggut.webp'), -- Brown 1
       (2,1,60,'Kappiinseli',4,8,20,60,180,320,450,30,3, 'kappiinseli.webp'), -- Brown 2
       (3,2,100,'Kurpark',6,12,30,90,270,400,550,50,6, 'kurpark.webp'), -- Light Blue 1
       (4,2,100,'Theaterplatz',6,12,30,90,270,400,550,50,8, 'theaterplatz.webp'), -- Light Blue 2
       (5,2,120,'Bahnhofstrasse',8,16,40,100,300,450,600,60,9, 'bahnhofstrasse.jpg'), -- Light Blue 3
       (6,3,140,'Stockmattstrasse',10,20,50,150,450,625,750,70,11, 'stockmattstrasse.jpg'), -- Pink 1
       (7,3,140,'Park Museum Langmatt',10,20,50,150,450,625,750,70,13, 'park_museum_langmatt.jpg'), -- Pink 2
       (8,3,160,'Spielplatz Kehl Baden',12,24,60,180,500,700,900,80,14, 'spielplatz_kehl_baden.webp'), -- Pink 3
       (9,4,180,'Mellingerstrasse',14,28,70,200,550,700,900,90,16, 'mellingerstrasse.webp'), -- Orange 1
       (10,4,180,'Römerstrasse',14,28,70,200,550,700,900,90,18, 'römerstrasse.jpg'), -- Orange 2
       (11,4,200,'Kanalstrasse',16,32,80,220,600,800,1000,100,19, 'kanalstrasse.jpg'), -- Orange 3
       (12,5,220,'Bahnhofsplatz Baden',18,36,90,250,700,875,1050,110,21,'bahnhofsplatz_baden.jpg'), -- Red 1
       (13,5,220,'Trafoplatz',18,36,90,250,700,875,1050,110,23, 'trafoplatz.jpg'), -- Red 2
       (14,5,240,'Kirchplatz',20,40,100,300,750,925,1100,120,24, 'kirchplatz.jpg'), -- Red 3
       (15,6,260,'Limmat Promenade',22,44,110,330,800,975,1150,130,26, 'limmatpromenade.webp'), -- Yellow 1
       (16,6,260,'Bruggerstrasse',22,44,110,330,800,975,1150,130,27, 'bruggerstrasse.jpg'), -- Yellow 2
       (17,6,280,'Haselstrasse',24,48,120,360,850,1025,1200,140,29, 'haselstrasse.webp'), -- Yellow 3
       (18,7,300,'Spielplatz Baldegg Baden',26,52,130,390,900,1100,1275,150,31, 'spielplatz_baldegg_baden.jpg'), -- Green 1
       (19,7,300,'Müsernstrasse',26,52,130,390,900,1100,1275,150,32, 'müsernstrasse.png'), -- Green 2
       (20,7,320,'Parkstrasse',28,56,150,450,1000,1200,1400,160,34,'parkstrasse.png'), -- Green 3
       (21,8,350,'Mättelipark',35,70,175,500,1100,1300,1500,175,37, 'mättelipark.webp'), -- Blue 1
       (22,8,400,'Park Villa Boveri',50,100,200,600,1400,1700,2000,200,39, 'park_villa_boveri.jpg'); -- Blue 2

INSERT INTO action_field (id, name, function, position)
VALUES (1,'Los','start',0),
       (2,'Gemeinschaftsfeld','communityChest',2),
       (3,'Einkommens Steuer','incomeTax',4),
       (4,'Ereignisfeld','chance',7),
       (5,'Gefängnis','jail',10),
       (6,'Gemeinschaftsfeld','communityChest',17),
       (7,'Frei Parken','freeParking',20),
       (8,'Ereignisfeld','chance',22),
       (9,'Geh ins Gefängnis','goToJail',30),
       (10,'Gemeinschaftsfeld','communityChest',33),
       (11,'Ereignisfeld','chance',36),
       (12,'Zusatz Steuer','luxuryTax',38);

--temporarily
INSERT INTO action_field (id, name, function, position, mortgage, img)
VALUES (13,'Bahnhof Baden','railroad',5, 100, 'bahnhof_baden.jpg'),
       (14,'Postautostation','railroad',15, 100, 'postautostation.png'),
       (15,'Lindenplatz','railroad',25, 100, 'lindenplatz.jpg'),
       (16,'Ziegelhau','railroad',35, 100, 'ziegelhau.webp'),
       (17,'Kraftwerk Kappelerhof','utility',12, 75, 'kraftwerk_kappelerhof.webp'),
       (18,'Power Tower','utility',28, 75, 'power_tower.webp');

-- Chance Cards
INSERT INTO card (id, text, type, function, amount_per_game)
VALUES (1, 'Die Bank zahlt dir eine divide von 50$', 'chance', 'add.100', 1),
       (2, 'Polizeibusse für zu schnelles fahren 15$', 'chance', 'subtract.100', 1),
       (3, 'Rücke bis zu «Trafoplatz» vor.', 'chance', 'moveTo.23', 1),
       (4, 'Rücke bis zum nächsten Bahn-Feld vor.', 'chance', 'moveToNextRailroad', 1),
       (5, 'Strassenreparaturen sind fällig. Zahle für deine Häuser und Hotels. 25$ pro Haus und 100$ pro Hotel.', 'chance', 'repair', 1),
       (6, 'Du kommst ohne Busse aus dem Gefängnis frei.', 'chance', 'freeFromJail', 1),
       (7, 'Zahle dein Schuldgeld 100$', 'chance', 'subtract.100', 1),
       (8, 'Rücke bis nach «Park Museum Langmatt» vor.', 'chance', 'moveTo.13', 1),
       (9, 'Lasse alle deine Häuser renovieren. Zahle für deine Häuser und Hotels. 25$ pro Haus und 100$ pro Hotel.', 'chance', 'repair', 1),
       (10, 'Rücke bis nach «Mättelipark» vor.', 'chance', 'moveTo.36', 1),
       (11, 'Rücke auf «Start» vor.', 'chance', 'moveTo.0', 1),
       (12, 'Gehe auf direktem Weg ins Gefängnis.', 'chance', 'moveTo.10', 1),
       (13, 'Du machst einen Ausflug auf das Feld «Bahnhof Baden»', 'chance', 'moveTo.5', 1),
       (14, 'Du Gewinnst im Lotto 100$', 'chance', 'add.100', 1),
       (15, 'Du bist Direktor geworden und zahlst deshalb jedem Spieler 100$', 'chance', 'payAll.100', 1);

-- Community Chest Cards
INSERT INTO card (id, text, type, function, amount_per_game)
VALUES (16, 'Rückzahlung einer Anleihe: Die Bank zahlt dir 25$', 'communityChest', 'add.25', 1),
       (17, 'Die Jahresrente wird fällig. Du erhältst 100$', 'communityChest', 'add.100', 1),
       (18, 'Zahle deine BBB-Busse von 50$', 'communityChest', 'subtract.50', 1),
       (19, 'Gehe zurück nach «Parkanlage Martinsberggut».', 'communityChest', 'moveTo.1', 1),
       (20, 'Du erhältst aus Lagerverkäufen 100$', 'communityChest', 'add.100', 1),
       (21, 'Du machst eine Erbschaft und erhältst 200$', 'communityChest', 'add.200', 1),
       (22, 'Du erhältst auf Vorzugs-Aktien 7% Divide: 50$', 'communityChest', 'add.50', 1),
       (23, 'Du kommst ohne Busse aus dem Gefängnis frei.', 'communityChest', 'freeFromJail', 1),
       (24, 'Du bekommst von der Bank 200$', 'communityChest', 'add.200', 1),
       (25, 'Du hast Geburtstag und erhältst von jedem Spieler 10$', 'communityChest', 'collect.10', 1),
       (26, 'Einkommenssteuer-Rückzahlung. Du erhältst 20$', 'communityChest', 'add.20', 1),
       (27, 'Zahle dem Spital 100$', 'communityChest', 'subtract.100', 1),
       (28, 'Du machst eine Erfindung und erhältst dafür 50$', 'communityChest', 'add.50', 1),
       (29, 'Rücke auf Start vor.', 'communityChest', 'moveTo.0', 1),
       (30, 'Gehe auf direktem Weg ins Gefängnis.', 'communityChest', 'moveTo.10', 1),
       (31, 'Bank-Irrtum zu deinen Gunsten. Du erhältst 200$', 'communityChest', 'add.200', 1);
