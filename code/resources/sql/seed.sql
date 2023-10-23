drop schema if exists lbaw2366 cascade;
create schema lbaw2366;
SET search_path TO lbaw2366;

----------- types

CREATE TYPE ShirtType as ENUM('Collarless', 'Regular', 'Short sleeve');

CREATE TYPE TshirtType as ENUM ('Regular', 'Long sleeve', 'Football');

CREATE TYPE JacketType as ENUM ('Regular', 'Baseball', 'Bomber');

CREATE TYPE PaymentMethod as ENUM ('Transfer', 'Paypal');

CREATE TYPE PurchaseStatus as ENUM ('Processing', 'Packed', 'Sent', 'Delivered');

CREATE TYPE NotificationType as ENUM ('SALE', 'RESTOCK','ORDER_UPDATE');

------------ tables

CREATE TABLE item(
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    price FLOAT NOT NULL CONSTRAINT price_positive CHECK (price > 0.0),
    rating FLOAT NOT NULL DEFAULT 0.0 CONSTRAINT rating_positive CHECK (rating >= 0.0 AND rating <= 5.0),
    stock INTEGER NOT NULL CONSTRAINT stock_positive CHECK (stock >= 0),
    color TEXT NOT NULL,
    era TEXT,
    fabric TEXT,
    description TEXT,
    brand TEXT
);

CREATE TABLE cart(
    id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY
);

CREATE TABLE "user"(
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL CONSTRAINT username_uk UNIQUE,
    email TEXT NOT NULL CONSTRAINT user_email_uk UNIQUE,
    password TEXT NOT NULL CONSTRAINT password_length CHECK (length(password) >= 10),
    phone VARCHAR(20), 
    is_banned boolean NOT NULL DEFAULT FALSE,
    remember_token TEXT DEFAULT NULL,
    id_cart INTEGER NOT NULL REFERENCES cart(id)
);

CREATE TABLE admin(
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE cart_item(
    id_cart INTEGER NOT NULL REFERENCES Cart(id),
    id_item INTEGER NOT NULL REFERENCES Item(id),
    quantity INTEGER NOT NULL DEFAULT 1 CONSTRAINT quantity_positive CHECK (quantity > 0),
    PRIMARY KEY(id_cart, id_item)
);

CREATE TABLE wishlist(
    id_user INTEGER NOT NULL REFERENCES "user"(id) ON DELETE CASCADE,
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
    id_user INTEGER NOT NULL REFERENCES "user"(id) ON DELETE SET NULL,
    id_location INTEGER NOT NULL REFERENCES location(id),
    id_cart INTEGER NOT NULL REFERENCES cart(id)
);

CREATE TABLE review(
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL CONSTRAINT description_length CHECK (length(description) <= 200),
    rating FLOAT NOT NULL CONSTRAINT rating_positive CHECK (rating >= 0.0 AND rating <= 5.0),
    up_votes INTEGER DEFAULT 0,
    down_votes INTEGER DEFAULT 0,
    id_user INTEGER REFERENCES "user"(id) ON DELETE SET NULL,
    id_item INTEGER NOT NULL REFERENCES item(id)
);

CREATE TABLE notification(
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL,
    date TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL, 
    notification_type NotificationType NOT NULL,
    id_user INTEGER NOT NULL REFERENCES "user"(id) ON DELETE SET NULL,
    id_item INTEGER  REFERENCES item(id) ON DELETE SET NULL,
    id_purchase INTEGER REFERENCES purchase(id) ON DELETE SET NULL
);

CREATE TABLE image(
    id serial PRIMARY KEY,
    id_item INTEGER REFERENCES item(id) ON DELETE CASCADE,
    id_user INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
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

-----------------------------------------
-- INDEXES
-----------------------------------------

-- B-tree type functions
CREATE INDEX price_index ON item USING btree (price);

-- B-tree type function using clustering
CREATE INDEX review_item_id_index ON review (id_item);
CLUSTER review USING review_item_id_index;

--Hash type functions
CREATE INDEX item_brand_index ON item USING HASH (brand);

-----------------------------------------
-- FTS INDEX
-----------------------------------------

-- Add column to item to store computed ts_vectors.

ALTER TABLE item
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors.

CREATE FUNCTION item_search_update() RETURNS TRIGGER AS $$
BEGIN
    NEW.tsvectors = (
        setweight(to_tsvector('english', NEW.name), 'A') ||
        setweight(to_tsvector('english', NEW.description), 'B')
    );
    RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Create trigger before insert or update on item.

CREATE TRIGGER item_search_update
BEFORE INSERT OR UPDATE ON item
FOR EACH ROW
EXECUTE PROCEDURE item_search_update();

-- Finally, create a GIN index for ts_vectors.

CREATE INDEX search_idx ON item USING GIN (tsvectors);

-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

-- TRIGGER 1: Updates the stock of an item when a purchase is made.

CREATE OR REPLACE FUNCTION update_item_stock()
RETURNS TRIGGER AS $BODY$
DECLARE
    item_record RECORD; 
BEGIN
    FOR item_record IN (
        SELECT item.id, cart_item.quantity
        FROM cart_item
        JOIN item ON cart_item.id_item = item.id
        WHERE cart_item.id_cart = NEW.id_cart
    ) LOOP
        UPDATE item
        SET stock = stock - item_record.quantity
        WHERE id = item_record.id;
    END LOOP;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_item_stock
AFTER INSERT ON purchase
FOR EACH ROW
EXECUTE FUNCTION update_item_stock();

-- TRIGGER 2: Updates the review count and average rating for an item whenever a new review is added or an existing review is modified

CREATE OR REPLACE FUNCTION update_item_reviews()
RETURNS TRIGGER AS $BODY$
BEGIN
    UPDATE item
    SET rating = (
        SELECT AVG(rating) FROM review WHERE id_item = NEW.id_item
    )
    WHERE id = NEW.id_item;
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_item_reviews_on_insert
    AFTER INSERT ON review
    FOR EACH ROW
EXECUTE FUNCTION update_item_reviews();
CREATE TRIGGER update_item_reviews_on_update
    AFTER UPDATE ON review
    FOR EACH ROW
EXECUTE FUNCTION update_item_reviews();

 -- TRIGGER 3: Notify When a Wishlist Item Enters Sale

CREATE OR REPLACE FUNCTION notify_wishlist_sale()
RETURNS TRIGGER AS $BODY$
BEGIN
    IF NEW.price < OLD.price THEN
        INSERT INTO notification (description, notification_type, id_user, id_item)
        SELECT 
            'Item on your wishlist (' || OLD.name || ') is now on sale.',
            'SALE',
            w.id_user,
            w.id_item
        FROM wishlist AS w
        WHERE w.id_item = NEW.id;
    END IF;
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER wishlist_sale_notification
    AFTER UPDATE ON item
    FOR EACH ROW
    EXECUTE FUNCTION notify_wishlist_sale(); 
   
-- TRIGGER 4: Notify When a Wishlist Item Enters in Stock

CREATE OR REPLACE FUNCTION notify_wishlist_stock()
RETURNS TRIGGER AS $BODY$
BEGIN
    -- Check if the 'stock' column was updated and the new stock is greater than 0
    IF OLD.stock = 0 AND NEW.stock > 0 THEN
        INSERT INTO notification (description, notification_type, id_user, id_item)
        SELECT 
            'Item on your wishlist (' || NEW.name || ') is now back in stock.',
            'RESTOCK',
            w.id_user,
            w.id_item
        FROM wishlist AS w
        WHERE w.id_item = NEW.id;
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER wishlist_stock_notification
    AFTER UPDATE ON item
    FOR EACH ROW
    EXECUTE FUNCTION notify_wishlist_stock();

-- TRIGGER 5: Notify When a Purchase Status Changes

CREATE OR REPLACE FUNCTION notify_purchase_status_change()
RETURNS TRIGGER AS $BODY$
BEGIN
    IF NEW.purchase_status = 'Packed' THEN
        INSERT INTO notification (description, notification_type, id_user, id_purchase)
        SELECT 
            'Your order (' || p.id || ') has been packet and is now being processed to be sent!',
            'ORDER_UPDATE',
            p.id_user,
            p.id
        FROM purchase AS p
        WHERE p.id = NEW.id AND NEW.purchase_status != OLD.purchase_status;
    END IF;
    IF NEW.purchase_status = 'Sent' THEN
        INSERT INTO notification (description, notification_type, id_user, id_purchase)
        SELECT 
            'Your order (' || p.id || ') has been sent!',
            'ORDER_UPDATE',
            p.id_user,
            p.id
        FROM purchase AS p
        WHERE p.id = NEW.id AND NEW.purchase_status != OLD.purchase_status;
    END IF;
    IF NEW.purchase_status = 'Delivered' THEN
        INSERT INTO notification (description, notification_type, id_user, id_purchase)
        SELECT 
            'Your order (' || p.id || ') has been delivered! Do not forget to leave a review!',
            'ORDER_UPDATE',
            p.id_user,
            p.id
        FROM purchase AS p
        WHERE p.id = NEW.id AND NEW.purchase_status != OLD.purchase_status;
    END IF;
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER purchase_status_change_notification
    AFTER UPDATE ON purchase
    FOR EACH ROW
    EXECUTE FUNCTION notify_purchase_status_change();

-- TRIGGER 6: Change users to a new empty cart whenever they make a purchase

CREATE OR REPLACE FUNCTION create_new_cart_for_user()
RETURNS TRIGGER AS $$
DECLARE
    new_cart_id INTEGER;
BEGIN
    -- Create a new empty cart for the user and capture the new ID
    INSERT INTO cart DEFAULT VALUES RETURNING id INTO new_cart_id;

    -- Update the user's record with the new cart ID
    UPDATE "user" SET id_cart = new_cart_id WHERE id = NEW.id_user;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER user_made_purchase
AFTER INSERT ON purchase
FOR EACH ROW
WHEN (NEW.id_user IS NOT NULL)
EXECUTE FUNCTION create_new_cart_for_user();

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

insert into location (address, city, country, postal_code) values ('9 Sauthoff Circle', 'Goya', 'Argentina', '3450');
insert into location (address, city, country, postal_code) values ('38217 Hagan Place', 'At Tibnī', 'Syria', '4490');
insert into location (address, city, country, postal_code) values ('75191 Texas Place', 'Qutun', 'China', '4490');
insert into location (address, city, country, postal_code) values ('76593 Mockingbird Way', 'Huaylillas', 'Peru', '4490');
insert into location (address, city, country, postal_code) values ('2 Springview Center', 'Boden', 'Sweden', '961 86');
insert into location (address, city, country, postal_code) values ('30 Steensland Center', 'Ḑawrān ad Daydah', 'Yemen', '4490');
insert into location (address, city, country, postal_code) values ('1 Russell Avenue', 'Đắk Glei', 'Vietnam', '4490');
insert into location (address, city, country, postal_code) values ('2 Dixon Parkway', 'Budapest', 'Hungary', '1147');
insert into location (address, city, country, postal_code) values ('7540 Lake View Street', 'Aigínio', 'Greece', '4490');
insert into location (address, city, country, postal_code) values ('33 Mayer Avenue', 'Nagua', 'Dominican Republic', '10118');
insert into location (address, city, country, postal_code) values ('9887 Lawn Center', 'Verkhnyachka', 'Ukraine', '4490');
insert into location (address, city, country, postal_code) values ('19358 Portage Pass', 'Doña Remedios Trinidad', 'Philippines', '3009');
insert into location (address, city, country, postal_code) values ('30257 Nancy Terrace', 'Šentvid pri Stični', 'Slovenia', '1296');
insert into location (address, city, country, postal_code) values ('0 Graceland Point', 'Lipsko', 'Poland', '27-300');
insert into location (address, city, country, postal_code) values ('05918 Cardinal Terrace', 'Sājir', 'Saudi Arabia', '4490');


--- ITEM

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Retro Graphic TShirt', 29.99, 25, 'White', '90s', 'Cotton', 'White TShirt with retro graphic design.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage Denim Jacket', 79.99, 10, 'Blue', '80s', 'Denim', 'A stylish vintage denim jacket.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Classic Flannel Shirt', 45.00, 15, 'Red', '70s', 'Cotton', 'Red flannel shirt with classic look.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage High-waist Jeans', 65.00, 20, 'Blue', '80s', 'Denim', 'High-waisted jeans with a vintage style.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Retro Sneakers', 50.00, 40, 'Multi', '90s', 'Canvas', 'Colorful sneakers with a retro look.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage leather Jacket', 109.99, 0, 'White', '70s', 'Denim', 'A stylish leather denim jacket.');


/* INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage Rock Band TShirt', 35.00, 30, 'Black', '80s', 'Cotton', 'Black TShirt with vintage rock band print.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('70s Denim Jacket', 95.00, 5, 'Blue', '70s', 'Denim', 'Blue denim jacket with 70s styling.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Retro Striped Shirt', 40.00, 25, 'Green', '80s', 'Cotton', 'Green striped shirt with a retro feel.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Classic Blue Jeans', 60.00, 20, 'Blue', '90s', 'Denim', 'Classic blue jeans with a relaxed fit.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Vintage Leather Sneakers', 80.00, 15, 'White', '70s', 'Leather', 'White leather sneakers with vintage design.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Vintage Baseball TShirt', 30.00, 35, 'White', '90s', 'Cotton', 'White baseball TShirt with vintage logo.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( '80s Style Denim Jacket', 89.99, 8, 'Blue', '80s', 'Denim', 'Denim jacket with 80s style accents.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Retro Western Shirt', 55.00, 18, 'Red', '70s', 'Cotton', 'Red western shirt with retro detailing.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Vintage Skinny Jeans', 70.00, 12, 'Black', '80s', 'Denim', 'Black skinny jeans with a vintage cut.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Classic Canvas Sneakers', 65.00, 30, 'Black', '90s', 'Canvas', 'Black classic canvas sneakers.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Vintage Band TShirt', 35.00, 28, 'Grey', '70s', 'Cotton', 'Grey TShirt with vintage band graphic.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Retro Leather Jacket', 120.00, 6, 'Black', '80s', 'Leather', 'Black leather jacket with retro styling.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Classic Plaid Shirt', 50.00, 22, 'Blue', '90s', 'Cotton', 'Blue plaid shirt with classic fit.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Vintage Straight-Leg Jeans', 75.00, 15, 'Blue', '70s', 'Denim', 'Straight-leg jeans with a vintage feel.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Retro High-Top Sneakers', 85.00, 10, 'Red', '80s', 'Canvas', 'Red high-top sneakers with retro flair.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Vintage Logo TShirt', 30.00, 40, 'Blue', '90s', 'Cotton', 'Blue TShirt with vintage brand logo.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( '70s Corduroy Jacket', 110.00, 4, 'Brown', '70s', 'Corduroy', 'Brown corduroy jacket from the 70s.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Retro Short Sleeve Shirt', 45.00, 20, 'Yellow', '80s', 'Cotton', 'Yellow short sleeve shirt with retro print.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Vintage Bootcut Jeans', 68.00, 13, 'Blue', '70s', 'Denim', 'Blue bootcut jeans with vintage styling.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ( 'Classic Leather Sneakers', 90.00, 18, 'White', '90s', 'Leather', 'Classic white leather sneakers.');
*/

--- USER

insert into "user" (username, email, password, phone, id_cart) values ('johndoe', 'johndoe@example.com', '1234567890', '938203081', 1);
insert into "user" (username, email, password, phone, id_cart) values ('bjamieson1', 'sbraxton1@example.com', 'kD7!qF?n&K', '932798895', 2);
insert into "user" (username, email, password, phone, id_cart) values ('kkennelly2', 'ddallywater2@example.com', 'aV8(dRf$kP', '939401278', 3);
insert into "user" (username, email, password, phone, id_cart) values ('tpechell3', 'ffooter3@example.com', 'zI1>5#6a6,k', '938762590', 4);
insert into "user" (username, email, password, phone, id_cart) values ('acastree4', 'jreford4@example.com', 'sO7~eEoK=`W<', '937716046', 5);
insert into "user" (username, email, password, phone, id_cart) values ('smahedy5', 'pboschmann5@example.com', 'fR4&!%#vXkvP', '937796246', 6);
insert into "user" (username, email, password, phone, id_cart) values ('mmcfater6', 'lghelerdini6@example.com', 'cH7#uiRmS`h`', '930855105', 7);
insert into "user" (username, email, password, phone, id_cart) values ('kestable7', 'bswann7@example.com', 'qU1=9mSxgWt+', '935748655', 8);
insert into "user" (username, email, password, phone, id_cart) values ('msommerled8', 'emothersdale8@example.com', 'fJ1`KU<1&$R', '937270532', 9);
insert into "user" (username, email, password, phone, id_cart) values ('amarjoribanks9', 'dmantripp9@example.com', 'bP4.=9)pH\p`', '932783259', 10);
insert into "user" (username, email, password, phone, id_cart) values ('nskilletta', 'kbeckleya@example.com', 'fP7%9BczXBDQ', '933756062', 11);
insert into "user" (username, email, password, phone, id_cart) values ('gdeignanb', 'mkaszperb@example.com', 'gA3|)?lF#eJ', '939431839', 12);
insert into "user" (username, email, password, phone, id_cart) values ('ndurdlec', 'mbenzac@example.com', 'mK9*kVj#4$I<', '932374374',13);
insert into "user" (username, email, password, phone, id_cart) values ('dwhitcombd', 'emadged@example.com', 'gA8\)aOC&h4K', '937788943',14);
insert into "user" (username, email, password, phone, id_cart) values ('evongrollmanne', 'lmccarrolle@example.com', 'aR4}r&=5P`0F', '938541696',15);
insert into "user" (username, email, password, phone, id_cart) values ('pirwinf', 'gkestonf@example.com', 'uU8<G2LXy)R?', '933213027',16);
insert into "user" (username, email, password, phone, id_cart) values ('bliffeyg', 'ldrennang@example.com', 'uN9&S%ccnfmk', '933378542',17);
insert into "user" (username, email, password, phone, id_cart) values ('freichelth', 'bpochonh@example.com', 'wM8=%||FA%QF', '939829485',18);
insert into "user" (username, email, password, phone, id_cart) values ('ahedgesi', 'jantonuttii@example.com', 'gK3=wACQr5T7', '936239761',19);
insert into "user" (username, email, password, phone, id_cart) values ('ftrailj', 'cperchj@example.com', 'wM4|L+.1.''Ki', '933875393',20);


--- ADMIN

insert into admin (username, email, password, phone) values ('tripleh', 'tripleh@example.com', '1234', '102-381-0489');
insert into admin (username, email, password, phone) values ('rkillcross1', 'aairy1@hc360.com', 'zC9$ft53j=&', '438-250-2550');
insert into admin (username, email, password, phone) values ('dvaughanhughes2', 'amillthorpe2@ed.gov', 'bQ4$$}Z,PFl{o', '214-326-3416');
insert into admin (username, email, password, phone) values ('amatterface3', 'ndanneil3@hud.gov', 'cW3?)hMX6Gzbs', '700-964-4874');
insert into admin (username, email, password, phone) values ('pthomasen4', 'gslym4@imdb.com', 'cM2}p)NgRpu6by', '700-772-7895');

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

INSERT INTO review (description,rating,id_user,id_item) values ('This is a masterpiece',5,1,1);
INSERT INTO review (description,rating,id_user,id_item) values ('i do not like this',1,2,1);
INSERT INTO review (description,rating,id_user,id_item) values ('great product, dont like the color tho',4,3,2);
INSERT INTO review (description,rating,id_user,id_item) values ('my name is jeff',5,1,5);
INSERT INTO review (description,rating,id_user,id_item) values ('wow.',5,4,3);
INSERT INTO review (description,rating,id_user,id_item) values ('This is a masterpiece!!',5,1,1);

--- PURCHASE

INSERT INTO purchase (price, purchase_date, delivery_date, purchase_status, payment_method, id_user, id_location, id_cart)
VALUES ( 109.98, '2023-10-10', '2023-10-15', 'Processing', 'Transfer', 1, 1, 1);
INSERT INTO purchase (price, purchase_date, delivery_date, purchase_status, payment_method, id_user, id_location, id_cart)
VALUES (45.00 , '2023-10-08', '2023-10-20', 'Processing', 'Paypal', 2,2, 2);

/* testing notification triggers */

UPDATE item SET stock = 1 WHERE id = 6;
UPDATE item SET price = 99.99 WHERE id = 6;
UPDATE purchase SET purchase_status = 'Packed' WHERE id = 1;
UPDATE purchase SET purchase_status = 'Delivered' WHERE id = 1;
UPDATE purchase SET purchase_status = 'Packed' WHERE id = 2;
UPDATE purchase SET purchase_status = 'Sent' WHERE id = 2;
