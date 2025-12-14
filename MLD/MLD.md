Table: users
users(
id: SERIAL PRIMARY KEY,
nom: VARCHAR(100) NOT NULL,
prenom: VARCHAR(100) NOT NULL,
tel: VARCHAR(20) NOT NULL UNIQUE,
email: VARCHAR(150) NOT NULL UNIQUE,
password: VARCHAR(255) NOT NULL,
adresse: TEXT,
role: VARCHAR(20) NOT NULL CHECK(role IN ('CLIENT', 'GESTIONNAIRE', 'LIVREUR')),
vehicule: VARCHAR(50),
disponible: BOOLEAN DEFAULT true,
createdAt: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)

Table: burger_categories
burger_categories(
id: SERIAL PRIMARY KEY,
nom: VARCHAR(100) NOT NULL UNIQUE
)

Table: burgers
burgers(
id: SERIAL PRIMARY KEY,
libelle: VARCHAR(150) NOT NULL,
description: TEXT,
prix: DECIMAL(10,2) NOT NULL CHECK(prix > 0),
imageUrl: VARCHAR(500),
isArchived: BOOLEAN DEFAULT false,
categorieId: INTEGER,
createdAt: TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (categorieId) REFERENCES burger_categories(id) ON DELETE SET NULL
)

Table: complements
complements(
id: SERIAL PRIMARY KEY,
libelle: VARCHAR(100) NOT NULL,
prix: DECIMAL(10,2) NOT NULL CHECK(prix >= 0),
imageUrl: VARCHAR(500),
type: VARCHAR(20) NOT NULL CHECK(type IN ('BOISSON', 'FRITE')),
isArchived: BOOLEAN DEFAULT false,
createdAt: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)

Table: menus
menus(
id: SERIAL PRIMARY KEY,
libelle: VARCHAR(150) NOT NULL,
imageUrl: VARCHAR(500),
isArchived: BOOLEAN DEFAULT false,
burgerId: INTEGER NOT NULL,
boissonId: INTEGER NOT NULL,
friteId: INTEGER NOT NULL,
createdAt: TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (burgerId) REFERENCES burgers(id) ON DELETE RESTRICT,
FOREIGN KEY (boissonId) REFERENCES complements(id) ON DELETE RESTRICT,
FOREIGN KEY (friteId) REFERENCES complements(id) ON DELETE RESTRICT
)

Table: zones
zones(
id: SERIAL PRIMARY KEY,
nom: VARCHAR(100) NOT NULL UNIQUE,
prixLivraison: DECIMAL(10,2) NOT NULL CHECK(prixLivraison >= 0),
actif: BOOLEAN DEFAULT true
)

Table: quartiers
quartiers(
id: SERIAL PRIMARY KEY,
nom: VARCHAR(100) NOT NULL,
codePostal: VARCHAR(10),
zoneId: INTEGER NOT NULL,
FOREIGN KEY (zoneId) REFERENCES zones(id) ON DELETE CASCADE
)

Table: commandes
commandes(
id: SERIAL PRIMARY KEY,
numeroCommande: VARCHAR(50) NOT NULL UNIQUE,
dateCommande: TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
montantTotal: DECIMAL(10,2) NOT NULL CHECK(montantTotal > 0),
etat: VARCHAR(20) NOT NULL DEFAULT 'EN_ATTENTE'
CHECK(etat IN ('EN_ATTENTE', 'VALIDEE', 'EN_PREPARATION', 'TERMINEE', 'EN_LIVRAISON', 'LIVREE', 'ANNULEE')),
modeConsommation: VARCHAR(20) NOT NULL
CHECK(modeConsommation IN ('SUR_PLACE', 'A_EMPORTER', 'LIVRAISON')),
adresseLivraison: TEXT,
clientId: INTEGER NOT NULL,
livreurId: INTEGER,
zoneId: INTEGER,
createdAt: TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updatedAt: TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (clientId) REFERENCES users(id) ON DELETE RESTRICT,
FOREIGN KEY (livreurId) REFERENCES users(id) ON DELETE SET NULL,
FOREIGN KEY (zoneId) REFERENCES zones(id) ON DELETE SET NULL
)

Table: lignes_commande
lignes_commande(
id: SERIAL PRIMARY KEY,
quantite: INTEGER NOT NULL CHECK(quantite > 0),
prixUnitaire: DECIMAL(10,2) NOT NULL CHECK(prixUnitaire >= 0),
sousTotal: DECIMAL(10,2) NOT NULL CHECK(sousTotal >= 0),
typeProduit: VARCHAR(20) NOT NULL CHECK(typeProduit IN ('BURGER', 'MENU', 'COMPLEMENT')),
commandeId: INTEGER NOT NULL,
burgerId: INTEGER,
menuId: INTEGER,
complementId: INTEGER,
FOREIGN KEY (commandeId) REFERENCES commandes(id) ON DELETE CASCADE,
FOREIGN KEY (burgerId) REFERENCES burgers(id) ON DELETE RESTRICT,
FOREIGN KEY (menuId) REFERENCES menus(id) ON DELETE RESTRICT,
FOREIGN KEY (complementId) REFERENCES complements(id) ON DELETE RESTRICT,
CHECK (
(typeProduit = 'BURGER' AND burgerId IS NOT NULL AND menuId IS NULL AND complementId IS NULL) OR
(typeProduit = 'MENU' AND menuId IS NOT NULL AND burgerId IS NULL AND complementId IS NULL) OR
(typeProduit = 'COMPLEMENT' AND complementId IS NOT NULL AND burgerId IS NULL AND menuId IS NULL)
)
)

Table: paiements
paiements(
id: SERIAL PRIMARY KEY,
datePaiement: TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
montant: DECIMAL(10,2) NOT NULL CHECK(montant > 0),
moyenPaiement: VARCHAR(20) NOT NULL CHECK(moyenPaiement IN ('WAVE', 'OM')),
refTransaction: VARCHAR(100) NOT NULL UNIQUE,
statut: VARCHAR(20) DEFAULT 'VALIDE' CHECK(statut IN ('VALIDE', 'ECHOUE', 'EN_ATTENTE')),
commandeId: INTEGER NOT NULL UNIQUE,
FOREIGN KEY (commandeId) REFERENCES commandes(id) ON DELETE RESTRICT
)
