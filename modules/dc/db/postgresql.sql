CREATE TABLE user
(
  id serial,
  email varchar(318) NOT NULL,
  username varchar(32) NOT NULL,
  "password" varchar(50) NOT NULL,
  logins integer NOT NULL DEFAULT 0,
  last_login integer,
  CONSTRAINT users_id_pkey PRIMARY KEY (id),
  CONSTRAINT users_username_key UNIQUE (username),
  CONSTRAINT users_email_key UNIQUE (email),
  CONSTRAINT users_logins_check CHECK (logins >= 0)
);

CREATE TABLE user_token
(
  id serial,
  user_id integer NOT NULL,
  user_agent varchar(40) NOT NULL,
  token character varying(32) NOT NULL,
  created integer NOT NULL,
  expires integer NOT NULL,
  CONSTRAINT user_tokens_id_pkey PRIMARY KEY (id),
  CONSTRAINT user_tokens_token_key UNIQUE (token)
);

ALTER TABLE user_tokens
  ADD CONSTRAINT user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;