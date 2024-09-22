BEGIN WORK ISOLATION LEVEL SERIALIZABLE;
--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4 (Debian 16.4-1.pgdg120+1)
-- Dumped by pg_dump version 16.4 (Debian 16.4-1.pgdg120+1)

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

ALTER TABLE IF EXISTS ONLY public.team_invitations DROP CONSTRAINT IF EXISTS team_invitations_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.swarms DROP CONSTRAINT IF EXISTS swarms_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.services DROP CONSTRAINT IF EXISTS services_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.services DROP CONSTRAINT IF EXISTS services_swarm_id_foreign;
ALTER TABLE IF EXISTS ONLY public.services DROP CONSTRAINT IF EXISTS services_placement_node_id_foreign;
ALTER TABLE IF EXISTS ONLY public.nodes DROP CONSTRAINT IF EXISTS nodes_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.nodes DROP CONSTRAINT IF EXISTS nodes_swarm_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_tasks DROP CONSTRAINT IF EXISTS node_tasks_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_tasks DROP CONSTRAINT IF EXISTS node_tasks_task_group_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_tasks DROP CONSTRAINT IF EXISTS node_tasks_meta__service_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_tasks DROP CONSTRAINT IF EXISTS node_tasks_meta__deployment_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_task_groups DROP CONSTRAINT IF EXISTS node_task_groups_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_task_groups DROP CONSTRAINT IF EXISTS node_task_groups_swarm_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_task_groups DROP CONSTRAINT IF EXISTS node_task_groups_node_id_foreign;
ALTER TABLE IF EXISTS ONLY public.node_task_groups DROP CONSTRAINT IF EXISTS node_task_groups_invoker_id_foreign;
ALTER TABLE IF EXISTS ONLY public.networks DROP CONSTRAINT IF EXISTS networks_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.networks DROP CONSTRAINT IF EXISTS networks_swarm_id_foreign;
ALTER TABLE IF EXISTS ONLY public.deployments DROP CONSTRAINT IF EXISTS deployments_team_id_foreign;
ALTER TABLE IF EXISTS ONLY public.deployments DROP CONSTRAINT IF EXISTS deployments_service_id_foreign;
DROP INDEX IF EXISTS public.transactions_paddle_subscription_id_index;
DROP INDEX IF EXISTS public.transactions_billable_type_billable_id_index;
DROP INDEX IF EXISTS public.teams_user_id_index;
DROP INDEX IF EXISTS public.subscriptions_billable_type_billable_id_index;
DROP INDEX IF EXISTS public.sessions_user_id_index;
DROP INDEX IF EXISTS public.sessions_last_activity_index;
DROP INDEX IF EXISTS public.personal_access_tokens_tokenable_type_tokenable_id_index;
DROP INDEX IF EXISTS public.jobs_queue_index;
DROP INDEX IF EXISTS public.customers_billable_type_billable_id_index;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_email_unique;
ALTER TABLE IF EXISTS ONLY public.transactions DROP CONSTRAINT IF EXISTS transactions_pkey;
ALTER TABLE IF EXISTS ONLY public.transactions DROP CONSTRAINT IF EXISTS transactions_paddle_id_unique;
ALTER TABLE IF EXISTS ONLY public.teams DROP CONSTRAINT IF EXISTS teams_pkey;
ALTER TABLE IF EXISTS ONLY public.team_user DROP CONSTRAINT IF EXISTS team_user_team_id_user_id_unique;
ALTER TABLE IF EXISTS ONLY public.team_user DROP CONSTRAINT IF EXISTS team_user_pkey;
ALTER TABLE IF EXISTS ONLY public.team_invitations DROP CONSTRAINT IF EXISTS team_invitations_team_id_email_unique;
ALTER TABLE IF EXISTS ONLY public.team_invitations DROP CONSTRAINT IF EXISTS team_invitations_pkey;
ALTER TABLE IF EXISTS ONLY public.swarms DROP CONSTRAINT IF EXISTS swarms_pkey;
ALTER TABLE IF EXISTS ONLY public.subscriptions DROP CONSTRAINT IF EXISTS subscriptions_pkey;
ALTER TABLE IF EXISTS ONLY public.subscriptions DROP CONSTRAINT IF EXISTS subscriptions_paddle_id_unique;
ALTER TABLE IF EXISTS ONLY public.subscription_items DROP CONSTRAINT IF EXISTS subscription_items_subscription_id_price_id_unique;
ALTER TABLE IF EXISTS ONLY public.subscription_items DROP CONSTRAINT IF EXISTS subscription_items_pkey;
ALTER TABLE IF EXISTS ONLY public.sessions DROP CONSTRAINT IF EXISTS sessions_pkey;
ALTER TABLE IF EXISTS ONLY public.services DROP CONSTRAINT IF EXISTS services_pkey;
ALTER TABLE IF EXISTS ONLY public.personal_access_tokens DROP CONSTRAINT IF EXISTS personal_access_tokens_token_unique;
ALTER TABLE IF EXISTS ONLY public.personal_access_tokens DROP CONSTRAINT IF EXISTS personal_access_tokens_pkey;
ALTER TABLE IF EXISTS ONLY public.password_reset_tokens DROP CONSTRAINT IF EXISTS password_reset_tokens_pkey;
ALTER TABLE IF EXISTS ONLY public.nodes DROP CONSTRAINT IF EXISTS nodes_pkey;
ALTER TABLE IF EXISTS ONLY public.nodes DROP CONSTRAINT IF EXISTS nodes_agent_token_unique;
ALTER TABLE IF EXISTS ONLY public.node_tasks DROP CONSTRAINT IF EXISTS node_tasks_pkey;
ALTER TABLE IF EXISTS ONLY public.node_task_groups DROP CONSTRAINT IF EXISTS node_task_groups_pkey;
ALTER TABLE IF EXISTS ONLY public.networks DROP CONSTRAINT IF EXISTS networks_pkey;
ALTER TABLE IF EXISTS ONLY public.migrations DROP CONSTRAINT IF EXISTS migrations_pkey;
ALTER TABLE IF EXISTS ONLY public.jobs DROP CONSTRAINT IF EXISTS jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.job_batches DROP CONSTRAINT IF EXISTS job_batches_pkey;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_uuid_unique;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.deployments DROP CONSTRAINT IF EXISTS deployments_pkey;
ALTER TABLE IF EXISTS ONLY public.customers DROP CONSTRAINT IF EXISTS customers_pkey;
ALTER TABLE IF EXISTS ONLY public.customers DROP CONSTRAINT IF EXISTS customers_paddle_id_unique;
ALTER TABLE IF EXISTS ONLY public.cache DROP CONSTRAINT IF EXISTS cache_pkey;
ALTER TABLE IF EXISTS ONLY public.cache_locks DROP CONSTRAINT IF EXISTS cache_locks_pkey;
ALTER TABLE IF EXISTS ONLY public.agent_releases DROP CONSTRAINT IF EXISTS agent_releases_pkey;
ALTER TABLE IF EXISTS public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.transactions ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.teams ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.team_user ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.team_invitations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.swarms ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.subscriptions ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.subscription_items ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.services ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.personal_access_tokens ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.nodes ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.node_tasks ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.node_task_groups ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.networks ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.migrations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.failed_jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.deployments ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.customers ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.agent_releases ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE IF EXISTS public.users_id_seq;
DROP TABLE IF EXISTS public.users;
DROP SEQUENCE IF EXISTS public.transactions_id_seq;
DROP TABLE IF EXISTS public.transactions;
DROP SEQUENCE IF EXISTS public.teams_id_seq;
DROP TABLE IF EXISTS public.teams;
DROP SEQUENCE IF EXISTS public.team_user_id_seq;
DROP TABLE IF EXISTS public.team_user;
DROP SEQUENCE IF EXISTS public.team_invitations_id_seq;
DROP TABLE IF EXISTS public.team_invitations;
DROP SEQUENCE IF EXISTS public.swarms_id_seq;
DROP TABLE IF EXISTS public.swarms;
DROP SEQUENCE IF EXISTS public.subscriptions_id_seq;
DROP TABLE IF EXISTS public.subscriptions;
DROP SEQUENCE IF EXISTS public.subscription_items_id_seq;
DROP TABLE IF EXISTS public.subscription_items;
DROP TABLE IF EXISTS public.sessions;
DROP SEQUENCE IF EXISTS public.services_id_seq;
DROP TABLE IF EXISTS public.services;
DROP SEQUENCE IF EXISTS public.personal_access_tokens_id_seq;
DROP TABLE IF EXISTS public.personal_access_tokens;
DROP TABLE IF EXISTS public.password_reset_tokens;
DROP SEQUENCE IF EXISTS public.nodes_id_seq;
DROP TABLE IF EXISTS public.nodes;
DROP SEQUENCE IF EXISTS public.node_tasks_id_seq;
DROP TABLE IF EXISTS public.node_tasks;
DROP SEQUENCE IF EXISTS public.node_task_groups_id_seq;
DROP TABLE IF EXISTS public.node_task_groups;
DROP SEQUENCE IF EXISTS public.networks_id_seq;
DROP TABLE IF EXISTS public.networks;
DROP SEQUENCE IF EXISTS public.migrations_id_seq;
DROP TABLE IF EXISTS public.migrations;
DROP SEQUENCE IF EXISTS public.jobs_id_seq;
DROP TABLE IF EXISTS public.jobs;
DROP TABLE IF EXISTS public.job_batches;
DROP SEQUENCE IF EXISTS public.failed_jobs_id_seq;
DROP TABLE IF EXISTS public.failed_jobs;
DROP SEQUENCE IF EXISTS public.deployments_id_seq;
DROP TABLE IF EXISTS public.deployments;
DROP SEQUENCE IF EXISTS public.customers_id_seq;
DROP TABLE IF EXISTS public.customers;
DROP TABLE IF EXISTS public.cache_locks;
DROP TABLE IF EXISTS public.cache;
DROP SEQUENCE IF EXISTS public.agent_releases_id_seq;
DROP TABLE IF EXISTS public.agent_releases;
SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: agent_releases; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.agent_releases (
    id bigint NOT NULL,
    tag_name character varying(255) NOT NULL,
    os character varying(255) NOT NULL,
    arch character varying(255) NOT NULL,
    download_url character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: agent_releases_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.agent_releases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: agent_releases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.agent_releases_id_seq OWNED BY public.agent_releases.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: customers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.customers (
    id bigint NOT NULL,
    billable_type character varying(255) NOT NULL,
    billable_id bigint NOT NULL,
    paddle_id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    trial_ends_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: customers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.customers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: customers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.customers_id_seq OWNED BY public.customers.id;


--
-- Name: deployments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.deployments (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    service_id bigint NOT NULL,
    data jsonb NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: deployments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.deployments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: deployments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.deployments_id_seq OWNED BY public.deployments.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: networks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.networks (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    swarm_id bigint NOT NULL,
    docker_name character varying(255) NOT NULL
);


--
-- Name: networks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.networks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: networks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.networks_id_seq OWNED BY public.networks.id;


--
-- Name: node_task_groups; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.node_task_groups (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    swarm_id bigint NOT NULL,
    node_id bigint,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    invoker_id bigint NOT NULL,
    started_at timestamp(0) without time zone,
    ended_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    type smallint NOT NULL,
    CONSTRAINT node_task_groups_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'running'::character varying, 'completed'::character varying, 'failed'::character varying, 'canceled'::character varying])::text[])))
);


--
-- Name: node_task_groups_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.node_task_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: node_task_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.node_task_groups_id_seq OWNED BY public.node_task_groups.id;


--
-- Name: node_tasks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.node_tasks (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    task_group_id bigint NOT NULL,
    type smallint NOT NULL,
    meta jsonb NOT NULL,
    payload jsonb NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    started_at timestamp(0) without time zone,
    ended_at timestamp(0) without time zone,
    result jsonb,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    meta__deployment_id bigint GENERATED ALWAYS AS (((meta -> 'deploymentId'::text))::integer) STORED,
    meta__service_id bigint GENERATED ALWAYS AS (((meta -> 'serviceId'::text))::integer) STORED,
    meta__docker_name character varying(255) GENERATED ALWAYS AS ((meta ->> 'dockerName'::text)) STORED,
    CONSTRAINT node_tasks_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'running'::character varying, 'completed'::character varying, 'failed'::character varying, 'canceled'::character varying])::text[])))
);


--
-- Name: node_tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.node_tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: node_tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.node_tasks_id_seq OWNED BY public.node_tasks.id;


--
-- Name: nodes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.nodes (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    agent_token character varying(255) NOT NULL,
    last_seen_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    data jsonb,
    swarm_id bigint
);


--
-- Name: nodes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.nodes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: nodes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.nodes_id_seq OWNED BY public.nodes.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: services; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.services (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    docker_name character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    swarm_id bigint NOT NULL,
    deleted_at timestamp(0) without time zone,
    placement_node_id bigint,
    slug character varying(255) NOT NULL
);


--
-- Name: services_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: services_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.services_id_seq OWNED BY public.services.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: subscription_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.subscription_items (
    id bigint NOT NULL,
    subscription_id bigint NOT NULL,
    product_id character varying(255) NOT NULL,
    price_id character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    quantity integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: subscription_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.subscription_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subscription_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.subscription_items_id_seq OWNED BY public.subscription_items.id;


--
-- Name: subscriptions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.subscriptions (
    id bigint NOT NULL,
    billable_type character varying(255) NOT NULL,
    billable_id bigint NOT NULL,
    type character varying(255) NOT NULL,
    paddle_id character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    trial_ends_at timestamp(0) without time zone,
    paused_at timestamp(0) without time zone,
    ends_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: subscriptions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.subscriptions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subscriptions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.subscriptions_id_seq OWNED BY public.subscriptions.id;


--
-- Name: swarms; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.swarms (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    data jsonb NOT NULL
);


--
-- Name: swarms_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.swarms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: swarms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.swarms_id_seq OWNED BY public.swarms.id;


--
-- Name: team_invitations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.team_invitations (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    email character varying(255) NOT NULL,
    role character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: team_invitations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.team_invitations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: team_invitations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.team_invitations_id_seq OWNED BY public.team_invitations.id;


--
-- Name: team_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.team_user (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    user_id bigint NOT NULL,
    role character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: team_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.team_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: team_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.team_user_id_seq OWNED BY public.team_user.id;


--
-- Name: teams; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.teams (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    personal_team boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    billing_name character varying(255) NOT NULL,
    billing_email character varying(255) NOT NULL,
    activating_subscription boolean DEFAULT false NOT NULL
);


--
-- Name: teams_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.teams_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: teams_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.teams_id_seq OWNED BY public.teams.id;


--
-- Name: transactions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.transactions (
    id bigint NOT NULL,
    billable_type character varying(255) NOT NULL,
    billable_id bigint NOT NULL,
    paddle_id character varying(255) NOT NULL,
    paddle_subscription_id character varying(255),
    invoice_number character varying(255),
    status character varying(255) NOT NULL,
    total character varying(255) NOT NULL,
    tax character varying(255) NOT NULL,
    currency character varying(3) NOT NULL,
    billed_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.transactions_id_seq OWNED BY public.transactions.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    current_team_id bigint,
    profile_photo_path character varying(2048),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    two_factor_secret text,
    two_factor_recovery_codes text,
    two_factor_confirmed_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: agent_releases id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agent_releases ALTER COLUMN id SET DEFAULT nextval('public.agent_releases_id_seq'::regclass);


--
-- Name: customers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.customers ALTER COLUMN id SET DEFAULT nextval('public.customers_id_seq'::regclass);


--
-- Name: deployments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.deployments ALTER COLUMN id SET DEFAULT nextval('public.deployments_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: networks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.networks ALTER COLUMN id SET DEFAULT nextval('public.networks_id_seq'::regclass);


--
-- Name: node_task_groups id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_task_groups ALTER COLUMN id SET DEFAULT nextval('public.node_task_groups_id_seq'::regclass);


--
-- Name: node_tasks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_tasks ALTER COLUMN id SET DEFAULT nextval('public.node_tasks_id_seq'::regclass);


--
-- Name: nodes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nodes ALTER COLUMN id SET DEFAULT nextval('public.nodes_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: services id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.services ALTER COLUMN id SET DEFAULT nextval('public.services_id_seq'::regclass);


--
-- Name: subscription_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_items ALTER COLUMN id SET DEFAULT nextval('public.subscription_items_id_seq'::regclass);


--
-- Name: subscriptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscriptions ALTER COLUMN id SET DEFAULT nextval('public.subscriptions_id_seq'::regclass);


--
-- Name: swarms id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.swarms ALTER COLUMN id SET DEFAULT nextval('public.swarms_id_seq'::regclass);


--
-- Name: team_invitations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.team_invitations ALTER COLUMN id SET DEFAULT nextval('public.team_invitations_id_seq'::regclass);


--
-- Name: team_user id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.team_user ALTER COLUMN id SET DEFAULT nextval('public.team_user_id_seq'::regclass);


--
-- Name: teams id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teams ALTER COLUMN id SET DEFAULT nextval('public.teams_id_seq'::regclass);


--
-- Name: transactions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions ALTER COLUMN id SET DEFAULT nextval('public.transactions_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: agent_releases; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.agent_releases (id, tag_name, os, arch, download_url, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: customers; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.customers (id, billable_type, billable_id, paddle_id, name, email, trial_ends_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: deployments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.deployments (id, team_id, service_id, data, created_at, updated_at) FROM stdin;
1	1	1	{"processes": [{"name": "svc", "caddy": [], "ports": [{"targetPort": 80, "publishedPort": 80}, {"targetPort": 443, "publishedPort": 443}, {"targetPort": 2019, "publishedPort": 2019}], "backups": [], "command": "sh /start.sh", "envVars": [{"name": "CADDY_ADMIN", "value": "0.0.0.0:2019"}], "fastCgi": null, "volumes": [{"id": "volume-a1b2c3d4e5f", "name": "data", "path": "/data", "dockerName": "svc_1_caddy_svc_data", "backupSchedule": null}, {"id": "volume-g6h7i8j9k1", "name": "config", "path": "/config", "dockerName": "svc_1_caddy_svc_config", "backupSchedule": null}], "workers": [], "replicas": 1, "dockerName": "svc_1_caddy_svc", "launchMode": "daemon", "secretVars": [], "configFiles": [{"path": "/ptah/caddy/tls/.keep", "content": "# Keep this file", "dockerName": "svc_1_caddy_svc_dpl_1_cfg_ptah_caddy_tls_keep"}, {"path": "/start.sh", "content": "# This is the entrypoint for Caddy.\\n# By default, it starts with a blank Caddyfile with a standard \\"Hello from Caddy\\" page.\\n\\nif [ -f \\"/config/caddy/autosave.json\\" ]; then\\n    echo \\"Starting with autosave.json\\"\\n\\n    caddy run --config \\"/config/caddy/autosave.json\\"\\nelse\\n    echo \\"Starting with the default Caddyfile\\"\\n\\n    caddy run --config \\"/etc/caddy/Caddyfile\\"\\nfi", "dockerName": "svc_1_caddy_svc_dpl_1_cfg_start_sh"}], "dockerImage": "caddy:2.8-alpine", "healthcheck": {"command": null, "retries": 3, "timeout": 5, "interval": 10, "startPeriod": 30, "startInterval": 5}, "secretFiles": [], "backupVolume": {"id": "volume-l2m3n4o5p6", "name": "backups", "path": "/ptah/backups", "dockerName": "svc_1_caddy_svc_ptah_backups"}, "rewriteRules": [], "redirectRules": [], "releaseCommand": {"command": null, "dockerName": null}, "placementNodeId": 1, "dockerRegistryId": null}], "networkName": "ptah_net", "internalDomain": "caddy.ptah.local"}	2024-09-01 00:00:00	2024-09-01 00:00:00
2	1	2	{"processes": [{"name": "pg", "caddy": [], "ports": [], "backups": [], "command": null, "envVars": [{"name": "POSTGRESQL_USERNAME", "value": "ptah_sh"}, {"name": "POSTGRESQL_PASSWORD", "value": "ptah_sh"}, {"name": "POSTGRESQL_DATABASE", "value": "ptah_sh"}], "fastCgi": null, "volumes": [{"id": "volume-q7r8s9t0a1", "name": "data", "path": "/bitnami/postgresql", "dockerName": "svc_2_ptah_pg_data", "backupSchedule": null}], "workers": [], "replicas": 1, "dockerName": "svc_2_ptah_pg", "launchMode": "daemon", "secretVars": [], "configFiles": [], "dockerImage": "bitnami/postgresql:16", "healthcheck": {"command": null, "retries": 3, "timeout": 5, "interval": 10, "startPeriod": 30, "startInterval": 5}, "secretFiles": [], "backupVolume": {"id": "volume-h7i8j9k0l1", "name": "backups", "path": "/ptah/backups", "dockerName": "svc_2_ptah_pg_ptah_backups"}, "rewriteRules": [], "redirectRules": [], "releaseCommand": {"command": null, "dockerName": null}, "placementNodeId": 1, "dockerRegistryId": null}, {"name": "pool", "caddy": [], "ports": [], "backups": [], "command": null, "envVars": [{"name": "PGBOUNCER_POOL_MODE", "value": "session"}, {"name": "POSTGRESQL_USERNAME", "value": "ptah_sh"}, {"name": "POSTGRESQL_PASSWORD", "value": "ptah_sh"}, {"name": "POSTGRESQL_DATABASE", "value": "ptah_sh"}, {"name": "POSTGRESQL_HOST", "value": "pg.server.ptah.local"}, {"name": "PGBOUNCER_PORT", "value": "5432"}, {"name": "PGBOUNCER_DATABASE", "value": "ptah_sh"}], "fastCgi": null, "volumes": [], "workers": [], "replicas": 1, "dockerName": "svc_2_ptah_pool", "launchMode": "daemon", "secretVars": [], "configFiles": [], "dockerImage": "bitnami/pgbouncer", "healthcheck": {"command": null, "retries": 3, "timeout": 5, "interval": 10, "startPeriod": 30, "startInterval": 5}, "secretFiles": [], "backupVolume": null, "rewriteRules": [], "redirectRules": [], "releaseCommand": {"command": null, "dockerName": null}, "placementNodeId": null, "dockerRegistryId": null}, {"name": "app", "caddy": [{"id": "caddy-b2c3d4e5f6", "path": "/*", "domain": "ptah.localhost", "targetPort": 8080, "publishedPort": 80, "targetProtocol": "http"}], "ports": [], "backups": [], "command": null, "envVars": [{"name": "APP_ENV", "value": "production"}, {"name": "APP_KEY", "value": "base64:APP_KEY"}, {"name": "BCRYPT_ROUNDS", "value": "12"}, {"name": "DB_CONNECTION", "value": "pgsql"}, {"name": "DB_HOST", "value": "pool.server.ptah.local"}, {"name": "DB_DATABASE", "value": "ptah_sh"}, {"name": "DB_USERNAME", "value": "ptah_sh"}, {"name": "DB_PASSWORD", "value": "ptah_sh"}, {"name": "LOG_CHANNEL", "value": "errorlog"}, {"name": "APP_URL", "value": "localhost"}, {"name": "BILLING_ENABLED", "value": "false"}], "fastCgi": null, "volumes": [], "workers": [{"name": "schedule", "command": "php artisan config:cache && php artisan schedule:work", "replicas": 1, "dockerName": "svc_2_ptah_app_wkr_schedule"}, {"name": "queue", "command": "php artisan config:cache && php artisan queue:work", "replicas": 1, "dockerName": "svc_2_ptah_app_wkr_queue"}], "replicas": 2, "dockerName": "svc_2_ptah_app", "launchMode": "daemon", "secretVars": [], "configFiles": [], "dockerImage": "ghcr.io/ptah-sh/ptah-server:latest", "healthcheck": {"command": null, "retries": 3, "timeout": 5, "interval": 10, "startPeriod": 30, "startInterval": 5}, "secretFiles": [], "backupVolume": null, "rewriteRules": [], "redirectRules": [], "releaseCommand": {"command": "php artisan config:cache && php artisan migrate --no-interaction --verbose --ansi --force", "dockerName": "svc_2_dpl_2_release_command"}, "placementNodeId": null, "dockerRegistryId": null}], "networkName": "ptah_net", "internalDomain": "server.ptah.local"}	2024-09-01 00:00:00	2024-09-01 00:00:00
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2019_05_03_000001_create_customers_table	1
5	2019_05_03_000002_create_subscriptions_table	1
6	2019_05_03_000003_create_subscription_items_table	1
7	2019_05_03_000004_create_transactions_table	1
8	2024_06_19_074636_add_two_factor_columns_to_users_table	1
9	2024_06_19_074645_create_personal_access_tokens_table	1
10	2024_06_19_074645_create_teams_table	1
11	2024_06_19_074646_create_team_user_table	1
12	2024_06_19_074647_create_team_invitations_table	1
13	2024_06_19_084206_create_swarms_table	1
14	2024_06_19_084441_create_nodes_table	1
15	2024_06_19_184050_alter_nodes_add_data_column	1
16	2024_06_19_202417_create_node_task_groups_table	1
17	2024_06_19_204239_create_node_tasks_table	1
18	2024_06_20_142704_alter_nodes_add_swarm_id_column	1
19	2024_06_21_081415_create_networks_table	1
20	2024_06_21_220222_create_services_table	1
21	2024_06_21_220426_create_deployments_table	1
22	2024_06_22_111455_alter_networks_add_swarm_id_column	1
23	2024_06_22_154415_alter_services_add_swarm_id_column	1
24	2024_06_22_155805_alter_node_task_groups_add_type_column	1
25	2024_06_23_164312_alter_networks_add_docker_name_column	1
26	2024_06_25_094334_alter_services_networks_swarms_drop_docker_id_column	1
27	2024_06_26_121016_alter_deployments_drop_task_group_id_column	1
28	2024_06_26_122528_alter_task_groups_add_virtual_deployment_id_column	1
29	2024_06_26_171205_alter_services_add_deleted_at_column	1
30	2024_06_26_172949_alter_services_change_docker_name_nullable	1
31	2024_06_29_100400_create_agent_releases_table	1
32	2024_07_01_121412_alter_swarms_add_data_column	1
33	2024_07_03_115106_alter_node_tasks_add_meta__service_id_column	1
34	2024_07_04_195451_alter_node_tasks_add_meta__docker_name_column	1
35	2024_07_10_204515_update_deployments_add_redirect_rules	1
36	2024_07_11_094004_update_deployments_add_caddy_id	1
37	2024_07_11_204234_update_swarms_add_s3_storages	1
38	2024_07_14_115507_update_deployments_add_volume_backups	1
39	2024_07_16_141738_update_deployments_add_backups_to_processes	1
40	2024_07_16_183412_update_deployments_set_registry_id	1
41	2024_07_17_194226_alter_teams_add_billing_columns	1
42	2024_07_25_134246_alter_services_add_placement_node_id_column	1
43	2024_07_29_191755_update_nodes_set_role	1
44	2024_08_05_083921_alter_teams_add_activating_subscription_column	1
45	2024_08_20_202958_alter_swarms_remove_name_column	1
46	2024_08_25_130825_update_node_data_address	1
47	2024_08_25_233000_add_slug_to_services_table	1
48	2024_09_06_091348_alter_deployments_add_rewrite_rules_data	1
49	2024_09_08_183203_alter_deployments_move_placement_node_id_to_processes	1
50	2024_09_09_000000_update_processes_structure_in_deployments	1
51	2024_09_21_123609_alter_swarms_update_data_with_fake_encryption_key	1
52	2024_09_22_083731_update_deployments_move_secret_vars_to_process	1
\.


--
-- Data for Name: networks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.networks (id, team_id, name, created_at, updated_at, swarm_id, docker_name) FROM stdin;
1	1	ptah_net	2024-09-01 00:00:00	2024-09-01 00:00:00	1	ptah_net
\.


--
-- Data for Name: node_task_groups; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.node_task_groups (id, team_id, swarm_id, node_id, status, invoker_id, started_at, ended_at, created_at, updated_at, type) FROM stdin;
1	1	1	1	completed	1	2024-09-01 00:00:00	2024-09-01 00:00:00	2024-09-01 00:00:00	2024-09-01 00:00:00	0
2	1	1	1	completed	1	2024-09-01 00:00:00	2024-09-01 00:00:00	2024-09-01 00:00:00	2024-09-01 00:00:00	1
3	1	1	1	completed	1	2024-09-01 00:00:00	2024-09-01 00:00:00	2024-09-01 00:00:00	2024-09-01 00:00:00	1
\.


--
-- Data for Name: node_tasks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.node_tasks (id, team_id, task_group_id, type, meta, payload, status, started_at, ended_at, result, created_at, updated_at) FROM stdin;
13	1	3	14	{"dockerImage": "ghcr.io/ptah-sh/ptah-server:latest"}	{"Image": "ghcr.io/ptah-sh/ptah-server:latest", "PullOptions": {}, "AuthConfigName": ""}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
1	1	1	1	{"swarmId": 1, "forceNewCluster": false}	{"SwarmInitRequest": {"Spec": {"Annotations": {"Name": "default", "Labels": {"sh.ptah.managed": "1", "sh.ptah.swarm.id": "1"}}}, "ListenAddr": "0.0.0.0:2377", "AdvertiseAddr": "192.168.1.1", "ForceNewCluster": false}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
2	1	1	7	[]	{"NodeSpec": {"Name": "default", "Role": "manager", "Labels": {"sh.ptah.managed": "1", "sh.ptah.node.id": "1", "sh.ptah.node.name": "default"}, "Availability": "active"}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
3	1	1	0	{"name": "ptah_net", "networkId": 1}	{"NetworkName": "ptah_net", "NetworkCreateOptions": {"Scope": "swarm", "Driver": "overlay", "Labels": {"sh.ptah.managed": "1", "sh.ptah.network.id": "1"}, "Attachable": true}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
6	1	2	14	{"dockerImage": "caddy:2.8-alpine"}	{"Image": "caddy:2.8-alpine", "PullOptions": {}, "AuthConfigName": ""}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
8	1	2	5	{"deploymentId": 1}	{"caddy": {"apps": {"http": {"servers": {}}}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
11	1	3	14	{"dockerImage": "bitnami/pgbouncer"}	{"Image": "bitnami/pgbouncer", "PullOptions": {}, "AuthConfigName": ""}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
9	1	3	14	{"dockerImage": "bitnami/postgresql:16"}	{"Image": "bitnami/postgresql:16", "PullOptions": {}, "AuthConfigName": ""}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
12	1	3	21	{"serviceId": 2, "dockerName": "svc_2_ptah_pool", "serviceName": "ptah", "deploymentId": 2}	{"SecretVars": {}, "AuthConfigName": "", "ReleaseCommand": {"Command": "", "ConfigName": "", "ConfigLabels": {}}, "SwarmServiceSpec": {"Mode": {"Replicated": {"Replicas": 1}}, "Name": "svc_2_ptah_pool", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "EndpointSpec": {"Ports": []}, "TaskTemplate": {"Networks": [{"Target": "ptah_net", "Aliases": ["pool.server.ptah.local"]}], "ContainerSpec": {"Env": ["PGBOUNCER_POOL_MODE=session", "POSTGRESQL_USERNAME=ptah_sh", "POSTGRESQL_PASSWORD=ptah_sh", "POSTGRESQL_DATABASE=ptah_sh", "POSTGRESQL_HOST=pg.server.ptah.local", "PGBOUNCER_PORT=5432", "PGBOUNCER_DATABASE=ptah_sh", "PTAH_HOSTNAME=pool.server.ptah.local"], "Args": null, "Hosts": ["pool.server.ptah.local"], "Image": "bitnami/pgbouncer", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "Mounts": [], "Command": null, "Configs": [], "Secrets": [], "Hostname": "dpl-2.pool.server.ptah.local", "Placement": [], "HealthCheck": null}}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
4	1	2	2	{"path": "/ptah/caddy/tls/.keep", "processName": "svc_1_caddy_svc", "deploymentId": 1}	{"SwarmConfigSpec": {"Data": "IyBLZWVwIHRoaXMgZmlsZQ==", "Name": "svc_1_caddy_svc_dpl_1_cfg_ptah_caddy_tls_keep", "Labels": {"sh.ptah.kind": "config", "sh.ptah.managed": "1", "sh.ptah.service.id": "1", "sh.ptah.service.slug": "caddy_happy_ptah_1", "sh.ptah.deployment.id": "1"}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
16	1	3	21	{"serviceId": 2, "dockerName": "svc_2_ptah_app_wkr_queue", "serviceName": "ptah", "deploymentId": 2}	{"SecretVars": {}, "AuthConfigName": "", "ReleaseCommand": {}, "SwarmServiceSpec": {"Mode": {"Replicated": {"Replicas": 1}}, "Name": "svc_2_ptah_app_wkr_queue", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "EndpointSpec": {"Ports": []}, "TaskTemplate": {"Networks": [{"Target": "ptah_net", "Aliases": ["queue.app.server.ptah.local"]}], "ContainerSpec": {"Env": ["APP_ENV=production", "APP_KEY=base64:APP_KEY", "BCRYPT_ROUNDS=12", "DB_CONNECTION=pgsql", "DB_HOST=pool.server.ptah.local", "DB_DATABASE=ptah_sh", "DB_USERNAME=ptah_sh", "DB_PASSWORD=ptah_sh", "LOG_CHANNEL=errorlog", "APP_URL=localhost", "BILLING_ENABLED=false"], "Args": ["php artisan config:cache && php artisan queue:work"], "Hosts": ["queue.app.server.ptah.local"], "Image": "ghcr.io/ptah-sh/ptah-server:latest", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "Mounts": [], "Command": ["sh", "-c"], "Configs": [], "Secrets": [], "Hostname": "dpl-2.queue.app.server.ptah.local", "Placement": [], "HealthCheck": {"Test": ["NONE"]}}}, "UpdateConfig": {"Parallelism": 1}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
15	1	3	21	{"serviceId": 2, "dockerName": "svc_2_ptah_app_wkr_schedule", "serviceName": "ptah", "deploymentId": 2}	{"SecretVars": {}, "AuthConfigName": "", "ReleaseCommand": {}, "SwarmServiceSpec": {"Mode": {"Replicated": {"Replicas": 1}}, "Name": "svc_2_ptah_app_wkr_schedule", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "EndpointSpec": {"Ports": []}, "TaskTemplate": {"Networks": [{"Target": "ptah_net", "Aliases": ["schedule.app.server.ptah.local"]}], "ContainerSpec": {"Env": ["APP_ENV=production", "APP_KEY=base64:APP_KEY", "BCRYPT_ROUNDS=12", "DB_CONNECTION=pgsql", "DB_HOST=pool.server.ptah.local", "DB_DATABASE=ptah_sh", "DB_USERNAME=ptah_sh", "DB_PASSWORD=ptah_sh", "LOG_CHANNEL=errorlog", "APP_URL=localhost", "BILLING_ENABLED=false"], "Args": ["php artisan config:cache && php artisan schedule:work"], "Hosts": ["schedule.app.server.ptah.local"], "Image": "ghcr.io/ptah-sh/ptah-server:latest", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "Mounts": [], "Command": ["sh", "-c"], "Configs": [], "Secrets": [], "Hostname": "dpl-2.schedule.app.server.ptah.local", "Placement": [], "HealthCheck": {"Test": ["NONE"]}}}, "UpdateConfig": {"Parallelism": 1}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
17	1	3	5	{"deploymentId": 2}	{"caddy": {"apps": {"http": {"servers": {"listen_80": {"listen": ["0.0.0.0:80"], "routes": [{"match": [{"host": ["ptah.localhost"], "path": ["/*"]}], "handle": [{"handler": "reverse_proxy", "headers": {"request": {"set": {"X-Forwarded-Host": ["ptah.localhost"], "X-Forwarded-Port": ["80"], "X-Forwarded-Proto": ["http"], "X-Forwarded-Schema": ["http"]}}, "response": {"set": {"X-Powered-By": ["https://ptah.sh"], "X-Ptah-Rule-Id": ["caddy-b2c3d4e5f6"]}}}, "transport": {"protocol": "http"}, "upstreams": [{"dial": "app.server.ptah.local:8080"}]}]}, {"match": [{"host": ["*"], "path": ["/*"]}], "handle": [{"body": "<!DOCTYPE html>\\n<html>\\n<head>\\n    <title>404 Not Found</title>\\n    <style>\\n        body {\\n            text-align: center;\\n            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;\\n            height: 100vh;\\n            display: flex;\\n            flex-direction: column;\\n            align-items: center;\\n            justify-content: space-evenly;\\n        }\\n\\n        .home-btn {\\n            display: inline-block;\\n            background-color: #007bff;\\n            color: #fff;\\n            padding: 10px 20px;\\n            border-radius: 5px;\\n            text-decoration: none;\\n            margin-top: 20px;\\n        }\\n\\n        .home-btn:hover {\\n            background-color: #0069d9;\\n        }\\n\\n        .powered-by {\\n            margin-top: 20px;\\n            color: #999;\\n        }\\n    </style>\\n\\n    <script>\\n        window.addEventListener('DOMContentLoaded', () => {\\n            if (window.location.pathname === \\"/\\") {\\n                document.getElementById('goHome').remove();\\n            }\\n        })\\n    </script>\\n</head>\\n<body>\\n    <div>\\n        <h1>404 Not Found</h1>\\n        <p>The requested URL was not found on this server.</p>\\n\\n        <p>Please contact the server administrator if you believe this is an error.</p>\\n\\n        <a id=\\"goHome\\" class=\\"home-btn\\" href=\\"/\\">Return to the Home Page</a>\\n    </div>\\n\\n    <p class=\\"powered-by\\"><small>Powered by <a href=\\"https://ptah.sh\\">ptah.sh</a></small></p>\\n</body>\\n</html>", "handler": "static_response", "headers": {"Content-Type": ["text/html; charset=utf-8"], "X-Powered-By": ["https://ptah.sh"]}, "status_code": "404"}]}]}}}}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
5	1	2	2	{"path": "/start.sh", "processName": "svc_1_caddy_svc", "deploymentId": 1}	{"SwarmConfigSpec": {"Data": "IyBUaGlzIGlzIHRoZSBlbnRyeXBvaW50IGZvciBDYWRkeS4KIyBCeSBkZWZhdWx0LCBpdCBzdGFydHMgd2l0aCBhIGJsYW5rIENhZGR5ZmlsZSB3aXRoIGEgc3RhbmRhcmQgIkhlbGxvIGZyb20gQ2FkZHkiIHBhZ2UuCgppZiBbIC1mICIvY29uZmlnL2NhZGR5L2F1dG9zYXZlLmpzb24iIF07IHRoZW4KICAgIGVjaG8gIlN0YXJ0aW5nIHdpdGggYXV0b3NhdmUuanNvbiIKCiAgICBjYWRkeSBydW4gLS1jb25maWcgIi9jb25maWcvY2FkZHkvYXV0b3NhdmUuanNvbiIKZWxzZQogICAgZWNobyAiU3RhcnRpbmcgd2l0aCB0aGUgZGVmYXVsdCBDYWRkeWZpbGUiCgogICAgY2FkZHkgcnVuIC0tY29uZmlnICIvZXRjL2NhZGR5L0NhZGR5ZmlsZSIKZmk=", "Name": "svc_1_caddy_svc_dpl_1_cfg_start_sh", "Labels": {"sh.ptah.kind": "config", "sh.ptah.managed": "1", "sh.ptah.service.id": "1", "sh.ptah.service.slug": "caddy_happy_ptah_1", "sh.ptah.deployment.id": "1"}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
7	1	2	21	{"serviceId": 1, "dockerName": "svc_1_caddy_svc", "serviceName": "caddy", "deploymentId": 1}	{"SecretVars": {}, "AuthConfigName": "", "ReleaseCommand": {"Command": "", "ConfigName": "", "ConfigLabels": {}}, "SwarmServiceSpec": {"Mode": {"Replicated": {"Replicas": 1}}, "Name": "svc_1_caddy_svc", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "1", "sh.ptah.service.slug": "caddy_happy_ptah_1", "sh.ptah.deployment.id": "1"}, "EndpointSpec": {"Ports": [{"Protocol": "tcp", "TargetPort": 80, "PublishMode": "ingress", "PublishedPort": 80}, {"Protocol": "tcp", "TargetPort": 443, "PublishMode": "ingress", "PublishedPort": 443}, {"Protocol": "tcp", "TargetPort": 2019, "PublishMode": "ingress", "PublishedPort": 2019}]}, "TaskTemplate": {"Networks": [{"Target": "ptah_net", "Aliases": ["svc.caddy.ptah.local"]}], "ContainerSpec": {"Env": ["CADDY_ADMIN=0.0.0.0:2019", "PTAH_HOSTNAME=svc.caddy.ptah.local"], "Args": ["/start.sh"], "Hosts": ["svc.caddy.ptah.local"], "Image": "caddy:2.8-alpine", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "1", "sh.ptah.service.slug": "caddy_happy_ptah_1", "sh.ptah.deployment.id": "1"}, "Mounts": [{"Type": "volume", "Source": "svc_1_caddy_svc_data", "Target": "/data", "VolumeOptions": {"Labels": {"sh.ptah.id": "volume-a1b2c3d4e5f", "sh.ptah.managed": "1", "sh.ptah.service.id": "1", "sh.ptah.service.slug": "caddy_happy_ptah_1", "sh.ptah.deployment.id": "1"}}}, {"Type": "volume", "Source": "svc_1_caddy_svc_config", "Target": "/config", "VolumeOptions": {"Labels": {"sh.ptah.id": "volume-g6h7i8j9k1", "sh.ptah.managed": "1", "sh.ptah.service.id": "1", "sh.ptah.service.slug": "caddy_happy_ptah_1", "sh.ptah.deployment.id": "1"}}}, {"Type": "volume", "Source": "svc_1_caddy_svc_ptah_backups", "Target": "/ptah/backups", "VolumeOptions": {"Labels": {"sh.ptah.id": "volume-l2m3n4o5p6", "sh.ptah.managed": "1", "sh.ptah.service.id": "1", "sh.ptah.service.slug": "caddy_happy_ptah_1", "sh.ptah.deployment.id": "1"}}}], "Command": ["sh"], "Configs": [{"File": {"GID": "0", "UID": "0", "Mode": 511, "Name": "/ptah/caddy/tls/.keep"}, "ConfigName": "svc_1_caddy_svc_dpl_1_cfg_ptah_caddy_tls_keep"}, {"File": {"GID": "0", "UID": "0", "Mode": 511, "Name": "/start.sh"}, "ConfigName": "svc_1_caddy_svc_dpl_1_cfg_start_sh"}], "Secrets": [], "Hostname": "dpl-1.svc.caddy.ptah.local", "Placement": {"Constraints": ["node.labels.sh.ptah.node.id==1"]}, "HealthCheck": null}}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
10	1	3	21	{"serviceId": 2, "dockerName": "svc_2_ptah_pg", "serviceName": "ptah", "deploymentId": 2}	{"SecretVars": {}, "AuthConfigName": "", "ReleaseCommand": {"Command": "", "ConfigName": "", "ConfigLabels": {}}, "SwarmServiceSpec": {"Mode": {"Replicated": {"Replicas": 1}}, "Name": "svc_2_ptah_pg", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "EndpointSpec": {"Ports": []}, "TaskTemplate": {"Networks": [{"Target": "ptah_net", "Aliases": ["pg.server.ptah.local"]}], "ContainerSpec": {"Env": ["POSTGRESQL_USERNAME=ptah_sh", "POSTGRESQL_PASSWORD=ptah_sh", "POSTGRESQL_DATABASE=ptah_sh", "PTAH_HOSTNAME=pg.server.ptah.local"], "Args": null, "Hosts": ["pg.server.ptah.local"], "Image": "bitnami/postgresql:16", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "Mounts": [{"Type": "volume", "Source": "svc_2_ptah_pg_data", "Target": "/bitnami/postgresql", "VolumeOptions": {"Labels": {"sh.ptah.id": "volume-q7r8s9t0a1", "sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}}}, {"Type": "volume", "Source": "svc_2_ptah_pg_ptah_backups", "Target": "/ptah/backups", "VolumeOptions": {"Labels": {"sh.ptah.id": "volume-h7i8j9k0l1", "sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}}}], "Command": null, "Configs": [], "Secrets": [], "Hostname": "dpl-2.pg.server.ptah.local", "Placement": {"Constraints": ["node.labels.sh.ptah.node.id==1"]}, "HealthCheck": null}}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
14	1	3	21	{"serviceId": 2, "dockerName": "svc_2_ptah_app", "serviceName": "ptah", "deploymentId": 2}	{"SecretVars": {}, "AuthConfigName": "", "ReleaseCommand": {"Command": "php artisan config:cache && php artisan migrate --no-interaction --verbose --ansi --force", "ConfigName": "svc_2_dpl_2_release_command", "ConfigLabels": {"sh.ptah.kind": "release-command", "sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}}, "SwarmServiceSpec": {"Mode": {"Replicated": {"Replicas": 2}}, "Name": "svc_2_ptah_app", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "EndpointSpec": {"Ports": []}, "TaskTemplate": {"Networks": [{"Target": "ptah_net", "Aliases": ["app.server.ptah.local"]}], "ContainerSpec": {"Env": ["APP_ENV=production", "APP_KEY=base64:APP_KEY", "BCRYPT_ROUNDS=12", "DB_CONNECTION=pgsql", "DB_HOST=pool.server.ptah.local", "DB_DATABASE=ptah_sh", "DB_USERNAME=ptah_sh", "DB_PASSWORD=ptah_sh", "LOG_CHANNEL=errorlog", "APP_URL=localhost", "BILLING_ENABLED=false", "PTAH_HOSTNAME=app.server.ptah.local"], "Args": null, "Hosts": ["app.server.ptah.local"], "Image": "ghcr.io/ptah-sh/ptah-server:latest", "Labels": {"sh.ptah.managed": "1", "sh.ptah.service.id": "2", "sh.ptah.service.slug": "ptah_happy_ptah_2", "sh.ptah.deployment.id": "2"}, "Mounts": [], "Command": null, "Configs": [], "Secrets": [], "Hostname": "dpl-2.app.server.ptah.local", "Placement": [], "HealthCheck": null}}}}	completed	2024-09-01 00:00:00	2024-09-01 00:00:00	[]	2024-09-01 00:00:00	2024-09-01 00:00:00
\.


--
-- Data for Name: nodes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.nodes (id, team_id, name, agent_token, last_seen_at, created_at, updated_at, data, swarm_id) FROM stdin;
1	1	default	fake_agent_token	\N	2024-09-01 00:00:00	2024-09-01 00:00:00	\N	1
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: services; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.services (id, team_id, name, docker_name, created_at, updated_at, swarm_id, deleted_at, placement_node_id, slug) FROM stdin;
1	1	caddy	svc_1_caddy	2024-09-01 00:00:00	2024-09-01 00:00:00	1	\N	\N	caddy_happy_ptah_1
2	1	ptah	svc_2_ptah	2024-09-01 00:00:00	2024-09-01 00:00:00	1	\N	\N	ptah_happy_ptah_2
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: subscription_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.subscription_items (id, subscription_id, product_id, price_id, status, quantity, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: subscriptions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.subscriptions (id, billable_type, billable_id, type, paddle_id, status, trial_ends_at, paused_at, ends_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: swarms; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.swarms (id, team_id, created_at, updated_at, data) FROM stdin;
1	1	2024-09-01 00:00:00	2024-09-01 00:00:00	{"joinTokens": {"worker": "-", "manager": "-"}, "registries": [], "s3Storages": [], "managerNodes": [], "encryptionKey": "-", "registriesRev": 0, "s3StoragesRev": 0}
\.


--
-- Data for Name: team_invitations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.team_invitations (id, team_id, email, role, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: team_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.team_user (id, team_id, user_id, role, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: teams; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.teams (id, user_id, name, personal_team, created_at, updated_at, billing_name, billing_email, activating_subscription) FROM stdin;
1	1	Self Host	t	2024-09-01 00:00:00	2024-09-01 00:00:00	Self Host	self-host@localhost	f
\.


--
-- Data for Name: transactions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.transactions (id, billable_type, billable_id, paddle_id, paddle_subscription_id, invoice_number, status, total, tax, currency, billed_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, current_team_id, profile_photo_path, created_at, updated_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at) FROM stdin;
1	Self Host	self-hosted@localhost	2024-09-01 00:00:00	self_hosted_password	\N	1	\N	2024-09-01 00:00:00	2024-09-01 00:00:00	\N	\N	\N
\.


--
-- Name: agent_releases_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.agent_releases_id_seq', 1, false);


--
-- Name: customers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.customers_id_seq', 1, false);


--
-- Name: deployments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.deployments_id_seq', 2, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 52, true);


--
-- Name: networks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.networks_id_seq', 1, true);


--
-- Name: node_task_groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.node_task_groups_id_seq', 3, true);


--
-- Name: node_tasks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.node_tasks_id_seq', 17, true);


--
-- Name: nodes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.nodes_id_seq', 1, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.services_id_seq', 2, true);


--
-- Name: subscription_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.subscription_items_id_seq', 1, false);


--
-- Name: subscriptions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.subscriptions_id_seq', 1, false);


--
-- Name: swarms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.swarms_id_seq', 1, true);


--
-- Name: team_invitations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.team_invitations_id_seq', 1, false);


--
-- Name: team_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.team_user_id_seq', 1, false);


--
-- Name: teams_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.teams_id_seq', 1, true);


--
-- Name: transactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.transactions_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- Name: agent_releases agent_releases_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.agent_releases
    ADD CONSTRAINT agent_releases_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: customers customers_paddle_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.customers
    ADD CONSTRAINT customers_paddle_id_unique UNIQUE (paddle_id);


--
-- Name: customers customers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.customers
    ADD CONSTRAINT customers_pkey PRIMARY KEY (id);


--
-- Name: deployments deployments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.deployments
    ADD CONSTRAINT deployments_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: networks networks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.networks
    ADD CONSTRAINT networks_pkey PRIMARY KEY (id);


--
-- Name: node_task_groups node_task_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_task_groups
    ADD CONSTRAINT node_task_groups_pkey PRIMARY KEY (id);


--
-- Name: node_tasks node_tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_tasks
    ADD CONSTRAINT node_tasks_pkey PRIMARY KEY (id);


--
-- Name: nodes nodes_agent_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nodes
    ADD CONSTRAINT nodes_agent_token_unique UNIQUE (agent_token);


--
-- Name: nodes nodes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nodes
    ADD CONSTRAINT nodes_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: services services_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: subscription_items subscription_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_items
    ADD CONSTRAINT subscription_items_pkey PRIMARY KEY (id);


--
-- Name: subscription_items subscription_items_subscription_id_price_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_items
    ADD CONSTRAINT subscription_items_subscription_id_price_id_unique UNIQUE (subscription_id, price_id);


--
-- Name: subscriptions subscriptions_paddle_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscriptions
    ADD CONSTRAINT subscriptions_paddle_id_unique UNIQUE (paddle_id);


--
-- Name: subscriptions subscriptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscriptions
    ADD CONSTRAINT subscriptions_pkey PRIMARY KEY (id);


--
-- Name: swarms swarms_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.swarms
    ADD CONSTRAINT swarms_pkey PRIMARY KEY (id);


--
-- Name: team_invitations team_invitations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.team_invitations
    ADD CONSTRAINT team_invitations_pkey PRIMARY KEY (id);


--
-- Name: team_invitations team_invitations_team_id_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.team_invitations
    ADD CONSTRAINT team_invitations_team_id_email_unique UNIQUE (team_id, email);


--
-- Name: team_user team_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.team_user
    ADD CONSTRAINT team_user_pkey PRIMARY KEY (id);


--
-- Name: team_user team_user_team_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.team_user
    ADD CONSTRAINT team_user_team_id_user_id_unique UNIQUE (team_id, user_id);


--
-- Name: teams teams_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.teams
    ADD CONSTRAINT teams_pkey PRIMARY KEY (id);


--
-- Name: transactions transactions_paddle_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_paddle_id_unique UNIQUE (paddle_id);


--
-- Name: transactions transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transactions
    ADD CONSTRAINT transactions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: customers_billable_type_billable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX customers_billable_type_billable_id_index ON public.customers USING btree (billable_type, billable_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: subscriptions_billable_type_billable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX subscriptions_billable_type_billable_id_index ON public.subscriptions USING btree (billable_type, billable_id);


--
-- Name: teams_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX teams_user_id_index ON public.teams USING btree (user_id);


--
-- Name: transactions_billable_type_billable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX transactions_billable_type_billable_id_index ON public.transactions USING btree (billable_type, billable_id);


--
-- Name: transactions_paddle_subscription_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX transactions_paddle_subscription_id_index ON public.transactions USING btree (paddle_subscription_id);


--
-- Name: deployments deployments_service_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.deployments
    ADD CONSTRAINT deployments_service_id_foreign FOREIGN KEY (service_id) REFERENCES public.services(id) ON DELETE CASCADE;


--
-- Name: deployments deployments_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.deployments
    ADD CONSTRAINT deployments_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: networks networks_swarm_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.networks
    ADD CONSTRAINT networks_swarm_id_foreign FOREIGN KEY (swarm_id) REFERENCES public.swarms(id) ON DELETE CASCADE;


--
-- Name: networks networks_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.networks
    ADD CONSTRAINT networks_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: node_task_groups node_task_groups_invoker_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_task_groups
    ADD CONSTRAINT node_task_groups_invoker_id_foreign FOREIGN KEY (invoker_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: node_task_groups node_task_groups_node_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_task_groups
    ADD CONSTRAINT node_task_groups_node_id_foreign FOREIGN KEY (node_id) REFERENCES public.nodes(id) ON DELETE CASCADE;


--
-- Name: node_task_groups node_task_groups_swarm_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_task_groups
    ADD CONSTRAINT node_task_groups_swarm_id_foreign FOREIGN KEY (swarm_id) REFERENCES public.swarms(id) ON DELETE CASCADE;


--
-- Name: node_task_groups node_task_groups_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_task_groups
    ADD CONSTRAINT node_task_groups_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: node_tasks node_tasks_meta__deployment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_tasks
    ADD CONSTRAINT node_tasks_meta__deployment_id_foreign FOREIGN KEY (meta__deployment_id) REFERENCES public.deployments(id) ON DELETE CASCADE;


--
-- Name: node_tasks node_tasks_meta__service_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_tasks
    ADD CONSTRAINT node_tasks_meta__service_id_foreign FOREIGN KEY (meta__service_id) REFERENCES public.services(id) ON DELETE CASCADE;


--
-- Name: node_tasks node_tasks_task_group_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_tasks
    ADD CONSTRAINT node_tasks_task_group_id_foreign FOREIGN KEY (task_group_id) REFERENCES public.node_task_groups(id) ON DELETE CASCADE;


--
-- Name: node_tasks node_tasks_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.node_tasks
    ADD CONSTRAINT node_tasks_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: nodes nodes_swarm_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nodes
    ADD CONSTRAINT nodes_swarm_id_foreign FOREIGN KEY (swarm_id) REFERENCES public.swarms(id) ON DELETE SET NULL;


--
-- Name: nodes nodes_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.nodes
    ADD CONSTRAINT nodes_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: services services_placement_node_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_placement_node_id_foreign FOREIGN KEY (placement_node_id) REFERENCES public.nodes(id) ON DELETE SET NULL;


--
-- Name: services services_swarm_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_swarm_id_foreign FOREIGN KEY (swarm_id) REFERENCES public.swarms(id) ON DELETE CASCADE;


--
-- Name: services services_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.services
    ADD CONSTRAINT services_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: swarms swarms_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.swarms
    ADD CONSTRAINT swarms_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- Name: team_invitations team_invitations_team_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.team_invitations
    ADD CONSTRAINT team_invitations_team_id_foreign FOREIGN KEY (team_id) REFERENCES public.teams(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

COMMIT;
