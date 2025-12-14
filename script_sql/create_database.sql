-- =====================================================
-- SCRIPT DE CRÉATION DE LA BASE DE DONNÉES
-- Projet: Brasil Burger - Gestion des Commandes
-- =====================================================

-- Suppression des tables si elles existent (pour réinitialisation)
DROP TABLE IF EXISTS paiements CASCADE;
DROP TABLE IF EXISTS lignes_commande CASCADE;
DROP TABLE IF EXISTS commandes CASCADE;
DROP TABLE IF EXISTS quartiers CASCADE;
DROP TABLE IF EXISTS zones CASCADE;
DROP TABLE IF EXISTS menus CASCADE;
DROP TABLE IF EXISTS complements CASCADE;
DROP TABLE IF EXISTS burgers CASCADE;
DROP TABLE IF EXISTS burger_categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- =====================================================
-- TABLE: users
-- =====================================================
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    tel VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    adresse TEXT,
    role VARCHAR(20) NOT NULL CHECK(role IN ('CLIENT', 'GESTIONNAIRE', 'LIVREUR')),
    vehicule VARCHAR(50),
    disponible BOOLEAN DEFAULT true,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLE: burger_categories
-- =====================================================
CREATE TABLE burger_categories (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- =====================================================
-- TABLE: burgers
-- =====================================================
CREATE TABLE burgers (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL CHECK(prix > 0),
    imageUrl VARCHAR(500),
    isArchived BOOLEAN DEFAULT false,
    categorieId INTEGER,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorieId) REFERENCES burger_categories(id) ON DELETE SET NULL
);

-- =====================================================
-- TABLE: complements
-- =====================================================
CREATE TABLE complements (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    prix DECIMAL(10,2) NOT NULL CHECK(prix >= 0),
    imageUrl VARCHAR(500),
    type VARCHAR(20) NOT NULL CHECK(type IN ('BOISSON', 'FRITE')),
    isArchived BOOLEAN DEFAULT false,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLE: menus
-- =====================================================
CREATE TABLE menus (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(150) NOT NULL,
    imageUrl VARCHAR(500),
    isArchived BOOLEAN DEFAULT false,
    burgerId INTEGER NOT NULL,
    boissonId INTEGER NOT NULL,
    friteId INTEGER NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (burgerId) REFERENCES burgers(id) ON DELETE RESTRICT,
    FOREIGN KEY (boissonId) REFERENCES complements(id) ON DELETE RESTRICT,
    FOREIGN KEY (friteId) REFERENCES complements(id) ON DELETE RESTRICT
);

-- =====================================================
-- TABLE: zones
-- =====================================================
CREATE TABLE zones (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    prixLivraison DECIMAL(10,2) NOT NULL CHECK(prixLivraison >= 0),
    actif BOOLEAN DEFAULT true
);

-- =====================================================
-- TABLE: quartiers
-- =====================================================
CREATE TABLE quartiers (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    codePostal VARCHAR(10),
    zoneId INTEGER NOT NULL,
    FOREIGN KEY (zoneId) REFERENCES zones(id) ON DELETE CASCADE
);

-- =====================================================
-- TABLE: commandes
-- =====================================================
CREATE TABLE commandes (
    id SERIAL PRIMARY KEY,
    numeroCommande VARCHAR(50) NOT NULL UNIQUE,
    dateCommande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montantTotal DECIMAL(10,2) NOT NULL CHECK(montantTotal > 0),
    etat VARCHAR(20) NOT NULL DEFAULT 'EN_ATTENTE' 
        CHECK(etat IN ('EN_ATTENTE', 'VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON', 'LIVREE', 'ANNULEE')),
    modeConsommation VARCHAR(20) NOT NULL 
        CHECK(modeConsommation IN ('SUR_PLACE', 'A_EMPORTER', 'LIVRAISON')),
    adresseLivraison TEXT,
    clientId INTEGER NOT NULL,
    livreurId INTEGER,
    zoneId INTEGER,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clientId) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (livreurId) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (zoneId) REFERENCES zones(id) ON DELETE SET NULL
);

-- =====================================================
-- TABLE: lignes_commande
-- =====================================================
CREATE TABLE lignes_commande (
    id SERIAL PRIMARY KEY,
    quantite INTEGER NOT NULL CHECK(quantite > 0),
    prixUnitaire DECIMAL(10,2) NOT NULL CHECK(prixUnitaire >= 0),
    sousTotal DECIMAL(10,2) NOT NULL CHECK(sousTotal >= 0),
    typeProduit VARCHAR(20) NOT NULL CHECK(typeProduit IN ('BURGER', 'MENU', 'COMPLEMENT')),
    commandeId INTEGER NOT NULL,
    burgerId INTEGER,
    menuId INTEGER,
    complementId INTEGER,
    FOREIGN KEY (commandeId) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (burgerId) REFERENCES burgers(id) ON DELETE RESTRICT,
    FOREIGN KEY (menuId) REFERENCES menus(id) ON DELETE RESTRICT,
    FOREIGN KEY (complementId) REFERENCES complements(id) ON DELETE RESTRICT,
    CHECK (
        (typeProduit = 'BURGER' AND burgerId IS NOT NULL AND menuId IS NULL AND complementId IS NULL) OR
        (typeProduit = 'MENU' AND menuId IS NOT NULL AND burgerId IS NULL AND complementId IS NULL) OR
        (typeProduit = 'COMPLEMENT' AND complementId IS NOT NULL AND burgerId IS NULL AND menuId IS NULL)
    )
);

-- =====================================================
-- TABLE: paiements
-- =====================================================
CREATE TABLE paiements (
    id SERIAL PRIMARY KEY,
    datePaiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montant DECIMAL(10,2) NOT NULL CHECK(montant > 0),
    moyenPaiement VARCHAR(20) NOT NULL CHECK(moyenPaiement IN ('WAVE', 'OM')),
    refTransaction VARCHAR(100) NOT NULL UNIQUE,
    statut VARCHAR(20) DEFAULT 'VALIDE' CHECK(statut IN ('VALIDE', 'ECHOUE', 'EN_ATTENTE')),
    commandeId INTEGER NOT NULL UNIQUE,
    FOREIGN KEY (commandeId) REFERENCES commandes(id) ON DELETE RESTRICT
);

-- =====================================================
-- INDEX POUR OPTIMISATION
-- =====================================================
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_tel ON users(tel);
CREATE INDEX idx_commandes_client ON commandes(clientId);
CREATE INDEX idx_commandes_etat ON commandes(etat);
CREATE INDEX idx_commandes_date ON commandes(dateCommande);
CREATE INDEX idx_commandes_livreur ON commandes(livreurId);
CREATE INDEX idx_commandes_numero ON commandes(numeroCommande);
CREATE INDEX idx_lignes_commande ON lignes_commande(commandeId);
CREATE INDEX idx_burgers_archived ON burgers(isArchived);
CREATE INDEX idx_burgers_categorie ON burgers(categorieId);
CREATE INDEX idx_menus_archived ON menus(isArchived);
CREATE INDEX idx_complements_archived ON complements(isArchived);
CREATE INDEX idx_complements_type ON complements(type);
CREATE INDEX idx_quartiers_zone ON quartiers(zoneId);
CREATE INDEX idx_paiements_commande ON paiements(commandeId);

-- =====================================================
-- DONNÉES DE TEST (OPTIONNEL mais recommandé)
-- =====================================================

-- Catégories de burgers
INSERT INTO burger_categories (nom) VALUES
('Classique'),
('Poulet'),
('Végétarien'),
('Premium');

-- Utilisateurs
INSERT INTO users (nom, prenom, tel, email, password, role, adresse) VALUES
('Admin', 'Gestionnaire', '+221771234567', 'admin@brasilburger.com', '$2y$10$example', 'GESTIONNAIRE', NULL),
('Diallo', 'Mamadou', '+221772345678', 'mamadou@livreur.com', '$2y$10$example', 'LIVREUR', NULL),
('Ndiaye', 'Ousmane', '+221774567890', 'ousmane@client.com', '$2y$10$example', 'CLIENT', 'Mermoz, Dakar');

-- Burgers
INSERT INTO burgers (libelle, description, prix, imageUrl, categorieId) VALUES
('Burger Classique', 'Burger avec steak haché, salade, tomate', 3500.00, '/images/burger1.jpg', 1),
('Cheese Burger', 'Burger avec double fromage', 4000.00, '/images/burger2.jpg', 1),
('Chicken Burger', 'Burger au poulet croustillant', 4500.00, '/images/burger3.jpg', 2);

-- Compléments
INSERT INTO complements (libelle, prix, imageUrl, type) VALUES
('Coca Cola 33cl', 500.00, '/images/coca.jpg', 'BOISSON'),
('Sprite 33cl', 500.00, '/images/sprite.jpg', 'BOISSON'),
('Frites Normales', 1000.00, '/images/frites.jpg', 'FRITE'),
('Frites Grande', 1500.00, '/images/frites-grande.jpg', 'FRITE');

-- Menus
INSERT INTO menus (libelle, imageUrl, burgerId, boissonId, friteId) VALUES
('Menu Classique', '/images/menu1.jpg', 1, 1, 3),
('Menu Cheese', '/images/menu2.jpg', 2, 2, 3);

-- Zones de livraison
INSERT INTO zones (nom, prixLivraison, actif) VALUES
('Plateau', 1000.00, true),
('Mermoz', 1500.00, true),
('Almadies', 2000.00, true);

-- Quartiers
INSERT INTO quartiers (nom, codePostal, zoneId) VALUES
('Plateau Centre', '11000', 1),
('Mermoz Pyrotechnie', '12000', 2),
('Almadies Plage', '13000', 3);