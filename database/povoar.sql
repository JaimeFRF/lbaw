--- CART

INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES;
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 
INSERT INTO cart DEFAULT VALUES; 

--- LOCATION

insert into location (id, address, city, country, postal_code) values (1, '9 Sauthoff Circle', 'Goya', 'Argentina', '3450');
insert into location (id, address, city, country, postal_code) values (2, '38217 Hagan Place', 'At Tibnī', 'Syria', '4490');
insert into location (id, address, city, country, postal_code) values (3, '75191 Texas Place', 'Qutun', 'China', '4490');
insert into location (id, address, city, country, postal_code) values (4, '76593 Mockingbird Way', 'Huaylillas', 'Peru', '4490');
insert into location (id, address, city, country, postal_code) values (5, '2 Springview Center', 'Boden', 'Sweden', '961 86');
insert into location (id, address, city, country, postal_code) values (6, '30 Steensland Center', 'Ḑawrān ad Daydah', 'Yemen', '4490');
insert into location (id, address, city, country, postal_code) values (7, '1 Russell Avenue', 'Đắk Glei', 'Vietnam', '4490');
insert into location (id, address, city, country, postal_code) values (8, '2 Dixon Parkway', 'Budapest', 'Hungary', '1147');
insert into location (id, address, city, country, postal_code) values (9, '7540 Lake View Street', 'Aigínio', 'Greece', '4490');
insert into location (id, address, city, country, postal_code) values (10, '33 Mayer Avenue', 'Nagua', 'Dominican Republic', '10118');
insert into location (id, address, city, country, postal_code) values (11, '9887 Lawn Center', 'Verkhnyachka', 'Ukraine', '4490');
insert into location (id, address, city, country, postal_code) values (12, '19358 Portage Pass', 'Doña Remedios Trinidad', 'Philippines', '3009');
insert into location (id, address, city, country, postal_code) values (13, '30257 Nancy Terrace', 'Šentvid pri Stični', 'Slovenia', '1296');
insert into location (id, address, city, country, postal_code) values (14, '0 Graceland Point', 'Lipsko', 'Poland', '27-300');
insert into location (id, address, city, country, postal_code) values (15, '05918 Cardinal Terrace', 'Sājir', 'Saudi Arabia', '4490');


--- ITEM

INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (1, 'Retro Graphic TShirt', 29.99, 25, 'White', '90s', 'Cotton', 'White TShirt with retro graphic design.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (2, 'Vintage Denim Jacket', 79.99, 10, 'Blue', '80s', 'Denim', 'A stylish vintage denim jacket.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (3, 'Classic Flannel Shirt', 45.00, 15, 'Red', '70s', 'Cotton', 'Red flannel shirt with classic look.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (4, 'Vintage High-waist Jeans', 65.00, 20, 'Blue', '80s', 'Denim', 'High-waisted jeans with a vintage style.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (5, 'Retro Sneakers', 50.00, 40, 'Multi', '90s', 'Canvas', 'Colorful sneakers with a retro look.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (6, 'Vintage leather Jacket', 109.99, 0, 'White', '70s', 'Denim', 'A stylish leather denim jacket.');

--- USER

insert into "user" (id, username, email, password, phone, id_cart) values (1, 'johndoe', 'johndoe@example.com', '1234567890', '938203081', 1);
insert into "user" (id, username, email, password, phone, id_cart) values (2, 'bjamieson1', 'sbraxton1@example.com', 'kD7!qF?n&K', '932798895', 2);
insert into "user" (id, username, email, password, phone, id_cart) values (3, 'kkennelly2', 'ddallywater2@example.com', 'aV8(dRf$kP', '939401278', 3);
insert into "user" (id, username, email, password, phone, id_cart) values (4, 'tpechell3', 'ffooter3@example.com', 'zI1>5#6a6,k', '938762590', 4);
insert into "user" (id, username, email, password, phone, id_cart) values (5, 'acastree4', 'jreford4@example.com', 'sO7~eEoK=`W<', '937716046', 5);
insert into "user" (id, username, email, password, phone, id_cart) values (6, 'smahedy5', 'pboschmann5@example.com', 'fR4&!%#vXkvP', '937796246', 6);
insert into "user" (id, username, email, password, phone, id_cart) values (7, 'mmcfater6', 'lghelerdini6@example.com', 'cH7#uiRmS`h`', '930855105', 7);
insert into "user" (id, username, email, password, phone, id_cart) values (8, 'kestable7', 'bswann7@example.com', 'qU1=9mSxgWt+', '935748655', 8);
insert into "user" (id, username, email, password, phone, id_cart) values (9, 'msommerled8', 'emothersdale8@example.com', 'fJ1`KU<1&$R', '937270532', 9);
insert into "user" (id, username, email, password, phone, id_cart) values (10, 'amarjoribanks9', 'dmantripp9@example.com', 'bP4.=9)pH\p`', '932783259', 10);
insert into "user" (id, username, email, password, phone, id_cart) values (11, 'nskilletta', 'kbeckleya@example.com', 'fP7%9BczXBDQ', '933756062', 11);
insert into "user" (id, username, email, password, phone, id_cart) values (12, 'gdeignanb', 'mkaszperb@example.com', 'gA3|)?lF#eJ', '939431839', 12);
insert into "user" (id, username, email, password, phone, id_cart) values (13, 'ndurdlec', 'mbenzac@example.com', 'mK9*kVj#4$I<', '932374374', 13);
insert into "user" (id, username, email, password, phone, id_cart) values (14, 'dwhitcombd', 'emadged@example.com', 'gA8\)aOC&h4K', '937788943', 14);
insert into "user" (id, username, email, password, phone, id_cart) values (15, 'evongrollmanne', 'lmccarrolle@example.com', 'aR4}r&=5P`0F', '938541696', 15);
insert into "user" (id, username, email, password, phone, id_cart) values (16, 'pirwinf', 'gkestonf@example.com', 'uU8<G2LXy)R?', '933213027', 16);
insert into "user" (id, username, email, password, phone, id_cart) values (17, 'bliffeyg', 'ldrennang@example.com', 'uN9&S%ccnfmk', '933378542', 17);
insert into "user" (id, username, email, password, phone, id_cart) values (18, 'freichelth', 'bpochonh@example.com', 'wM8=%||FA%QF', '939829485', 18);
insert into "user" (id, username, email, password, phone, id_cart) values (19, 'ahedgesi', 'jantonuttii@example.com', 'gK3=wACQr5T7', '936239761', 19);
insert into "user" (id, username, email, password, phone, id_cart) values (20, 'ftrailj', 'cperchj@example.com', 'wM4|L+.1.''Ki', '933875393', 20);


--- ADMIN

insert into admin (id, username, email, password, phone) values (21, 'tripleh', 'tripleh@example.com', '1234', '102-381-0489');
insert into admin (id, username, email, password, phone) values (22, 'rkillcross1', 'aairy1@hc360.com', 'zC9$ft53j=&', '438-250-2550');
insert into admin (id, username, email, password, phone) values (23, 'dvaughanhughes2', 'amillthorpe2@ed.gov', 'bQ4$$}Z,PFl{o', '214-326-3416');
insert into admin (id, username, email, password, phone) values (24, 'amatterface3', 'ndanneil3@hud.gov', 'cW3?)hMX6Gzbs', '700-964-4874');
insert into admin (id, username, email, password, phone) values (25, 'pthomasen4', 'gslym4@imdb.com', 'cM2}p)NgRpu6by', '700-772-7895');

--- WISHLIST

INSERT INTO wishlist (id_user,id_item) VALUES (1,1);
INSERT INTO wishlist (id_user,id_item) VALUES (1,6);
INSERT INTO wishlist (id_user,id_item) VALUES (2,2);
INSERT INTO wishlist (id_user,id_item) VALUES (3,3);
INSERT INTO wishlist (id_user,id_item) VALUES (4,4);
INSERT INTO wishlist (id_user,id_item) VALUES (5,5);

--- IMAGE

INSERT INTO image (id_item, filepath) VALUES (1, 'images/retro_graphic_tshirt_1.png');
INSERT INTO image (id_item, filepath) VALUES (1, 'images/retro_graphic_tshirt_2.png');

INSERT INTO image (id_item, filepath) VALUES (2, 'images/vintage_denim_jacket_1.png');
INSERT INTO image (id_item, filepath) VALUES (2, 'images/vintage_denim_jacket_2.png');

INSERT INTO image (id_item, filepath) VALUES (3, 'images/classic_flannel_shirt_1.png');
INSERT INTO image (id_item, filepath) VALUES (3, 'images/classic_flannel_shirt_2.png');

INSERT INTO image (id_item, filepath) VALUES (4, 'images/vintage_highwaist_jeans_1.png');
INSERT INTO image (id_item, filepath) VALUES (4, 'images/vintage_highwaist_jeans_2.png');

INSERT INTO image (id_item, filepath) VALUES (5, 'images/retro_sneakers_1.png');
INSERT INTO image (id_item, filepath) VALUES (5, 'images/retro_sneakers_2.png');

INSERT INTO image (id_user, filepath) VALUES (1, 'images/profile_user_1.png');
INSERT INTO image (id_user, filepath) VALUES (2, 'images/profile_user_2.png');
INSERT INTO image (id_user, filepath) VALUES (3, 'images/profile_user_3.png');
INSERT INTO image (id_user, filepath) VALUES (4, 'images/profile_user_4.png');
INSERT INTO image (id_user, filepath) VALUES (5, 'images/profile_user_5.png');

--- SHIRT

INSERT INTO shirt (id_item, shirt_type, size) VALUES (3, 'Regular', 'M');

--- TSHIRT

INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (1, 'Regular', 'L');

--- JACKET

INSERT INTO jacket (id_item, jacket_type, size) VALUES (2, 'Bomber', 'S');

--- JEANS

INSERT INTO jeans (id_item, waist_size, inseam_size, rise_size) VALUES (4, 32, 30, 10);

--- SNEAKER

INSERT INTO sneaker (id_item, shoe_size) VALUES (5, 38);

--- CART_ITEM

INSERT INTO cart_item (id_cart, id_item) VALUES (1, 1);
INSERT INTO cart_item (id_cart, id_item) VALUES (1, 2);
INSERT INTO cart_item (id_cart, id_item) VALUES (2, 3);
INSERT INTO cart_item (id_cart, id_item) VALUES (3, 1);
INSERT INTO cart_item (id_cart, id_item) VALUES (4, 5);
INSERT INTO cart_item (id_cart, id_item) VALUES (5, 4);
INSERT INTO cart_item (id_cart, id_item) VALUES (6, 3);
INSERT INTO cart_item (id_cart, id_item) VALUES (7, 2);
INSERT INTO cart_item (id_cart, id_item) VALUES (8, 1);
INSERT INTO cart_item (id_cart, id_item) VALUES (9, 4);
INSERT INTO cart_item (id_cart, id_item) VALUES (10, 5);

--- REVIEW

INSERT INTO review (id,description,rating,id_user,id_item) values (1,'This is a masterpiece',5,1,1);
INSERT INTO review (id,description,rating,id_user,id_item) values (2,'i do not like this',1,2,1);
INSERT INTO review (id,description,rating,id_user,id_item) values (3,'great product, dont like the color tho',4,3,2);
INSERT INTO review (id,description,rating,id_user,id_item) values (4,'my name is jeff',5,1,5);
INSERT INTO review (id,description,rating,id_user,id_item) values (5,'wow.',5,4,3);
INSERT INTO review (id,description,rating,id_user,id_item) values (6,'This is a masterpiece!!',5,1,1);

--- PURCHASE

INSERT INTO purchase (price, purchase_date, delivery_date, purchase_status, payment_method, id_user, id_location, id_cart)
VALUES ( 109.98, '2023-10-10', '2023-10-15', 'Processing', 'Transfer', 1, 1, 1);
INSERT INTO purchase (price, purchase_date, delivery_date, purchase_status, payment_method, id_user, id_location, id_cart)
VALUES (45.00 , '2023-10-08', '2023-10-20', 'Processing', 'Paypal', 2,2, 2);


/* testing triggers */
UPDATE item SET stock = 1 WHERE id = 6;
UPDATE item SET price = 99.99 WHERE id = 6;
UPDATE purchase SET purchase_status = 'Packed' WHERE id = 1;
UPDATE purchase SET purchase_status = 'Delivered' WHERE id = 1;
UPDATE purchase SET purchase_status = 'Packed' WHERE id = 2;
UPDATE purchase SET purchase_status = 'Sent' WHERE id = 2;