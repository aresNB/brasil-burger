package com.brasilburger;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.services.BurgerService;
import com.brasilburger.utils.ConsoleUtils;
import com.brasilburger.services.ComplementService;
import com.brasilburger.services.MenuService;

import java.util.Scanner;

public class App {

    private static Scanner scanner = new Scanner(System.in);
    private static BurgerService burgerService = new BurgerService();
    private static ComplementService complementService = new ComplementService();
    private static MenuService menuService = new MenuService();

    public static void main(String[] args) {
        DatabaseConfig dbConfig = DatabaseConfig.getInstance();

        if (!dbConfig.testConnection()) {
            System.err.println("❌ Impossible de se connecter à la base de données!");
            return;
        }

        afficherMenuPrincipal();

        dbConfig.closeConnection();
        scanner.close();
    }

    private static void afficherMenuPrincipal() {
        while (true) {
            ConsoleUtils.clearScreen();
            System.out.println("\n╔════════════════════════════════════════════════════════╗");
            System.out.println("║        🍔  BRASIL BURGER - GESTION CATALOGUE  🍔       ║");
            System.out.println("╚════════════════════════════════════════════════════════╝");
            System.out.println("\n1. Gérer les Burgers");
            System.out.println("2. Gérer les Compléments");
            System.out.println("3. Gérer les Menus");
            System.out.println("0. Quitter");
            System.out.print("\nVotre choix : ");

            int choix = ConsoleUtils.lireEntier(scanner);

            switch (choix) {
                case 1:
                    burgerService.afficherMenu();
                    break;
                case 2:
                    complementService.afficherMenu();
                    break;
                case 3:
                    menuService.afficherMenu();
                    break;
                case 0:
                    System.out.println("\n🍔 À bientôt! 🍔\n");
                    return;
                default:
                    System.out.println("❌ Choix invalide!");
                    ConsoleUtils.pause();
            }
        }
    }
}