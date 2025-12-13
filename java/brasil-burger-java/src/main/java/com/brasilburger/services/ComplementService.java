package com.brasilburger.services;

import com.brasilburger.dao.ComplementDAO;
import com.brasilburger.models.Complement;
import com.brasilburger.utils.ConsoleUtils;

import java.math.BigDecimal;
import java.sql.SQLException;
import java.util.Scanner;

public class ComplementService {

    private ComplementDAO complementDAO;
    private Scanner scanner;

    public ComplementService() {
        this.complementDAO = new ComplementDAO();
        this.scanner = new Scanner(System.in);
    }

    public void afficherMenu() {
        while (true) {
            ConsoleUtils.clearScreen();
            System.out.println("\n" + ConsoleUtils.SEPARATOR);
            System.out.println(ConsoleUtils.centerText("GESTION DES COMPLÉMENTS"));
            System.out.println(ConsoleUtils.SEPARATOR);
            System.out.println("\n1. Créer un complément");
            System.out.println("0. Retour au menu principal");
            System.out.print("\nVotre choix : ");

            int choix = ConsoleUtils.lireEntier(scanner);

            switch (choix) {
                case 1:
                    creerComplement();
                    break;
                case 0:
                    return;
                default:
                    System.out.println("❌ Choix invalide!");
                    ConsoleUtils.pause();
            }
        }
    }

    private void creerComplement() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("CRÉER UN COMPLÉMENT"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            scanner.nextLine(); // Clear buffer

            System.out.print("\n📝 Libellé du complément : ");
            String libelle = scanner.nextLine().trim();

            if (libelle.isEmpty()) {
                System.out.println("❌ Le libellé ne peut pas être vide!");
                ConsoleUtils.pause();
                return;
            }

            System.out.print("💰 Prix (FCFA) : ");
            BigDecimal prix = new BigDecimal(scanner.nextLine().trim());

            if (prix.compareTo(BigDecimal.ZERO) < 0) {
                System.out.println("❌ Le prix ne peut pas être négatif!");
                ConsoleUtils.pause();
                return;
            }

            System.out.print("🖼️  URL de l'image : ");
            String imageUrl = scanner.nextLine().trim();

            System.out.println("\n📂 Type de complément :");
            System.out.println("  1. BOISSON");
            System.out.println("  2. FRITE");
            System.out.print("Votre choix : ");
            int typeChoix = ConsoleUtils.lireEntier(scanner);

            String type = (typeChoix == 1) ? "BOISSON" : "FRITE";

            Complement complement = new Complement(libelle, prix, imageUrl, type);
            complementDAO.create(complement);

            System.out.println("\n✅ Complément créé avec succès!");
            System.out.println(complement.toDetailString());

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la création: " + e.getMessage());
        } catch (NumberFormatException e) {
            System.out.println("❌ Format de nombre invalide!");
        }

        ConsoleUtils.pause();
    }
}