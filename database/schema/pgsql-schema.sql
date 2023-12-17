--
-- PostgreSQL database dump
--

-- Dumped from database version 11.16 (Debian 11.16-1.pgdg90+1)
-- Dumped by pg_dump version 14.9 (Ubuntu 14.9-0ubuntu0.22.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: lbaw2366; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA lbaw2366;


--
-- Name: jackettype; Type: TYPE; Schema: lbaw2366; Owner: -
--

CREATE TYPE lbaw2366.jackettype AS ENUM (
    'Regular',
    'Baseball',
    'Bomber'
);


--
-- Name: notificationtype; Type: TYPE; Schema: lbaw2366; Owner: -
--

CREATE TYPE lbaw2366.notificationtype AS ENUM (
    'SALE',
    'RESTOCK',
    'ORDER_UPDATE'
);


--
-- Name: paymentmethod; Type: TYPE; Schema: lbaw2366; Owner: -
--

CREATE TYPE lbaw2366.paymentmethod AS ENUM (
    'Transfer',
    'Paypal'
);


--
-- Name: purchasestatus; Type: TYPE; Schema: lbaw2366; Owner: -
--

CREATE TYPE lbaw2366.purchasestatus AS ENUM (
    'Processing',
    'Packed',
    'Sent',
    'Delivered'
);


--
-- Name: shirttype; Type: TYPE; Schema: lbaw2366; Owner: -
--

CREATE TYPE lbaw2366.shirttype AS ENUM (
    'Collarless',
    'Regular',
    'Short sleeve'
);


--
-- Name: tshirttype; Type: TYPE; Schema: lbaw2366; Owner: -
--

CREATE TYPE lbaw2366.tshirttype AS ENUM (
    'Regular',
    'Long sleeve',
    'Football'
);


--
-- Name: create_new_cart_for_user(); Type: FUNCTION; Schema: lbaw2366; Owner: -
--

CREATE FUNCTION lbaw2366.create_new_cart_for_user() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
    new_cart_id INTEGER;
BEGIN
    -- Create a new empty cart for the user and capture the new ID
    INSERT INTO cart DEFAULT VALUES RETURNING id INTO new_cart_id;

    -- Update the user's record with the new cart ID
    UPDATE "user" SET id_cart = new_cart_id WHERE id = NEW.id_user;

    RETURN NEW;
END;
$$;


--
-- Name: item_search_update(); Type: FUNCTION; Schema: lbaw2366; Owner: -
--

CREATE FUNCTION lbaw2366.item_search_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.tsvectors = (
        setweight(to_tsvector('english', NEW.name), 'A') ||
        setweight(to_tsvector('english', NEW.description), 'B')
    );
    RETURN NEW;
END $$;


--
-- Name: notify_purchase_status_change(); Type: FUNCTION; Schema: lbaw2366; Owner: -
--

CREATE FUNCTION lbaw2366.notify_purchase_status_change() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


--
-- Name: notify_wishlist_sale(); Type: FUNCTION; Schema: lbaw2366; Owner: -
--

CREATE FUNCTION lbaw2366.notify_wishlist_sale() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


--
-- Name: notify_wishlist_stock(); Type: FUNCTION; Schema: lbaw2366; Owner: -
--

CREATE FUNCTION lbaw2366.notify_wishlist_stock() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


--
-- Name: update_item_reviews(); Type: FUNCTION; Schema: lbaw2366; Owner: -
--

CREATE FUNCTION lbaw2366.update_item_reviews() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE item
    SET rating = (
        SELECT AVG(rating) FROM review WHERE id_item = NEW.id_item
    )
    WHERE id = NEW.id_item;
    RETURN NEW;
END;
$$;


--
-- Name: update_item_stock(); Type: FUNCTION; Schema: lbaw2366; Owner: -
--

CREATE FUNCTION lbaw2366.update_item_stock() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
$$;


SET default_tablespace = '';

--
-- Name: admin; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.admin (
    id integer NOT NULL,
    username text NOT NULL,
    email text NOT NULL,
    password text NOT NULL,
    phone character varying(20)
);


--
-- Name: admin_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.admin_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admin_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.admin_id_seq OWNED BY lbaw2366.admin.id;


--
-- Name: cart; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.cart (
    id integer NOT NULL
);


--
-- Name: cart_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

ALTER TABLE lbaw2366.cart ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME lbaw2366.cart_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: cart_item; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.cart_item (
    id_cart integer NOT NULL,
    id_item integer NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    CONSTRAINT quantity_positive CHECK ((quantity > 0))
);


--
-- Name: image; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.image (
    id integer NOT NULL,
    id_item integer,
    id_user integer,
    filepath text
);


--
-- Name: image_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.image_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: image_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.image_id_seq OWNED BY lbaw2366.image.id;


--
-- Name: item; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.item (
    id integer NOT NULL,
    name text NOT NULL,
    price double precision NOT NULL,
    rating double precision DEFAULT 0.0 NOT NULL,
    stock integer NOT NULL,
    color text NOT NULL,
    era text,
    fabric text,
    description text,
    brand text,
    tsvectors tsvector,
    CONSTRAINT price_positive CHECK ((price > (0.0)::double precision)),
    CONSTRAINT rating_positive CHECK (((rating >= (0.0)::double precision) AND (rating <= (5.0)::double precision))),
    CONSTRAINT stock_positive CHECK ((stock >= 0))
);


--
-- Name: item_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.item_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: item_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.item_id_seq OWNED BY lbaw2366.item.id;


--
-- Name: jacket; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.jacket (
    id_item integer NOT NULL,
    jacket_type lbaw2366.jackettype NOT NULL,
    size text NOT NULL
);


--
-- Name: jeans; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.jeans (
    id_item integer NOT NULL,
    waist_size integer NOT NULL,
    inseam_size integer NOT NULL,
    rise_size integer NOT NULL,
    CONSTRAINT inseam_size_check CHECK ((inseam_size > 0)),
    CONSTRAINT rise_size_check CHECK ((rise_size > 0)),
    CONSTRAINT waist_size_check CHECK ((waist_size > 0))
);


--
-- Name: location; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.location (
    id integer NOT NULL,
    address text NOT NULL,
    city text NOT NULL,
    country text NOT NULL,
    postal_code text NOT NULL,
    description text
);


--
-- Name: location_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.location_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: location_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.location_id_seq OWNED BY lbaw2366.location.id;


--
-- Name: notification; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.notification (
    id integer NOT NULL,
    description text NOT NULL,
    date timestamp with time zone DEFAULT now() NOT NULL,
    notification_type lbaw2366.notificationtype NOT NULL,
    id_user integer NOT NULL,
    id_item integer,
    id_purchase integer
);


--
-- Name: notification_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.notification_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notification_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.notification_id_seq OWNED BY lbaw2366.notification.id;


--
-- Name: purchase; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.purchase (
    id integer NOT NULL,
    price double precision NOT NULL,
    purchase_date date NOT NULL,
    delivery_date date NOT NULL,
    purchase_status lbaw2366.purchasestatus NOT NULL,
    payment_method lbaw2366.paymentmethod NOT NULL,
    id_user integer NOT NULL,
    id_location integer NOT NULL,
    id_cart integer NOT NULL,
    CONSTRAINT delivery_date_check CHECK ((delivery_date >= purchase_date)),
    CONSTRAINT price_positive CHECK ((price > (0.0)::double precision))
);


--
-- Name: purchase_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.purchase_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: purchase_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.purchase_id_seq OWNED BY lbaw2366.purchase.id;


--
-- Name: review; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.review (
    id integer NOT NULL,
    description text NOT NULL,
    rating double precision NOT NULL,
    up_votes integer DEFAULT 0,
    down_votes integer DEFAULT 0,
    id_user integer,
    id_item integer NOT NULL,
    CONSTRAINT description_length CHECK ((length(description) <= 200)),
    CONSTRAINT rating_positive CHECK (((rating >= (0.0)::double precision) AND (rating <= (5.0)::double precision)))
);


--
-- Name: review_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.review_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: review_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.review_id_seq OWNED BY lbaw2366.review.id;


--
-- Name: shirt; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.shirt (
    id_item integer NOT NULL,
    shirt_type lbaw2366.shirttype NOT NULL,
    size text NOT NULL
);


--
-- Name: sneakers; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.sneakers (
    id_item integer NOT NULL,
    shoe_size integer NOT NULL,
    CONSTRAINT shoe_size_check CHECK ((shoe_size >= 0))
);


--
-- Name: tshirt; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.tshirt (
    id_item integer NOT NULL,
    tshirt_type lbaw2366.tshirttype NOT NULL,
    size text NOT NULL
);


--
-- Name: user; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366."user" (
    id integer NOT NULL,
    username text NOT NULL,
    email text NOT NULL,
    password text NOT NULL,
    phone character varying(20),
    is_banned boolean DEFAULT false NOT NULL,
    remember_token text,
    id_cart integer NOT NULL,
    CONSTRAINT password_length CHECK ((length(password) >= 10))
);


--
-- Name: user_id_seq; Type: SEQUENCE; Schema: lbaw2366; Owner: -
--

CREATE SEQUENCE lbaw2366.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: lbaw2366; Owner: -
--

ALTER SEQUENCE lbaw2366.user_id_seq OWNED BY lbaw2366."user".id;


--
-- Name: wishlist; Type: TABLE; Schema: lbaw2366; Owner: -
--

CREATE TABLE lbaw2366.wishlist (
    id_user integer NOT NULL,
    id_item integer NOT NULL
);


--
-- Name: admin id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.admin ALTER COLUMN id SET DEFAULT nextval('lbaw2366.admin_id_seq'::regclass);


--
-- Name: image id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.image ALTER COLUMN id SET DEFAULT nextval('lbaw2366.image_id_seq'::regclass);


--
-- Name: item id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.item ALTER COLUMN id SET DEFAULT nextval('lbaw2366.item_id_seq'::regclass);


--
-- Name: location id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.location ALTER COLUMN id SET DEFAULT nextval('lbaw2366.location_id_seq'::regclass);


--
-- Name: notification id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.notification ALTER COLUMN id SET DEFAULT nextval('lbaw2366.notification_id_seq'::regclass);


--
-- Name: purchase id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.purchase ALTER COLUMN id SET DEFAULT nextval('lbaw2366.purchase_id_seq'::regclass);


--
-- Name: review id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.review ALTER COLUMN id SET DEFAULT nextval('lbaw2366.review_id_seq'::regclass);


--
-- Name: user id; Type: DEFAULT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366."user" ALTER COLUMN id SET DEFAULT nextval('lbaw2366.user_id_seq'::regclass);


--
-- Name: admin admin_email_key; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.admin
    ADD CONSTRAINT admin_email_key UNIQUE (email);


--
-- Name: admin admin_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.admin
    ADD CONSTRAINT admin_pkey PRIMARY KEY (id);


--
-- Name: admin admin_username_key; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.admin
    ADD CONSTRAINT admin_username_key UNIQUE (username);


--
-- Name: cart_item cart_item_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.cart_item
    ADD CONSTRAINT cart_item_pkey PRIMARY KEY (id_cart, id_item);


--
-- Name: cart cart_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.cart
    ADD CONSTRAINT cart_pkey PRIMARY KEY (id);


--
-- Name: image image_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.image
    ADD CONSTRAINT image_pkey PRIMARY KEY (id);


--
-- Name: item item_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.item
    ADD CONSTRAINT item_pkey PRIMARY KEY (id);


--
-- Name: jacket jacket_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.jacket
    ADD CONSTRAINT jacket_pkey PRIMARY KEY (id_item);


--
-- Name: jeans jeans_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.jeans
    ADD CONSTRAINT jeans_pkey PRIMARY KEY (id_item);


--
-- Name: location location_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.location
    ADD CONSTRAINT location_pkey PRIMARY KEY (id);


--
-- Name: notification notification_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.notification
    ADD CONSTRAINT notification_pkey PRIMARY KEY (id);


--
-- Name: purchase purchase_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.purchase
    ADD CONSTRAINT purchase_pkey PRIMARY KEY (id);


--
-- Name: review review_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.review
    ADD CONSTRAINT review_pkey PRIMARY KEY (id);


--
-- Name: shirt shirt_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.shirt
    ADD CONSTRAINT shirt_pkey PRIMARY KEY (id_item);


--
-- Name: sneakers sneakers_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.sneakers
    ADD CONSTRAINT sneakers_pkey PRIMARY KEY (id_item);


--
-- Name: tshirt tshirt_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.tshirt
    ADD CONSTRAINT tshirt_pkey PRIMARY KEY (id_item);


--
-- Name: user user_email_uk; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366."user"
    ADD CONSTRAINT user_email_uk UNIQUE (email);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: user username_uk; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366."user"
    ADD CONSTRAINT username_uk UNIQUE (username);


--
-- Name: wishlist wishlist_pkey; Type: CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.wishlist
    ADD CONSTRAINT wishlist_pkey PRIMARY KEY (id_user, id_item);


--
-- Name: item_brand_index; Type: INDEX; Schema: lbaw2366; Owner: -
--

CREATE INDEX item_brand_index ON lbaw2366.item USING hash (brand);


--
-- Name: price_index; Type: INDEX; Schema: lbaw2366; Owner: -
--

CREATE INDEX price_index ON lbaw2366.item USING btree (price);


--
-- Name: review_item_id_index; Type: INDEX; Schema: lbaw2366; Owner: -
--

CREATE INDEX review_item_id_index ON lbaw2366.review USING btree (id_item);

ALTER TABLE lbaw2366.review CLUSTER ON review_item_id_index;


--
-- Name: search_idx; Type: INDEX; Schema: lbaw2366; Owner: -
--

CREATE INDEX search_idx ON lbaw2366.item USING gin (tsvectors);


--
-- Name: item item_search_update; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER item_search_update BEFORE INSERT OR UPDATE ON lbaw2366.item FOR EACH ROW EXECUTE PROCEDURE lbaw2366.item_search_update();


--
-- Name: purchase purchase_status_change_notification; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER purchase_status_change_notification AFTER UPDATE ON lbaw2366.purchase FOR EACH ROW EXECUTE PROCEDURE lbaw2366.notify_purchase_status_change();


--
-- Name: review update_item_reviews_on_insert; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER update_item_reviews_on_insert AFTER INSERT ON lbaw2366.review FOR EACH ROW EXECUTE PROCEDURE lbaw2366.update_item_reviews();


--
-- Name: review update_item_reviews_on_update; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER update_item_reviews_on_update AFTER UPDATE ON lbaw2366.review FOR EACH ROW EXECUTE PROCEDURE lbaw2366.update_item_reviews();


--
-- Name: purchase update_item_stock; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER update_item_stock AFTER INSERT ON lbaw2366.purchase FOR EACH ROW EXECUTE PROCEDURE lbaw2366.update_item_stock();


--
-- Name: purchase user_made_purchase; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER user_made_purchase AFTER INSERT ON lbaw2366.purchase FOR EACH ROW WHEN ((new.id_user IS NOT NULL)) EXECUTE PROCEDURE lbaw2366.create_new_cart_for_user();


--
-- Name: item wishlist_sale_notification; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER wishlist_sale_notification AFTER UPDATE ON lbaw2366.item FOR EACH ROW EXECUTE PROCEDURE lbaw2366.notify_wishlist_sale();


--
-- Name: item wishlist_stock_notification; Type: TRIGGER; Schema: lbaw2366; Owner: -
--

CREATE TRIGGER wishlist_stock_notification AFTER UPDATE ON lbaw2366.item FOR EACH ROW EXECUTE PROCEDURE lbaw2366.notify_wishlist_stock();


--
-- Name: cart_item cart_item_id_cart_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.cart_item
    ADD CONSTRAINT cart_item_id_cart_fkey FOREIGN KEY (id_cart) REFERENCES lbaw2366.cart(id);


--
-- Name: cart_item cart_item_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.cart_item
    ADD CONSTRAINT cart_item_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id);


--
-- Name: image image_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.image
    ADD CONSTRAINT image_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id) ON DELETE CASCADE;


--
-- Name: image image_id_user_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.image
    ADD CONSTRAINT image_id_user_fkey FOREIGN KEY (id_user) REFERENCES lbaw2366."user"(id) ON DELETE CASCADE;


--
-- Name: jacket jacket_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.jacket
    ADD CONSTRAINT jacket_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id) ON DELETE CASCADE;


--
-- Name: jeans jeans_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.jeans
    ADD CONSTRAINT jeans_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id) ON DELETE CASCADE;


--
-- Name: notification notification_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.notification
    ADD CONSTRAINT notification_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id) ON DELETE SET NULL;


--
-- Name: notification notification_id_purchase_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.notification
    ADD CONSTRAINT notification_id_purchase_fkey FOREIGN KEY (id_purchase) REFERENCES lbaw2366.purchase(id) ON DELETE SET NULL;


--
-- Name: notification notification_id_user_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.notification
    ADD CONSTRAINT notification_id_user_fkey FOREIGN KEY (id_user) REFERENCES lbaw2366."user"(id) ON DELETE SET NULL;


--
-- Name: purchase purchase_id_cart_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.purchase
    ADD CONSTRAINT purchase_id_cart_fkey FOREIGN KEY (id_cart) REFERENCES lbaw2366.cart(id);


--
-- Name: purchase purchase_id_location_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.purchase
    ADD CONSTRAINT purchase_id_location_fkey FOREIGN KEY (id_location) REFERENCES lbaw2366.location(id);


--
-- Name: purchase purchase_id_user_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.purchase
    ADD CONSTRAINT purchase_id_user_fkey FOREIGN KEY (id_user) REFERENCES lbaw2366."user"(id) ON DELETE SET NULL;


--
-- Name: review review_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.review
    ADD CONSTRAINT review_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id);


--
-- Name: review review_id_user_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.review
    ADD CONSTRAINT review_id_user_fkey FOREIGN KEY (id_user) REFERENCES lbaw2366."user"(id) ON DELETE SET NULL;


--
-- Name: shirt shirt_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.shirt
    ADD CONSTRAINT shirt_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id) ON DELETE CASCADE;


--
-- Name: sneakers sneakers_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.sneakers
    ADD CONSTRAINT sneakers_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id) ON DELETE CASCADE;


--
-- Name: tshirt tshirt_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.tshirt
    ADD CONSTRAINT tshirt_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id) ON DELETE CASCADE;


--
-- Name: user user_id_cart_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366."user"
    ADD CONSTRAINT user_id_cart_fkey FOREIGN KEY (id_cart) REFERENCES lbaw2366.cart(id);


--
-- Name: wishlist wishlist_id_item_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.wishlist
    ADD CONSTRAINT wishlist_id_item_fkey FOREIGN KEY (id_item) REFERENCES lbaw2366.item(id);


--
-- Name: wishlist wishlist_id_user_fkey; Type: FK CONSTRAINT; Schema: lbaw2366; Owner: -
--

ALTER TABLE ONLY lbaw2366.wishlist
    ADD CONSTRAINT wishlist_id_user_fkey FOREIGN KEY (id_user) REFERENCES lbaw2366."user"(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

