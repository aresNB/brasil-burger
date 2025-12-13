package com.brasilburger.services;

import com.brasilburger.dao.MenuDAO;
import com.brasilburger.dao.BurgerDAO;
import com.brasilburger.dao.ComplementDAO;
import com.brasilburger.models.Menu;
import com.brasilburger.models.Burger;
import com.brasilburger.models.Complement;
import com.brasilburger.utils.ConsoleUtils;

import java.sql.SQLException;
import java.util.List;
import java.util.Scanner;

public class MenuService {

    private MenuDAO menuDAO;
    private BurgerDAO burgerDAO;
    private ComplementDAO complementDAO;
    private Scanner scanner;

    public MenuService() {
        this.menuDAO = new MenuDAO();
        this.burgerDAO = new BurgerDAO();
        this.complementDAO = new ComplementDAO();
        this.scanner = new Scanner(System.in);
    }

    public void afficherMenu() {
        while (true) {
            ConsoleUtils.clearScreen();
            System.out.println("\n" + ConsoleUtils.SEPARATOR);
            System.out.println(ConsoleUtils.centerText("GESTION DES MENUS"));
            System.out.println(ConsoleUtils.SEPARATOR);
            System.out.println("\n1. Créer un menu");
            System.out.println("0. Retour au menu principal");
            System.out.print("\nVotre choix : ");

            int choix = ConsoleUtils.lireEntier(scanner);

            switch (choix) {
                case 1:
                    creerMenu();
                    break;
                case 0:
                    return;
                default:
                    System.out.println("❌ Choix invalide!");
                    ConsoleUtils.pause();
            }
        }
    }

    private void creerMenu() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("CRÉER UN MENU"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            scanner.nextLine(); // Clear buffer

            System.out.print("\n📝 Libellé du menu : ");
            String libelle = scanner.nextLine().trim();

            if (libelle.isEmpty()) {
                System.out.println("❌ Le libellé ne peut pas être vide!");
                ConsoleUtils.pause();
                return;
            }

            System.out.print("🖼️  URL de l'image : ");
            String imageUrl = scanner.nextLine().trim();

            // Sélection du burger
            List<Burger> burgers = burgerDAO.findAllActive();
            System.out.println("\n🍔 Burgers disponibles :");
            for (Burger b : burgers) {
                System.out.println("  " + b);
            }
            System.out.print("Choisir un burger (ID) : ");
            int burgerId = ConsoleUtils.lireEntier(scanner);

            // Sélection de la boisson
            List<Complement> boissons = complementDAO.findByType("BOISSON");
            System.out.println("\n🥤 Boissons disponibles :");
            for (Complement c : boissons) {
                System.out.println("  " + c);
            }
            System.out.print("Choisir une boisson (ID) : ");
            int boissonId = ConsoleUtils.lireEntier(scanner);

            // Sélection des frites
            List<Complement> frites = complementDAO.findByType("FRITE");
            System.out.println("\n🍟 Frites disponibles :");
            for (Complement c : frites) {
                System.out.println("  " + c);
            }
            System.out.print("Choisir des frites (ID) : ");
            int friteId = ConsoleUtils.lireEntier(scanner);

            // Créer le menu
            Menu menu = new Menu(libelle, imageUrl, burgerId, boissonId, friteId);
            menuDAO.create(menu);

            // Recharger pour afficher avec détails
            menu = menuDAO.findById(menu.getId());

            System.out.println("\n✅ Menu créé avec succès!");
            System.out.println(menu.toDetailString());

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la création: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }
}