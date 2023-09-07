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

INSERT INTO building (id, street_id, price, name, unit_rent, street_rent, single_house_rent, double_house_rent, triple_house_rent, quadruple_house_rent, hotel_rent, mortgage, position)
VALUES (1,1,60,'Building 1',2,4,10,30,90,160,250,30,1), -- Brown 1
       (2,1,60,'Building 2',4,8,20,60,180,320,450,30,3), -- Brown 2
       (3,2,100,'Building 3',6,12,30,90,270,400,550,50,6), -- Light Blue 1
       (4,2,100,'Building 4',6,12,30,90,270,400,550,50,8), -- Light Blue 2
       (5,2,120,'Building 5',8,16,40,100,300,450,600,60,9), -- Light Blue 3
       (6,3,140,'Building 6',10,20,50,150,450,625,750,70,11), -- Pink 1
       (7,3,140,'Building 7',10,20,50,150,450,625,750,70,13), -- Pink 2
       (8,3,160,'Building 8',12,24,60,180,500,700,900,80,14), -- Pink 3
       (9,4,180,'Building 9',14,28,70,200,550,700,900,90,16), -- Orange 1
       (10,4,180,'Building 10',14,28,70,200,550,700,900,90,18), -- Orange 2
       (11,4,200,'Building 11',16,32,80,220,600,800,1000,100,19), -- Orange 3
       (12,5,220,'Building 12',18,36,90,250,700,875,1050,110,21), -- Red 1
       (13,5,220,'Building 13',18,36,90,250,700,875,1050,110,23), -- Red 2
       (14,5,240,'Building 14',20,40,100,300,750,925,1100,120,24), -- Red 3
       (15,6,260,'Building 15',22,44,110,330,800,975,1150,130,26), -- Yellow 1
       (16,6,260,'Building 16',22,44,110,330,800,975,1150,130,27), -- Yellow 2
       (17,6,280,'Building 16',24,48,120,360,850,1025,1200,140,29), -- Yellow 3
       (18,7,300,'Building 17',26,52,130,390,900,1100,1275,150,31), -- Green 1
       (19,7,300,'Building 18',26,52,130,390,900,1100,1275,150,32), -- Green 2
       (20,7,320,'Building 19',28,56,150,450,1000,1200,1400,160,34), -- Green 3
       (21,8,350,'Building 20',35,70,175,500,1100,1300,1500,175,37), -- Blue 1
       (22,8,400,'Building 21',50,100,200,600,1400,1700,2000,200,39); -- Blue 2

INSERT INTO action_field (id, name, function, position)
VALUES (1,'Start','start',0),
       (2,'Community Chest','communityChest',2),
       (3,'Income Tax','incomeTax',4),
       (4,'Chance','chance',7),
       (5,'Jail','jail',10),
       (6,'Community Chest','communityChest',17),
       (7,'Free Parking','freeParking',20),
       (8,'Chance','chance',22),
       (9,'Go To Jail','goToJail',30),
       (10,'Community Chest','communityChest',33),
       (11,'Chance','chance',36),
       (12,'Luxury Tax','luxuryTax',38);

--temporarily
INSERT INTO action_field (id, name, function, position)
VALUES (13,'Railroad 1','railroad',5),
       (14,'Railroad 2','railroad',15),
       (15,'Railroad 3','railroad',25),
       (16,'Railroad 4','railroad',35),
       (17,'Utility 1','utility',12),
       (18,'Utility 2','utility',28);
