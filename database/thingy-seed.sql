drop schema if exists lbaw2366 cascade;
create schema lbaw2366;
SET search_path TO lbaw2366;

----------- types

CREATE TYPE ShirtType as ENUM('Collarless', 'Regular', 'Short Sleeve');

CREATE TYPE TshirtType as ENUM ('Regular', 'Long Sleeve', 'Football');

CREATE TYPE JacketType as ENUM ('Regular', 'Baseball', 'Bomber');

CREATE TYPE JeansType as ENUM ('Regular', 'Skinny', 'Baggy');

CREATE TYPE SneakersType as ENUM ('Leather', 'Casual', 'Sports');

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
    id_user INTEGER REFERENCES users(id) ON DELETE SET NULL,
    id_item INTEGER NOT NULL REFERENCES item(id)
);

CREATE TABLE notification(
    id SERIAL PRIMARY KEY,
    description TEXT NOT NULL, 
    date TIMESTAMP WITH TIME ZONE DEFAULT now() NOT NULL, 
    notification_type NotificationType NOT NULL,
    id_user INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
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

-- CREATE OR REPLACE FUNCTION update_item_stock()
-- RETURNS TRIGGER AS $BODY$
-- DECLARE
--     item_record RECORD; 
-- BEGIN
--     FOR item_record IN (
--         SELECT item.id, cart_item.quantity
--         FROM cart_item
--         JOIN item ON cart_item.id_item = item.id
--         WHERE cart_item.id_cart = NEW.id_cart
--     ) LOOP
--         UPDATE item
--         SET stock = stock - item_record.quantity
--         WHERE id = item_record.id;
--     END LOOP;

--     RETURN NEW;
-- END;
-- $BODY$
-- LANGUAGE plpgsql;

-- CREATE TRIGGER update_item_stock
-- AFTER INSERT ON purchase
-- FOR EACH ROW
-- EXECUTE FUNCTION update_item_stock();

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

-- CREATE OR REPLACE FUNCTION create_new_cart_for_user()
-- RETURNS TRIGGER AS $$
-- DECLARE
--     new_cart_id INTEGER;
-- BEGIN
--     INSERT INTO cart DEFAULT VALUES RETURNING id INTO new_cart_id;

--     UPDATE users SET id_cart = new_cart_id WHERE id = NEW.id_user;

--     RETURN NEW;
-- END;
-- $$ LANGUAGE plpgsql;

-- CREATE TRIGGER user_made_purchase
-- AFTER INSERT ON purchase
-- FOR EACH ROW
-- WHEN (NEW.id_user IS NOT NULL)
-- EXECUTE FUNCTION create_new_cart_for_user();

-- TRIGGER 7: Creating cart for new user

CREATE OR REPLACE FUNCTION create_new_cart_for_new_user()
RETURNS TRIGGER AS $$
DECLARE
    new_cart_id INTEGER;
BEGIN
    INSERT INTO cart DEFAULT VALUES RETURNING id INTO new_cart_id;

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

CREATE OR REPLACE FUNCTION combined_purchase_trigger()
RETURNS TRIGGER AS $$
DECLARE
    item_record RECORD;
    new_cart_id INTEGER;
BEGIN
    IF NEW.id_user IS NOT NULL THEN
        INSERT INTO cart DEFAULT VALUES RETURNING id INTO new_cart_id;
        UPDATE users SET id_cart = new_cart_id WHERE id = NEW.id_user;
    END IF;

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
$$ LANGUAGE plpgsql;

CREATE TRIGGER combined_purchase_trigger
AFTER INSERT ON purchase
FOR EACH ROW
EXECUTE FUNCTION combined_purchase_trigger();



-- TRIGGER 9: Remove item from all carts when its stock is 0

CREATE OR REPLACE FUNCTION remove_items_from_carts()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.stock = 0 THEN
        DELETE FROM cart_item
        WHERE id_item = NEW.id
        AND id_cart NOT IN (SELECT id_cart FROM purchase);
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER remove_from_carts_on_stock_zero
AFTER UPDATE OF stock ON item
FOR EACH ROW
WHEN (OLD.stock != NEW.stock)
EXECUTE FUNCTION remove_items_from_carts();



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

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Striped Polo Shirt', 34.99, 15, 'White, Blue', '2000s', 'Cotton', 'Polo shirt with a classic striped pattern.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Hooded Zip-up Jacket', 69.99, 18, 'Gray', '90s', 'Polyester', 'Comfortable hooded jacket with a zip-up front.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage Corduroy Pants', 42.00, 22, 'Green', '2000s', 'Corduroy', 'Brown corduroy pants with a vintage touch.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Brushed Leather Shoes', 59.99, 35, 'Black', '2010s', 'Leather', 'Classic leathered shoes with details.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Casual Button-up Shirt', 29.99, 28, 'Navy', '80s', 'Cotton', 'Versatile button-up shirt for a casual look.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Casual Regular T-Shirt', 9.99, 50, 'Gray', '2000s', 'Cotton', 'Comfortable regular fit T-shirt in gray.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Athletic Running Shoes', 39.99, 35, 'Gray', '2010s', 'Mesh', 'Performance-oriented running shoes in gray.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage Bomber Jacket', 89.99, 12, 'White, Purple, Black', '80s', 'Satin', 'Vintage bomber jacket with 3 colors. ');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('High-Top Canvas Sneakers', 44.00, 30, 'White', '2010s', 'Canvas', 'White high-top sneakers for a trendy look.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Long Sleeve Graphic Tee', 24.99, 35, 'Black', '2010s', 'Cotton', 'Stylish long sleeve T-shirt with a graphic design in black.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Football Jersey', 99.99, 1, 'Red', '2000s', 'Polyester', 'Footbal jersey from the 2008/09 season in red');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Classic Baseball Jacket', 74.99, 15, 'Olive', '80s', 'Wool', 'Stylish olive green bomber jacket.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Cargo Pants', 47.00, 20, 'Olive', '2000s', 'Cotton', 'Comfortable khaki cargo pants.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Suede Ankle Boots', 79.99, 25, 'Tan', '90s', 'Suede', 'Fashionable tan suede ankle boots.');

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Football Jersey', 89.99, 3, 'White', '90s', 'Polyester', 'White footbal jersey from the 1996/97 season');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Hip-Hop Group Shirt', 14.99, 15, 'Yellow', '90s', 'Cotton', 'Yellow T-shirt in honor of an old-school hip-hop group');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Distressed Band Tour T-Shirt', 14.99, 25, 'Black', '70s', 'Cotton', 'Black T-shirt from a rock band tour');

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Vintage Beach Shirt', 19.99, 25, 'Green', '2010s', 'Cotton', 'Green T-shirt adequate for sunsets in the beach.');

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('High-Waist Flared Jeans', 35.00, 18, 'Light Blue', '2010s', 'Denim', 'Light blue high-waisted flared jeans, a staple from the 2010s era.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Cargo Shorts', 29.99, 20, 'Blue', '80s', 'Denim', 'Channel the 1980s with these acid-washed skinny jeans for a bold vintage look.');

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Designed Old School Jacket', 89.99, 10, 'Olive', '2000s', 'Corduroy', 'Olive green corduroy bomber jacket, a timeless piece from the 2000s.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Leather Aviator Jacket', 129.99, 8, 'Dark Brown', '80s', 'Leather', 'Dark brown leather aviator jacket, capturing the spirit of vintage aviation.');

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Canvas Low-Top Sneakers', 45.00, 25, 'White', '2010s', 'Canvas', 'Cream-colored canvas low-top sneakers, reminiscent of the 2010s fashion era.');
INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Old School Basketball Shoes', 59.00, 15, 'White', '90s', 'Leather', 'Shoes worn by basketball players in the 2000s that turned into famous streetwear.');





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

INSERT INTO image (id_item, filepath) VALUES (2, 'images/vintage_denim_jacket_1.png');
INSERT INTO image (id_item, filepath) VALUES (2, 'images/vintage_denim_jacket_2.png');

INSERT INTO image (id_item, filepath) VALUES (3, 'images/classic_flannel_shirt_1.png');

INSERT INTO image (id_item, filepath) VALUES (4, 'images/vintage_highwaist_jeans_1.png');

INSERT INTO image (id_item, filepath) VALUES (5, 'images/retro_sneakers_1.png');

INSERT INTO image (id_item, filepath) VALUES (7, 'images/striped_polo_shirt_1.png');
INSERT INTO image (id_item, filepath) VALUES (7, 'images/striped_polo_shirt_2.png');

INSERT INTO image (id_item, filepath) VALUES (8, 'images/hooded_zipup_jacket_1.png');
INSERT INTO image (id_item, filepath) VALUES (8, 'images/hooded_zipup_jacket_2.png');

INSERT INTO image (id_item, filepath) VALUES (9, 'images/vintage_corduroy_pants.png');

INSERT INTO image (id_item, filepath) VALUES (10, 'images/brushed_leather_shoes_1.png');
INSERT INTO image (id_item, filepath) VALUES (10, 'images/brushed_leather_shoes_2.png');

INSERT INTO image (id_item, filepath) VALUES (11, 'images/casual_button_shirt.png');

INSERT INTO image (id_item, filepath) VALUES (12, 'images/casual_regular_tshirt.png');

INSERT INTO image (id_item, filepath) VALUES (13, 'images/athletic_running_shoes.png');

INSERT INTO image (id_item, filepath) VALUES (14, 'images/graphic_bomber_jacket.png');

INSERT INTO image (id_item, filepath) VALUES (15, 'images/hightop_canvas_sneakers.png');

INSERT INTO image (id_item, filepath) VALUES (16, 'images/long_sleeve_graphic_tshirt.png');

INSERT INTO image (id_item, filepath) VALUES (17, 'images/football_jersey.png');

INSERT INTO image (id_item, filepath) VALUES (18, 'images/classic_baseball_jacket.png');

INSERT INTO image (id_item, filepath) VALUES (19, 'images/cargo_pants.png');

INSERT INTO image (id_item, filepath) VALUES (20, 'images/suede_anke_boots.png');

INSERT INTO image (id_item, filepath) VALUES (21, 'images/white_football_jersey.png');

INSERT INTO image (id_item, filepath) VALUES (22, 'images/hh_group_shirt.png');

INSERT INTO image (id_item, filepath) VALUES (23, 'images/rock_band_tshirt.png');

INSERT INTO image (id_item, filepath) VALUES (24, 'images/beach_shirt.png');

INSERT INTO image (id_item, filepath) VALUES (25, 'images/flared_jeans.png');

INSERT INTO image (id_item, filepath) VALUES (26, 'images/cargo_shorts.png');

INSERT INTO image (id_item, filepath) VALUES (27, 'images/designer_vintage_jacket.png');

INSERT INTO image (id_item, filepath) VALUES (28, 'images/leather_aviator_jacket.png');

INSERT INTO image (id_item, filepath) VALUES (29, 'images/canvas_low_top.png');

INSERT INTO image (id_item, filepath) VALUES (30, 'images/basketball_shoes.png');


INSERT INTO image (id_user, filepath) VALUES (1, 'images/profile_user_1.png');

--- SHIRT

INSERT INTO shirt (id_item, shirt_type, size) VALUES (3, 'Regular', 'M');
INSERT INTO shirt (id_item, shirt_type, size) VALUES (7, 'Short Sleeve', 'S');
INSERT INTO shirt (id_item, shirt_type, size) VALUES (11, 'Regular', 'XL');
INSERT INTO shirt (id_item, shirt_type, size) VALUES (24, 'Short Sleeve', 'M');

--- TSHIRT

INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (1, 'Regular', 'L');
INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (12, 'Regular', 'XL');
INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (16, 'Long Sleeve', 'M');
INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (17, 'Football', 'S');
INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (21, 'Football', 'L');
INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (22, 'Regular', 'M');
INSERT INTO tshirt (id_item, tshirt_type, size) VALUES (23, 'Regular', 'XL');

--- JACKET

INSERT INTO jacket (id_item, jacket_type, size) VALUES (2, 'Bomber', 'S');
INSERT INTO jacket (id_item, jacket_type, size) VALUES (6, 'Regular', 'M');
INSERT INTO jacket (id_item, jacket_type, size) VALUES (8, 'Regular', 'L');
INSERT INTO jacket (id_item, jacket_type, size) VALUES (14, 'Bomber', 'XL');
INSERT INTO jacket (id_item, jacket_type, size) VALUES (18, 'Baseball', 'M');
INSERT INTO jacket (id_item, jacket_type, size) VALUES (27, 'Regular', 'M');
INSERT INTO jacket (id_item, jacket_type, size) VALUES (28, 'Regular', 'L');


--- JEANS

INSERT INTO jeans (id_item, jeans_type, size) VALUES (4, 'Regular', 'S');
INSERT INTO jeans (id_item, jeans_type, size) VALUES (9, 'Regular', 'M');
INSERT INTO jeans (id_item, jeans_type, size) VALUES (19, 'Baggy', 'M');
INSERT INTO jeans (id_item, jeans_type, size) VALUES (25, 'Regular', 'M');
INSERT INTO jeans (id_item, jeans_type, size) VALUES (26, 'Skinny', 'S');

--- sneakers

INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (5, 'Casual', '38');
INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (10, 'Leather', '41');
INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (13, 'Sports', '43');
INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (15, 'Casual', '36');
INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (20, 'Casual', '45');
INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (29, 'Casual', '45');
INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (30, 'Sports', '45');




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

--testing

INSERT INTO item (name, price, stock, color, era, fabric, description) VALUES ('Test', 1, 1, 'White', '70s', 'Denim', 'A stylish leather denim jacket.');
INSERT INTO sneakers (id_item, sneakers_type, size) VALUES (7, 'Casual', '39');
INSERT INTO cart_item (id_cart, id_item) VALUES (11, 7);
INSERT INTO cart_item (id_cart, id_item) VALUES (12, 7);
-- INSERT INTO purchase (price, purchase_date, delivery_date, purchase_status, payment_method, id_user, id_location, id_cart)
-- VALUES ( 1, '2023-10-10', '2023-10-15', 'Paid', 'Transfer', 1, 1, 12);


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
