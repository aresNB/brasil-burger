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

}