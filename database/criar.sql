drop schema if exists lbaw2366 cascade;
create schema lbaw2366;
SET search_path TO lbaw2366;

CREATE TYPE ShirtType as ENUM('Collarless', 'Regular', 'Short sleeve');

CREATE TYPE TshirtType as ENUM ('Regular', 'Long sleeve', 'Football');

CREATE TYPE JacketType as ENUM ('Regular', 'Baseball', 'Bomber');

CREATE TYPE PaymentMethod as ENUM ('Transfer', 'Paypal');

CREATE TYPE PurchaseStatus as ENUM ('Processing', 'Packed', 'Sent', 'Delivered');

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

CREATE TABLE users(
    id SERIAL PRIMARY KEY,
    name TEXT,
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
    id_user INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
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
    id_user INTEGER NOT NULL REFERENCES users(id) ON DELETE SET NULL,
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

-- TRIGGER 7: Notify users when a product in cart changes price

CREATE OR REPLACE FUNCTION notify_cart_item_price_change()
RETURNS TRIGGER AS $BODY$
BEGIN
    IF NEW.price != OLD.price THEN
        INSERT INTO notification (description, notification_type, id_user, id_item)
        SELECT 
            'Item in your cart (' || OLD.name || ') changed price to ' || NEW.price || '.',
            'PRICE_CHANGE',
            ci.id_cart,
            NEW.id
        FROM cart_item AS ci
        WHERE ci.id_item = NEW.id;
    END IF;
    RETURN NEW;
END;
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER cart_item_price_change_notification
AFTER UPDATE ON item
FOR EACH ROW
EXECUTE FUNCTION notify_cart_item_price_change();

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
