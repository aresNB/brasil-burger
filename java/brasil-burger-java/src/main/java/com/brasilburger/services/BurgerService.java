package com.brasilburger.services;

import com.brasilburger.dao.BurgerDAO;
import com.brasilburger.dao.CategorieDAO;
import com.brasilburger.models.Burger;
import com.brasilburger.models.BurgerCategorie;
import com.brasilburger.utils.ConsoleUtils;

import java.math.BigDecimal;
import java.sql.SQLException;
import java.util.List;
import java.util.Scanner;

public class BurgerService {

    private BurgerDAO burgerDAO;
    private CategorieDAO categorieDAO;
    private Scanner scanner;

    public BurgerService() {
        this.burgerDAO = new BurgerDAO();
        this.categorieDAO = new CategorieDAO();
        this.scanner = new Scanner(System.in);
    }

    public void afficherMenu() {
        while (true) {
            ConsoleUtils.clearScreen();
            System.out.println("\n" + ConsoleUtils.SEPARATOR);
            System.out.println(ConsoleUtils.centerText("GESTION DES BURGERS"));
            System.out.println(ConsoleUtils.SEPARATOR);
            System.out.println("\n1. Créer un burger");
            System.out.println("2. Lister tous les burgers");
            System.out.println("3. Modifier un burger");
            System.out.println("4. Archiver un burger");
            System.out.println("5. Rechercher par catégorie");
            System.out.println("0. Retour au menu principal");
            System.out.print("\nVotre choix : ");

            int choix = ConsoleUtils.lireEntier(scanner);

            switch (choix) {
                case 1:
                    creerBurger();
                    break;
                case 2:
                    listerBurgers();
                    break;
                case 3:
                    modifierBurger();
                    break;
                case 4:
                    archiverBurger();
                    break;
                case 5:
                    rechercherParCategorie();
                    break;
                case 0:
                    return;
                default:
                    System.out.println("❌ Choix invalide!");
                    ConsoleUtils.pause();
            }
        }
    }

    private void creerBurger() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("CRÉER UN BURGER"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            scanner.nextLine();

            System.out.print("\n📝 Libellé du burger : ");
            String libelle = scanner.nextLine().trim();

            if (libelle.isEmpty()) {
                System.out.println("❌ Le libellé ne peut pas être vide!");
                ConsoleUtils.pause();
                return;
            }

            System.out.print("📄 Description : ");
            String description = scanner.nextLine().trim();

            System.out.print("💰 Prix (FCFA) : ");
            BigDecimal prix = new BigDecimal(scanner.nextLine().trim());

            if (prix.compareTo(BigDecimal.ZERO) <= 0) {
                System.out.println("❌ Le prix doit être supérieur à 0!");
                ConsoleUtils.pause();
                return;
            }

            System.out.print("🖼️  URL de l'image : ");
            String imageUrl = scanner.nextLine().trim();

            List<BurgerCategorie> categories = categorieDAO.findAll();
            System.out.println("\n📂 Catégories disponibles :");
            for (BurgerCategorie cat : categories) {
                System.out.println("  " + cat);
            }
            System.out.print("Choisir une catégorie (ID) : ");
            int categorieId = ConsoleUtils.lireEntier(scanner);

            Burger burger = new Burger(libelle, description, prix, imageUrl, categorieId);
            burgerDAO.create(burger);

            System.out.println("\n✅ Burger créé avec succès!");
            System.out.println(burger.toDetailString());

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la création: " + e.getMessage());
        } catch (NumberFormatException e) {
            System.out.println("❌ Format de nombre invalide!");
        }

        ConsoleUtils.pause();
    }

    /**
     * Lister tous les burgers
     */
    private void listerBurgers() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("LISTE DES BURGERS"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Burger> burgers = burgerDAO.findAll();

            if (burgers.isEmpty()) {
                System.out.println("\n📭 Aucun burger trouvé.");
            } else {
                System.out.println("\n📋 Total : " + burgers.size() + " burger(s)\n");

                // Séparer actifs et archivés
                List<Burger> actifs = burgers.stream()
                        .filter(b -> !b.isArchived())
                        .toList();
                List<Burger> archives = burgers.stream()
                        .filter(Burger::isArchived)
                        .toList();

                if (!actifs.isEmpty()) {
                    System.out.println("✅ BURGERS ACTIFS (" + actifs.size() + "):");
                    for (Burger burger : actifs) {
                        System.out.println("  " + burger);
                    }
                }

                if (!archives.isEmpty()) {
                    System.out.println("\n📦 BURGERS ARCHIVÉS (" + archives.size() + "):");
                    for (Burger burger : archives) {
                        System.out.println("  " + burger);
                    }
                }
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la récupération: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }

    /**
     * Modifier un burger
     */
    private void modifierBurger() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("MODIFIER UN BURGER"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            // Afficher la liste des burgers actifs
            List<Burger> burgers = burgerDAO.findAllActive();
            if (burgers.isEmpty()) {
                System.out.println("\n📭 Aucun burger actif trouvé.");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📋 Burgers disponibles :");
            for (Burger burger : burgers) {
                System.out.println("  " + burger);
            }

            System.out.print("\nID du burger à modifier : ");
            int id = ConsoleUtils.lireEntier(scanner);

            Burger burger = burgerDAO.findById(id);
            if (burger == null) {
                System.out.println("❌ Burger introuvable!");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📝 Burger actuel :");
            System.out.println(burger.toDetailString());

            scanner.nextLine(); // Clear buffer

            // Modification des champs
            System.out.print("\nNouveau libellé (ou Entrée pour garder) : ");
            String libelle = scanner.nextLine().trim();
            if (!libelle.isEmpty()) {
                burger.setLibelle(libelle);
            }

            System.out.print("Nouvelle description (ou Entrée pour garder) : ");
            String description = scanner.nextLine().trim();
            if (!description.isEmpty()) {
                burger.setDescription(description);
            }

            System.out.print("Nouveau prix (ou 0 pour garder) : ");
            String prixStr = scanner.nextLine().trim();
            if (!prixStr.isEmpty() && !prixStr.equals("0")) {
                burger.setPrix(new BigDecimal(prixStr));
            }

            System.out.print("Nouvelle URL image (ou Entrée pour garder) : ");
            String imageUrl = scanner.nextLine().trim();
            if (!imageUrl.isEmpty()) {
                burger.setImageUrl(imageUrl);
            }

            // Catégorie
            List<BurgerCategorie> categories = categorieDAO.findAll();
            System.out.println("\n📂 Catégories disponibles :");
            for (BurgerCategorie cat : categories) {
                System.out.println("  " + cat);
            }
            System.out.print("Nouvelle catégorie (ou 0 pour garder) : ");
            int categorieId = ConsoleUtils.lireEntier(scanner);
            if (categorieId > 0) {
                burger.setCategorieId(categorieId);
            }

            burgerDAO.update(burger);
            System.out.println("\n✅ Burger modifié avec succès!");

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la modification: " + e.getMessage());
        } catch (NumberFormatException e) {
            System.out.println("❌ Format de nombre invalide!");
        }

        ConsoleUtils.pause();
    }

    /**
     * Archiver un burger
     */
    private void archiverBurger() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("ARCHIVER UN BURGER"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Burger> burgers = burgerDAO.findAllActive();
            if (burgers.isEmpty()) {
                System.out.println("\n📭 Aucun burger actif trouvé.");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📋 Burgers actifs :");
            for (Burger burger : burgers) {
                System.out.println("  " + burger);
            }

            System.out.print("\nID du burger à archiver : ");
            int id = ConsoleUtils.lireEntier(scanner);

            Burger burger = burgerDAO.findById(id);
            if (burger == null) {
                System.out.println("❌ Burger introuvable!");
                ConsoleUtils.pause();
                return;
            }

            if (burger.isArchived()) {
                System.out.println("⚠️  Ce burger est déjà archivé!");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n⚠️  Voulez-vous vraiment archiver ce burger ?");
            System.out.println(burger.toDetailString());
            System.out.print("Confirmer (O/N) : ");

            // FIX: Utiliser scanner.next() au lieu de nextLine()
            String confirmation = scanner.next().trim().toUpperCase();
            scanner.nextLine(); // Vider le buffer après

            if (confirmation.equals("O") || confirmation.equals("OUI")) {
                burgerDAO.archive(id);
                System.out.println("\n✅ Burger archivé avec succès!");
            } else {
                System.out.println("\n❌ Opération annulée.");
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de l'archivage: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }

    /**
     * Rechercher par catégorie
     */
    private void rechercherParCategorie() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("RECHERCHE PAR CATÉGORIE"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<BurgerCategorie> categories = categorieDAO.findAll();
            System.out.println("\n📂 Catégories disponibles :");
            for (BurgerCategorie cat : categories) {
                System.out.println("  " + cat);
            }

            System.out.print("\nChoisir une catégorie (ID) : ");
            int categorieId = ConsoleUtils.lireEntier(scanner);

            List<Burger> burgers = burgerDAO.findByCategorie(categorieId);

            if (burgers.isEmpty()) {
                System.out.println("\n📭 Aucun burger trouvé dans cette catégorie.");
            } else {
                System.out.println("\n✅ " + burgers.size() + " burger(s) trouvé(s) :\n");
                for (Burger burger : burgers) {
                    System.out.println(burger.toDetailString());
                }
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la recherche: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }
}