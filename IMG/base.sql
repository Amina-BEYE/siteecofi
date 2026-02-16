-- Extensions utiles
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Types personnalisés
CREATE TYPE project_status AS ENUM ('draft', 'planned', 'in_progress', 'completed', 'archived');
CREATE TYPE lead_source AS ENUM ('contact_form', 'service_form', 'phone', 'email', 'social', 'other');
CREATE TYPE lead_status AS ENUM ('new', 'in_progress', 'won', 'lost', 'archived');

-- Table entreprise / paramètres globaux
CREATE TABLE site_settings (
    id               uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    company_name     text NOT NULL,
    tagline          text,
    description      text,
    primary_email    text,
    secondary_email  text,
    primary_phone    text,
    secondary_phone  text,
    whatsapp_number  text,
    address          text,
    facebook_url     text,
    instagram_url    text,
    linkedin_url     text,
    youtube_url      text,
    hero_title       text,
    hero_subtitle    text,
    hero_cta_label   text,
    hero_cta_target  text,
    created_at       timestamptz NOT NULL DEFAULT now(),
    updated_at       timestamptz NOT NULL DEFAULT now()
);

-- Horaires d’ouverture
CREATE TABLE operating_hours (
    id          uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    label       text NOT NULL,
    day_of_week smallint NOT NULL CHECK (day_of_week BETWEEN 0 AND 6), -- 0 = dimanche
    opens_at    time,
    closes_at   time,
    is_closed   boolean NOT NULL DEFAULT false,
    created_at  timestamptz NOT NULL DEFAULT now(),
    updated_at  timestamptz NOT NULL DEFAULT now()
);

-- Gestion des médias
CREATE TABLE media_assets (
    id            uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    file_name     text NOT NULL,
    alt_text      text,
    url           text NOT NULL,
    mime_type     text,
    width         int,
    height        int,
    source_type   text CHECK (source_type IN ('internal', 'external')),
    created_by    uuid,
    created_at    timestamptz NOT NULL DEFAULT now(),
    updated_at    timestamptz NOT NULL DEFAULT now()
);

-- Contenu structuré (sections de page)
CREATE TABLE page_sections (
    id              uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    page_slug       text NOT NULL, -- ex: 'home', 'service-briques'
    section_key     text NOT NULL, -- ex: 'hero', 'services', 'contact'
    title           text,
    subtitle        text,
    content         jsonb,
    media_id        uuid REFERENCES media_assets(id) ON DELETE SET NULL,
    display_order   int NOT NULL DEFAULT 0,
    is_active       boolean NOT NULL DEFAULT true,
    created_at      timestamptz NOT NULL DEFAULT now(),
    updated_at      timestamptz NOT NULL DEFAULT now(),
    UNIQUE (page_slug, section_key)
);

-- Navigation
CREATE TABLE navigation_links (
    id            uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    menu_key      text NOT NULL, -- ex: 'header', 'footer'
    label         text NOT NULL,
    target_url    text NOT NULL,
    display_order int NOT NULL DEFAULT 0,
    is_external   boolean NOT NULL DEFAULT false,
    created_at    timestamptz NOT NULL DEFAULT now(),
    updated_at    timestamptz NOT NULL DEFAULT now()
);

-- Services
CREATE TABLE services (
    id               uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    name             text NOT NULL,
    slug             text NOT NULL UNIQUE,
    short_description text,
    long_description  text,
    category         text,
    hero_media_id    uuid REFERENCES media_assets(id) ON DELETE SET NULL,
    hero_badge       text,
    is_featured      boolean NOT NULL DEFAULT false,
    is_active        boolean NOT NULL DEFAULT true,
    display_order    int NOT NULL DEFAULT 0,
    created_at       timestamptz NOT NULL DEFAULT now(),
    updated_at       timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE service_highlights (
    id          uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    service_id  uuid NOT NULL REFERENCES services(id) ON DELETE CASCADE,
    title       text NOT NULL,
    description text,
    icon        text,
    display_order int NOT NULL DEFAULT 0,
    created_at  timestamptz NOT NULL DEFAULT now(),
    updated_at  timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE service_metrics (
    id            uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    service_id    uuid NOT NULL REFERENCES services(id) ON DELETE CASCADE,
    label         text NOT NULL,
    value         text NOT NULL,
    display_order int NOT NULL DEFAULT 0,
    created_at    timestamptz NOT NULL DEFAULT now(),
    updated_at    timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE service_media (
    id            uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    service_id    uuid NOT NULL REFERENCES services(id) ON DELETE CASCADE,
    media_id      uuid NOT NULL REFERENCES media_assets(id) ON DELETE CASCADE,
    caption       text,
    display_order int NOT NULL DEFAULT 0,
    created_at    timestamptz NOT NULL DEFAULT now(),
    updated_at    timestamptz NOT NULL DEFAULT now()
);

-- Projets
CREATE TABLE project_categories (
    id          uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    name        text NOT NULL,
    slug        text NOT NULL UNIQUE,
    description text,
    created_at  timestamptz NOT NULL DEFAULT now(),
    updated_at  timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE projects (
    id              uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    category_id     uuid REFERENCES project_categories(id) ON DELETE SET NULL,
    title           text NOT NULL,
    slug            text NOT NULL UNIQUE,
    summary         text,
    description     text,
    status          project_status NOT NULL DEFAULT 'draft',
    client_name     text,
    location        text,
    start_date      date,
    end_date        date,
    progress_pct    int CHECK (progress_pct BETWEEN 0 AND 100),
    featured        boolean NOT NULL DEFAULT false,
    hero_media_id   uuid REFERENCES media_assets(id) ON DELETE SET NULL,
    created_at      timestamptz NOT NULL DEFAULT now(),
    updated_at      timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE project_media (
    id            uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    project_id    uuid NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
    media_id      uuid NOT NULL REFERENCES media_assets(id) ON DELETE CASCADE,
    caption       text,
    display_order int NOT NULL DEFAULT 0,
    is_primary    boolean NOT NULL DEFAULT false,
    created_at    timestamptz NOT NULL DEFAULT now(),
    updated_at    timestamptz NOT NULL DEFAULT now()
);

-- Témoignages et équipe
CREATE TABLE testimonials (
    id             uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    author_name    text NOT NULL,
    author_role    text,
    company        text,
    content        text NOT NULL,
    rating         int CHECK (rating BETWEEN 1 AND 5),
    media_id       uuid REFERENCES media_assets(id) ON DELETE SET NULL,
    display_order  int NOT NULL DEFAULT 0,
    is_published   boolean NOT NULL DEFAULT true,
    created_at     timestamptz NOT NULL DEFAULT now(),
    updated_at     timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE team_members (
    id             uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    full_name      text NOT NULL,
    role_title     text NOT NULL,
    bio            text,
    phone          text,
    email          text,
    linkedin_url   text,
    media_id       uuid REFERENCES media_assets(id) ON DELETE SET NULL,
    is_visible     boolean NOT NULL DEFAULT true,
    display_order  int NOT NULL DEFAULT 0,
    created_at     timestamptz NOT NULL DEFAULT now(),
    updated_at     timestamptz NOT NULL DEFAULT now()
);

-- Requêtes de contact globales
CREATE TABLE contact_requests (
    id             uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    full_name      text NOT NULL,
    email          text,
    phone          text,
    subject        text,
    message        text NOT NULL,
    source_page    text,
    metadata       jsonb,
    handled_by     uuid REFERENCES users(id) ON DELETE SET NULL,
    handled_at     timestamptz,
    created_at     timestamptz NOT NULL DEFAULT now(),
    updated_at     timestamptz NOT NULL DEFAULT now()
);

-- Leads (devis, demandes spécifiques)
CREATE TABLE leads (
    id             uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    lead_source    lead_source NOT NULL DEFAULT 'contact_form',
    lead_status    lead_status NOT NULL DEFAULT 'new',
    first_name     text,
    last_name      text,
    company_name   text,
    email          text,
    phone          text,
    preferred_contact_method text CHECK (preferred_contact_method IN ('phone', 'email', 'whatsapp')),
    project_description text,
    budget_range   text,
    requested_start_date date,
    assigned_to    uuid REFERENCES users(id) ON DELETE SET NULL,
    notes          text,
    metadata       jsonb,
    created_at     timestamptz NOT NULL DEFAULT now(),
    updated_at     timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE lead_services (
    lead_id     uuid NOT NULL REFERENCES leads(id) ON DELETE CASCADE,
    service_id  uuid NOT NULL REFERENCES services(id) ON DELETE CASCADE,
    primary key (lead_id, service_id)
);

CREATE TABLE service_leads (
    id             uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    service_id     uuid NOT NULL REFERENCES services(id) ON DELETE CASCADE,
    full_name      text NOT NULL,
    email          text,
    phone          text NOT NULL,
    company_name   text,
    project_scope  text,
    quantity       text,
    additional_info text,
    lead_status    lead_status NOT NULL DEFAULT 'new',
    metadata       jsonb,
    created_at     timestamptz NOT NULL DEFAULT now(),
    updated_at     timestamptz NOT NULL DEFAULT now()
);

-- Authentification & administration
CREATE TABLE users (
    id            uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    email         text NOT NULL UNIQUE,
    password_hash text NOT NULL,
    first_name    text,
    last_name     text,
    phone         text,
    is_active     boolean NOT NULL DEFAULT true,
    last_login_at timestamptz,
    created_at    timestamptz NOT NULL DEFAULT now(),
    updated_at    timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE roles (
    id          uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    name        text NOT NULL UNIQUE,
    description text,
    created_at  timestamptz NOT NULL DEFAULT now(),
    updated_at  timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE permissions (
    id          uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    code        text NOT NULL UNIQUE, -- ex: 'manage_services'
    description text,
    created_at  timestamptz NOT NULL DEFAULT now(),
    updated_at  timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE role_permissions (
    role_id       uuid NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id uuid NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE user_roles (
    user_id uuid NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id uuid NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, role_id)
);

-- Audit de contenu (optionnel)
CREATE TABLE activity_logs (
    id           uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id      uuid REFERENCES users(id) ON DELETE SET NULL,
    action       text NOT NULL,
    entity_type  text NOT NULL, -- ex: 'service', 'project'
    entity_id    uuid,
    metadata     jsonb,
    created_at   timestamptz NOT NULL DEFAULT now()
);
