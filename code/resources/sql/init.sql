DROP TABLE IF EXISTS wishlist CASCADE;
DROP TABLE IF EXISTS cart_item CASCADE;
DROP TABLE IF EXISTS review CASCADE;
DROP TABLE IF EXISTS notification CASCADE;
DROP TABLE IF EXISTS image CASCADE;
DROP TABLE IF EXISTS purchase CASCADE;
DROP TABLE IF EXISTS location CASCADE;
DROP TABLE IF EXISTS wishlist CASCADE;
DROP TABLE IF EXISTS shirt CASCADE;
DROP TABLE IF EXISTS tshirt CASCADE;
DROP TABLE IF EXISTS jacket CASCADE;
DROP TABLE IF EXISTS sneaker CASCADE;
DROP TABLE IF EXISTS jeans CASCADE;
DROP TABLE IF EXISTS cart CASCADE;
DROP TABLE IF EXISTS item CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS user CASCADE;

-- drop triggers

-- drop functions 

-- drop indexes

-- drop types

DROP TYPE IF EXISTS ShirtType;
DROP TYPE IF EXISTS TshirtType;
DROP TYPE IF EXISTS JacketType;
DROP TYPE IF EXISTS PaymentMethod;
DROP TYPE IF EXISTS PurchaseStatus;
DROP TYPE IF EXISTS NotificationType;

----------- types



CREATE TYPE ShirtType as ENUM('Collarless', 'Regular', 'Short sleeve');

CREATE TYPE TshirtType as ENUM ('Regular', 'Long sleeve', 'Football');

CREATE TYPE JacketType as ENUM ('Regular', 'Baseball', 'Bomber');

CREATE TYPE PaymentMethod as ENUM ('Transfer', 'Paypal');

CREATE TYPE PurchaseStatus as ENUM ('Processing', 'Packed', 'Sent', 'Delivered');

CREATE TYPE NotificationType as ENUM ('Sale', 'PurchaseStatusChange','RESTOCK','ORDER_UPDATE')


------------ tables


CREATE TABLE user(
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT username_uk UNIQUE,
    email TEXT NOT NULL CONSTRAINT user_email_uk UNIQUE,
    password TEXT NOT NULL CONSTRAINT password_length CHECK (length(password) >= 10),
    phone VARCHAR(20), 
    is_banned boolean NOT NULL DEFAULT FALSE,
    remember_token TEXT DF NULL,
    id_cart INTEGER NOT NULL REFERENCES Cart(id),
);

CREATE TABLE admin(
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    phone VARCHAR(20),
);

CREATE TABLE cart(
    id SERIAL PRIMARY KEY
);

CREATE TABLE item(
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT price_positive CHECK (price > 0.0),
    rating FLOAT NOT NULL DEFAULT 0.0 CONSTRAINT rating_positive CHECK (rating >= 0.0 AND rating <= 5.0),
    stock INTEGER NOT NULL CONSTRAINT stock_positive CHECK (stock >= 0),
    color TEXT NOT NULL,
    era TEXT,
    fabric TEXT,
    description TEXT
);

CREATE TABLE cart_item(
    id_cart INTEGER NOT NULL REFERENCES Cart(id),
    id_item INTEGER NOT NULL REFERENCES Item(id),
    PRIMARY KEY(id_cart, id_item)
);

CREATE TABLE wishlist(
    id_user INTEGER NOT NULL REFERENCES user(id) ON DELETE CASCADE,
    id_item INTEGER NOT NULL REFERENCES item(id),
    PRIMARY KEY(id_user, id_item)
);

CREATE TABLE location(
    id SERIAL PRIMARY KEY,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    country TEXT NOT NULL,
    postal_code TEXT NOT NULL,
    description TEXT
);

CREATE TABLE purchase(
    id SERIAL PRIMARY KEY,
    price FLOAT NOT NULL CONSTRAINT price_positive CHECK (price > 0.0),
    purchase_date DATE NOT NULL,
    delivery_date DATE NOT NULL CONSTRAINT delivery_date_check CHECK (delivery_date >= purchase_date),
    purchase_status PurchaseStatus NOT NULL,
    payment_method PaymentMethod NOT NULL,
    id_user INTEGER NOT NULL REFERENCES user(id) ON DELETE SET NULL,
    id_location INTEGER NOT NULL REFERENCES location(id)
);

CREATE TABLE review(
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL CONSTRAINT description_length CHECK (length(description) <= 200),
    rating FLOAT NOT NULL CONSTRAINT rating_positive CHECK (rating >= 0.0 AND rating <= 5.0),
    up_votes INTEGER DEFAULT 0,
    down_votes INTEGER DEFAULT 0,
    id_user INTEGER REFERENCES user(id) ON DELETE SET NULL,
    id_item INTEGER NOT NULL REFERENCES item(id)
);

CREATE TABLE notification(
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL,
    date TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL, 
    notification_type NotificationType NOT NULL,
    id_user INTEGER NOT NULL REFERENCES user(id) ON DELETE SET NULL,
    id_item INTEGER NOT NULL REFERENCES item(id) ON DELETE SET NULL,
    id_purchase INTEGER NOT NULL REFERENCES purchase(id) ON DELETE SET NULL
);

CREATE TABLE image(
    id serial PRIMARY KEY,
    id_item INTEGER REFERENCES item(id) ON DELETE CASCADE,
    id_user INTEGER REFERENCES user(id) ON DELETE CASCADE,
    filepath TEXT
);

CREATE TABLE shirt(
    id_item INTEGER PRIMARY KEY REFERENCES item(id) ON DELETE CASCADE,
    shirt_type ShirtType NOT NULL,
    size TEXT NOT NULL
);

CREATE TABLE tshirt(
    id_item INTEGER PRIMARY KEY REFERENCES item(id) ON DELETE CASCADE,
    tshirt_type TshirtType NOT NULL,
    size TEXT NOT NULL
);

CREATE TABLE jacket(
    id_item INTEGER PRIMARY KEY REFERENCES item(id) ON DELETE CASCADE,
    jacket_type JacketType NOT NULL,
    size TEXT NOT NULL
);

CREATE TABLE sneaker(
    id_item INTEGER PRIMARY KEY REFERENCES item(id) ON DELETE CASCADE,
    shoe_size INTEGER NOT NULL CONSTRAINT shoe_size_check CHECK (shoe_size >= 0)
);

CREATE TABLE jeans(
    id_item INTEGER PRIMARY KEY REFERENCES item(id) ON DELETE CASCADE,
    waist_size INTEGER NOT NULL CONSTRAINT waist_size_check CHECK (waist_size > 0),
    inseam_size INTEGER NOT NULL CONSTRAINT inseam_size_check CHECK (inseam_size > 0),
    rise_size INTEGER NOT NULL CONSTRAINT rise_size_check CHECK (rise_size > 0)
);

--- CART

INSERT INTO cart (id) VALUES (1);
INSERT INTO cart (id) VALUES (2);
INSERT INTO cart (id) VALUES (3);
INSERT INTO cart (id) VALUES (4);
INSERT INTO cart (id) VALUES (5);
INSERT INTO cart (id) VALUES (6);
INSERT INTO cart (id) VALUES (7);
INSERT INTO cart (id) VALUES (8);
INSERT INTO cart (id) VALUES (9);
INSERT INTO cart (id) VALUES (10);
INSERT INTO cart (id) VALUES (11);
INSERT INTO cart (id) VALUES (12);
INSERT INTO cart (id) VALUES (13);
INSERT INTO cart (id) VALUES (14);
INSERT INTO cart (id) VALUES (15);
INSERT INTO cart (id) VALUES (16);
INSERT INTO cart (id) VALUES (17);
INSERT INTO cart (id) VALUES (18);
INSERT INTO cart (id) VALUES (19);
INSERT INTO cart (id) VALUES (20);

--- WISHLIST

INSERT INTO wishlist (id) VALUES (1);
INSERT INTO wishlist (id) VALUES (2);
INSERT INTO wishlist (id) VALUES (3);
INSERT INTO wishlist (id) VALUES (4);
INSERT INTO wishlist (id) VALUES (5);
INSERT INTO wishlist (id) VALUES (6);
INSERT INTO wishlist (id) VALUES (7);
INSERT INTO wishlist (id) VALUES (8);
INSERT INTO wishlist (id) VALUES (9);
INSERT INTO wishlist (id) VALUES (10);
INSERT INTO wishlist (id) VALUES (11);
INSERT INTO wishlist (id) VALUES (12);
INSERT INTO wishlist (id) VALUES (13);
INSERT INTO wishlist (id) VALUES (14);
INSERT INTO wishlist (id) VALUES (15);
INSERT INTO wishlist (id) VALUES (16);
INSERT INTO wishlist (id) VALUES (17);
INSERT INTO wishlist (id) VALUES (18);
INSERT INTO wishlist (id) VALUES (19);
INSERT INTO wishlist (id) VALUES (20);

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
/* INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (6, 'Vintage Rock Band TShirt', 35.00, 30, 'Black', '80s', 'Cotton', 'Black TShirt with vintage rock band print.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (7, '70s Denim Jacket', 95.00, 5, 'Blue', '70s', 'Denim', 'Blue denim jacket with 70s styling.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (8, 'Retro Striped Shirt', 40.00, 25, 'Green', '80s', 'Cotton', 'Green striped shirt with a retro feel.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (9, 'Classic Blue Jeans', 60.00, 20, 'Blue', '90s', 'Denim', 'Classic blue jeans with a relaxed fit.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (10, 'Vintage Leather Sneakers', 80.00, 15, 'White', '70s', 'Leather', 'White leather sneakers with vintage design.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (11, 'Vintage Baseball TShirt', 30.00, 35, 'White', '90s', 'Cotton', 'White baseball TShirt with vintage logo.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (12, '80s Style Denim Jacket', 89.99, 8, 'Blue', '80s', 'Denim', 'Denim jacket with 80s style accents.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (13, 'Retro Western Shirt', 55.00, 18, 'Red', '70s', 'Cotton', 'Red western shirt with retro detailing.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (14, 'Vintage Skinny Jeans', 70.00, 12, 'Black', '80s', 'Denim', 'Black skinny jeans with a vintage cut.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (15, 'Classic Canvas Sneakers', 65.00, 30, 'Black', '90s', 'Canvas', 'Black classic canvas sneakers.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (16, 'Vintage Band TShirt', 35.00, 28, 'Grey', '70s', 'Cotton', 'Grey TShirt with vintage band graphic.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (17, 'Retro Leather Jacket', 120.00, 6, 'Black', '80s', 'Leather', 'Black leather jacket with retro styling.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (18, 'Classic Plaid Shirt', 50.00, 22, 'Blue', '90s', 'Cotton', 'Blue plaid shirt with classic fit.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (19, 'Vintage Straight-Leg Jeans', 75.00, 15, 'Blue', '70s', 'Denim', 'Straight-leg jeans with a vintage feel.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (20, 'Retro High-Top Sneakers', 85.00, 10, 'Red', '80s', 'Canvas', 'Red high-top sneakers with retro flair.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (21, 'Vintage Logo TShirt', 30.00, 40, 'Blue', '90s', 'Cotton', 'Blue TShirt with vintage brand logo.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (22, '70s Corduroy Jacket', 110.00, 4, 'Brown', '70s', 'Corduroy', 'Brown corduroy jacket from the 70s.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (23, 'Retro Short Sleeve Shirt', 45.00, 20, 'Yellow', '80s', 'Cotton', 'Yellow short sleeve shirt with retro print.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (24, 'Vintage Bootcut Jeans', 68.00, 13, 'Blue', '70s', 'Denim', 'Blue bootcut jeans with vintage styling.');
INSERT INTO item (id, name, price, stock, color, era, fabric, description) VALUES (25, 'Classic Leather Sneakers', 90.00, 18, 'White', '90s', 'Leather', 'Classic white leather sneakers.');
 */

--- USER

insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (1, 'johndoe', 'johndoe@example.com', '1234', '908-203-0817', false, 'NatoquePenatibusEt.mp3', 1);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (2, 'bjamieson1', 'sbraxton1@epa.gov', 'kD7!qF?n&', '862-798-8952', false, 'Sit.xls', 2);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (3, 'kkennelly2', 'ddallywater2@umn.edu', 'aV8(dRf$', '859-401-2783', false, 'NislAenean.jpeg', 3);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (4, 'tpechell3', 'ffooter3@xrea.com', 'zI1>5#6a6,', '968-762-5907', false, 'ProinEuMi.avi', 4);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (5, 'acastree4', 'jreford4@boston.com', 'sO7~eEoK=`W<', '357-716-0462', false, 'EratVolutpat.txt', 5);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (6, 'smahedy5', 'pboschmann5@buzzfeed.com', 'fR4&!%#vXkvPh8=l', '847-796-2466', false, 'InPorttitorPede.xls', 6);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (7, 'mmcfater6', 'lghelerdini6@ft.com', 'cH7#uiRmS`h`', '710-855-1050', false, 'NuncNisl.mp3', 7);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (8, 'kestable7', 'bswann7@virginia.edu', 'qU1=9mSxgWt', '975-748-6554', false, 'MolestieHendreritAt.txt', 8);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (9, 'msommerled8', 'emothersdale8@vimeo.com', 'fJ1`KU<1&', '827-270-5321', false, 'AcNequeDuis.doc', 9);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (10, 'amarjoribanks9', 'dmantripp9@jugem.jp', 'bP4.=9)pH\p''', '452-783-2593', false, 'InBlandit.avi', 10);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (11, 'nskilletta', 'kbeckleya@sohu.com', 'fP7%9BczXBDQ?b', '383-756-0620', false, 'VestibulumAnteIpsum.txt', 11);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (12, 'gdeignanb', 'mkaszperb@purevolume.com', 'gA3|)?lF#e', '249-431-8395', false, 'PenatibusEtMagnis.jpeg', 12);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (13, 'ndurdlec', 'mbenzac@bluehost.com', 'mK9*kVj#4$I</i', '822-374-3745', false, 'MusEtiamVel.mp3', 13);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (14, 'dwhitcombd', 'emadged@java.com', 'gA8\)aOC&h4', '447-788-9439', false, 'BibendumFelisSed.tiff', 14);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (15, 'evongrollmanne', 'lmccarrolle@ihg.com', 'aR4}r&=5P`0FJ3', '338-541-6962', false, 'ViverraPede.avi', 15);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (16, 'pirwinf', 'gkestonf@mashable.com', 'uU8<G2LXy)R?@mf?', '793-213-0273', false, 'Nec.avi', 16);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (17, 'bliffeyg', 'ldrennang@timesonline.co.uk', 'uN9&S%ccnfmk/N', '123-378-5421', false, 'InFaucibus.xls', 17);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (18, 'freichelth', 'bpochonh@guardian.co.uk', 'wM8=%||FA%QF~@', '789-829-4854', false, 'Odio.doc', 18);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (19, 'ahedgesi', 'jantonuttii@hubpages.com', 'gK3=wACQ', '426-239-7613', false, 'PhasellusSit.xls', 19);
insert into user (id, username, email, password, phone, is_banned, profile_picture, id_cart) values (20, 'ftrailj', 'cperchj@washingtonpost.com', 'wM4|L+.1.''KiG', '523-875-3936', false, 'SapienCursus.mp3', 20);

--- ADMIN

insert into admin (id, username, email, password, phone) values (21, 'tripleh', 'tripleh@example.com', '1234', '102-381-0489');
insert into admin (id, username, email, password, phone) values (22, 'rkillcross1', 'aairy1@hc360.com', 'zC9$ft53j=&', '438-250-2550');
insert into admin (id, username, email, password, phone) values (23, 'dvaughanhughes2', 'amillthorpe2@ed.gov', 'bQ4$$}Z,PFl{o', '214-326-3416');
insert into admin (id, username, email, password, phone) values (24, 'amatterface3', 'ndanneil3@hud.gov', 'cW3?)hMX6Gzbs', '700-964-4874');
insert into admin (id, username, email, password, phone) values (25, 'pthomasen4', 'gslym4@imdb.com', 'cM2}p)NgRpu6by', '700-772-7895');


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

insert into review values (id,description,rating,id_user,id_item) values (1,'This is a masterpiece',5,1,1);
insert into review values (id,description,rating,id_user,id_item) values (2,"i don't like this",1,2,1);
insert into review values (id,description,rating,id_user,id_item) values (3,'great product, dont like the color tho',4,3,2);
insert into review values (id,description,rating,id_user,id_item) values (4,'my name is jeff',5,1,5);
insert into review values (id,description,rating,id_user,id_item) values (5,'wow.',5,4,3);
