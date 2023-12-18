drop schema if exists lbaw2366 cascade;
create schema lbaw2366;
SET search_path TO lbaw2366;

----------- types

CREATE TYPE ShirtType as ENUM('Collarless', 'Regular', 'Short sleeve');

CREATE TYPE TshirtType as ENUM ('Regular', 'Long sleeve', 'Football');

CREATE TYPE JacketType as ENUM ('Regular', 'Baseball', 'Bomber');

CREATE TYPE JeansType as ENUM ('Regular', 'Skinny', 'Baggy');

CREATE TYPE SneakersType as ENUM ('Leather', 'Casual');

CREATE TYPE PaymentMethod as ENUM ('Transfer', 'Paypal');

CREATE TYPE PurchaseStatus as ENUM ('Paid', 'Packed', 'Sent', 'Delivered');


CREATE TYPE NotificationType as ENUM ('SALE', 'RESTOCK','ORDER_UPDATE', 'PRICE_CHANGE');

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

CREATE TABLE location(
    id SERIAL PRIMARY KEY,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    country TEXT NOT NULL,
    postal_code TEXT NOT NULL,
    description TEXT
);

CREATE TABLE users(
    id SERIAL PRIMARY KEY,
    name TEXT,
    username TEXT NOT NULL CONSTRAINT username_uk UNIQUE,
    email TEXT NOT NULL CONSTRAINT user_email_uk UNIQUE,
    password TEXT NOT NULL CONSTRAINT password_length CHECK (length(password) >= 10),
    phone VARCHAR(20), 
    is_banned boolean NOT NULL DEFAULT FALSE,
    remember_token TEXT DEFAULT NULL,
    id_cart INTEGER REFERENCES cart(id),
    id_location INTEGER REFERENCES location(id)
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
    id_user INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    id_item INTEGER NOT NULL REFERENCES item(id),
    PRIMARY KEY(id_user, id_item)
);

CREATE TABLE purchase(
    id SERIAL PRIMARY KEY,
    price FLOAT NOT NULL CONSTRAINT price_positive CHECK (price > 0.0),
    purchase_date DATE NOT NULL,
    delivery_date DATE NOT NULL CONSTRAINT delivery_date_check CHECK (delivery_date >= purchase_date),
    purchase_status PurchaseStatus NOT NULL,
    payment_method PaymentMethod NOT NULL,
    id_user INTEGER REFERENCES users(id) ON DELETE SET NULL,
    id_location INTEGER NOT NULL REFERENCES location(id),
    id_cart INTEGER NOT NULL REFERENCES cart(id)
);

CREATE TABLE review(
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL CONSTRAINT description_length CHECK (length(description) <= 200),
    rating FLOAT NOT NULL CONSTRAINT rating_positive CHECK (rating >= 0.0 AND rating <= 5.0),
    up_votes INTEGER DEFAULT 0,
    down_votes INTEGER DEFAULT 0,
    id_user INTEGER REFERENCES users(id) ON DELETE SET NULL,
    id_item INTEGER NOT NULL REFERENCES item(id)
);

CREATE TABLE notification(
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL, 
    date TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL, 
    notification_type NotificationType NOT NULL,
    id_user INTEGER NOT NULL REFERENCES users(id) ON DELETE SET NULL,
    id_item INTEGER  REFERENCES item(id) ON DELETE SET NULL,
    id_purchase INTEGER REFERENCES purchase(id) ON DELETE SET NULL
);

CREATE TABLE image(
    id serial PRIMARY KEY,
    id_item INTEGER REFERENCES item(id) ON DELETE CASCADE,
    id_user INTEGER REFERENCES users(id) ON DELETE CASCADE,
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

CREATE TABLE sneakers(
    id_item INTEGER PRIMARY KEY REFERENCES item(id) ON DELETE CASCADE,
    sneakers_type SneakersType NOT NULL,
    size INTEGER NOT NULL CONSTRAINT size_check CHECK (size >= 0)
);

CREATE TABLE jeans(
    id_item INTEGER PRIMARY KEY REFERENCES item(id) ON DELETE CASCADE,
    jeans_type JeansType NOT NULL,
    size TEXT NOT NULL
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
    UPDATE users SET id_cart = new_cart_id WHERE id = NEW.id_user;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER user_made_purchase
AFTER INSERT ON purchase
FOR EACH ROW
WHEN (NEW.id_user IS NOT NULL)
EXECUTE FUNCTION create_new_cart_for_user();

-- TRIGGER 7: Creating cart for new user

CREATE OR REPLACE FUNCTION create_new_cart_for_new_user()
RETURNS TRIGGER AS $$
DECLARE
    new_cart_id INTEGER;
BEGIN
    -- Create a new empty cart for the user and capture the new ID
    INSERT INTO cart DEFAULT VALUES RETURNING id INTO new_cart_id;

    -- Update the user's record with the new cart ID
    UPDATE users SET id_cart = new_cart_id WHERE id = NEW.id;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER user_registered
AFTER INSERT ON users
FOR EACH ROW
WHEN (NEW.id IS NOT NULL)
EXECUTE FUNCTION create_new_cart_for_new_user();

-- TRIGGER 8: Notify users when a product in cart changes price

CREATE OR REPLACE FUNCTION notify_cart_item_price_change()
RETURNS TRIGGER AS $BODY$
BEGIN
    IF NEW.price != OLD.price THEN
        INSERT INTO notification (description, notification_type, id_user, id_item)
        SELECT
            'Item in your cart ("' || OLD.name || '") changed price to ' || NEW.price || '.',
            'PRICE_CHANGE',
            u.id,
            NEW.id
        FROM cart_item AS ci
        JOIN users AS u ON ci.id_cart = u.id_cart
        WHERE ci.id_item = NEW.id;
    END IF;
    RETURN NEW;
END;
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER cart_item_price_change_notification
AFTER UPDATE ON item
FOR EACH ROW
EXECUTE FUNCTION notify_cart_item_price_change();

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
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage Leather Jacket', 109.99, 0, 'White', '70s', 'Denim', 'A stylish leather denim jacket.');

--- USER

insert into users (username, email, password, phone) values ('johndoe', 'johndoe@example.com', '$2y$10$xAvXOTsApkcRzaJ0ZKQyyuE24KAc0X8RfTJxHMtDHSc7fcOvTQxjK', '938203081'); -- password is 1234567890
insert into users (username, email, password, phone) values ('bjamieson1', 'sbraxton1@example.com', 'kD7!qF?n&K', '932798895');
insert into users (username, email, password, phone) values ('kkennelly2', 'ddallywater2@example.com', 'aV8(dRf$kP', '939401278');
insert into users (username, email, password, phone) values ('tpechell3', 'ffooter3@example.com', 'zI1>5#6a6,k', '938762590');
insert into users (username, email, password, phone) values ('acastree4', 'jreford4@example.com', 'sO7~eEoK=`W<', '937716046');
insert into users (username, email, password, phone) values ('smahedy5', 'pboschmann5@example.com', 'fR4&!%#vXkvP', '937796246');
insert into users (username, email, password, phone) values ('mmcfater6', 'lghelerdini6@example.com', 'cH7#uiRmS`h`', '930855105');
insert into users (username, email, password, phone) values ('kestable7', 'bswann7@example.com', 'qU1=9mSxgWt+', '935748655');
insert into users (username, email, password, phone) values ('msommerled8', 'emothersdale8@example.com', 'fJ1`KU<1&$R', '937270532');
insert into users (username, email, password, phone) values ('amarjoribanks9', 'dmantripp9@example.com', 'bP4.=9)pH\p`', '932783259');
insert into users (username, email, password, phone) values ('nskilletta', 'kbeckleya@example.com', 'fP7%9BczXBDQ', '933756062');
insert into users (username, email, password, phone) values ('gdeignanb', 'mkaszperb@example.com', 'gA3|)?lF#eJ', '939431839');
insert into users (username, email, password, phone) values ('ndurdlec', 'mbenzac@example.com', 'mK9*kVj#4$I<', '932374374');
insert into users (username, email, password, phone) values ('dwhitcombd', 'emadged@example.com', 'gA8\)aOC&h4K', '937788943');
insert into users (username, email, password, phone) values ('evongrollmanne', 'lmccarrolle@example.com', 'aR4}r&=5P`0F', '938541696');
insert into users (username, email, password, phone) values ('pirwinf', 'gkestonf@example.com', 'uU8<G2LXy)R?', '933213027');
insert into users (username, email, password, phone) values ('bliffeyg', 'ldrennang@example.com', 'uN9&S%ccnfmk', '933378542');
insert into users (username, email, password, phone) values ('freichelth', 'bpochonh@example.com', 'wM8=%||FA%QF', '939829485');
insert into users (username, email, password, phone) values ('ahedgesi', 'jantonuttii@example.com', 'gK3=wACQr5T7', '936239761');
insert into users (username, email, password, phone) values ('ftrailj', 'cperchj@example.com', 'wM4|L+.1.''Ki', '933875393');


--- ADMIN

insert into admin (username, email, password, phone) values ('tripleh', 'tripleh@example.com', '$2y$10$011i8OjsUtRMBWbhww3oh.zzv.RmdiN.qufOgiTR52nv5GKJLph.y', '102-381-0489'); -- password is 1234
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
INSERT INTO jacket (id_item, jacket_type, size) VALUES (6, 'Regular', 'M');


--- JEANS

INSERT INTO jeans (id_item, jeans_type, size) VALUES (4, 'Regular', 'S');

--- sneakers

INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (5, 'Casual', '38');

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
VALUES ( 109.98, '2023-10-10', '2023-10-15', 'Paid', 'Transfer', 1, 1, 1);
INSERT INTO purchase (price, purchase_date, delivery_date, purchase_status, payment_method, id_user, id_location, id_cart)
VALUES (45.00 , '2023-10-08', '2023-10-20', 'Paid', 'Paypal', 2,2, 2);

/* testing notification triggers */

UPDATE item SET stock = 1 WHERE id = 6;
UPDATE item SET price = 99.99 WHERE id = 6;
UPDATE purchase SET purchase_status = 'Packed' WHERE id = 1;
UPDATE purchase SET purchase_status = 'Delivered' WHERE id = 1;
UPDATE purchase SET purchase_status = 'Packed' WHERE id = 2;
UPDATE purchase SET purchase_status = 'Sent' WHERE id = 2;
