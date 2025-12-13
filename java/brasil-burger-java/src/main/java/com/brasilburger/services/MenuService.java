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
import java.util.List;

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
            System.out.println("2. Lister tous les menus");
            System.out.println("3. Modifier un menu");
            System.out.println("4. Archiver un menu");
            System.out.println("0. Retour au menu principal");
            System.out.print("\nVotre choix : ");

            int choix = ConsoleUtils.lireEntier(scanner);

            switch (choix) {
                case 1:
                    creerMenu();
                    break;
                case 2:
                    listerMenus();
                    break;
                case 3:
                    modifierMenu();
                    break;
                case 4:
                    archiverMenu();
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

    private void listerMenus() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("LISTE DES MENUS"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Menu> menus = menuDAO.findAll();

            if (menus.isEmpty()) {
                System.out.println("\n📭 Aucun menu trouvé.");
            } else {
                System.out.println("\n📋 Total : " + menus.size() + " menu(s)\n");

                for (Menu menu : menus) {
                    System.out.println(menu.toDetailString());
                }
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la récupération: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }

    private void modifierMenu() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("MODIFIER UN MENU"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Menu> menus = menuDAO.findAllActive();
            if (menus.isEmpty()) {
                System.out.println("\n📭 Aucun menu actif trouvé.");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📋 Menus disponibles :");
            for (Menu menu : menus) {
                System.out.println("  " + menu);
            }

            System.out.print("\nID du menu à modifier : ");
            int id = ConsoleUtils.lireEntier(scanner);

            Menu menu = menuDAO.findById(id);
            if (menu == null) {
                System.out.println("❌ Menu introuvable!");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📝 Menu actuel :");
            System.out.println(menu.toDetailString());

            scanner.nextLine(); // Clear buffer

            System.out.print("\nNouveau libellé (ou Entrée pour garder) : ");
            String libelle = scanner.nextLine().trim();
            if (!libelle.isEmpty()) {
                menu.setLibelle(libelle);
            }

            System.out.print("Nouvelle URL image (ou Entrée pour garder) : ");
            String imageUrl = scanner.nextLine().trim();
            if (!imageUrl.isEmpty()) {
                menu.setImageUrl(imageUrl);
            }

            // Modification des composants (optionnel)
            System.out.print("\nModifier le burger ? (O/N) : ");
            String changeBurger = scanner.next().trim().toUpperCase();
            scanner.nextLine();

            if (changeBurger.equals("O")) {
                List<Burger> burgers = burgerDAO.findAllActive();
                System.out.println("\n🍔 Burgers disponibles :");
                for (Burger b : burgers) {
                    System.out.println("  " + b);
                }
                System.out.print("Nouveau burger (ID) : ");
                menu.setBurgerId(ConsoleUtils.lireEntier(scanner));
                scanner.nextLine();
            }

            System.out.print("Modifier la boisson ? (O/N) : ");
            String changeBoisson = scanner.next().trim().toUpperCase();
            scanner.nextLine();

            if (changeBoisson.equals("O")) {
                List<Complement> boissons = complementDAO.findByType("BOISSON");
                System.out.println("\n🥤 Boissons disponibles :");
                for (Complement c : boissons) {
                    System.out.println("  " + c);
                }
                System.out.print("Nouvelle boisson (ID) : ");
                menu.setBoissonId(ConsoleUtils.lireEntier(scanner));
                scanner.nextLine();
            }

            System.out.print("Modifier les frites ? (O/N) : ");
            String changeFrite = scanner.next().trim().toUpperCase();
            scanner.nextLine();

            if (changeFrite.equals("O")) {
                List<Complement> frites = complementDAO.findByType("FRITE");
                System.out.println("\n🍟 Frites disponibles :");
                for (Complement c : frites) {
                    System.out.println("  " + c);
                }
                System.out.print("Nouvelles frites (ID) : ");
                menu.setFriteId(ConsoleUtils.lireEntier(scanner));
            }

            menuDAO.update(menu);
            System.out.println("\n✅ Menu modifié avec succès!");

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la modification: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }

    private void archiverMenu() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("ARCHIVER UN MENU"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Menu> menus = menuDAO.findAllActive();
            if (menus.isEmpty()) {
                System.out.println("\n📭 Aucun menu actif trouvé.");
                ConsoleUtils.pause();
                return;
            }
            System.out.println("\n📋 Menus actifs :");
            for (Menu menu : menus) {
                System.out.println("  " + menu);
            }

            System.out.print("\nID du menu à archiver : ");
            int id = ConsoleUtils.lireEntier(scanner);

            Menu menu = menuDAO.findById(id);
            if (menu == null) {
                System.out.println("❌ Menu introuvable!");
                ConsoleUtils.pause();
                return;
            }

            if (menu.isArchived()) {
                System.out.println("⚠️  Ce menu est déjà archivé!");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n⚠️  Voulez-vous vraiment archiver ce menu ?");
            System.out.println(menu.toDetailString());
            System.out.print("Confirmer (O/N) : ");

            String confirmation = scanner.next().trim().toUpperCase();
            scanner.nextLine();

            if (confirmation.equals("O") || confirmation.equals("OUI")) {
                menuDAO.archive(id);
                System.out.println("\n✅ Menu archivé avec succès!");
            } else {
                System.out.println("\n❌ Opération annulée.");
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de l'archivage: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }
}