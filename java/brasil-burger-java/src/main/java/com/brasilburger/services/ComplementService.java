package com.brasilburger.services;

import com.brasilburger.dao.ComplementDAO;
import com.brasilburger.models.Complement;
import com.brasilburger.utils.ConsoleUtils;

import java.math.BigDecimal;
import java.sql.SQLException;
import java.util.Scanner;
import java.util.List;

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
            System.out.println("2. Lister tous les compléments");
            System.out.println("3. Modifier un complément");
            System.out.println("4. Archiver un complément");
            System.out.println("5. Filtrer par type");
            System.out.println("0. Retour au menu principal");
            System.out.print("\nVotre choix : ");

            int choix = ConsoleUtils.lireEntier(scanner);

            switch (choix) {
                case 1:
                    creerComplement();
                    break;
                case 2:
                    listerComplements();
                    break;
                case 3:
                    modifierComplement();
                    break;
                case 4:
                    archiverComplement();
                    break;
                case 5:
                    filtrerParType();
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

    /**
     * Lister tous les compléments
     */
    private void listerComplements() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("LISTE DES COMPLÉMENTS"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Complement> complements = complementDAO.findAll();

            if (complements.isEmpty()) {
                System.out.println("\n📭 Aucun complément trouvé.");
            } else {
                System.out.println("\n📋 Total : " + complements.size() + " complément(s)\n");

                // Séparer par type
                List<Complement> boissons = complements.stream()
                        .filter(c -> c.getType().equals("BOISSON") && !c.isArchived())
                        .toList();
                List<Complement> frites = complements.stream()
                        .filter(c -> c.getType().equals("FRITE") && !c.isArchived())
                        .toList();
                List<Complement> archives = complements.stream()
                        .filter(Complement::isArchived)
                        .toList();

                if (!boissons.isEmpty()) {
                    System.out.println("🥤 BOISSONS (" + boissons.size() + "):");
                    for (Complement c : boissons) {
                        System.out.println("  " + c);
                    }
                }

                if (!frites.isEmpty()) {
                    System.out.println("\n🍟 FRITES (" + frites.size() + "):");
                    for (Complement c : frites) {
                        System.out.println("  " + c);
                    }
                }

                if (!archives.isEmpty()) {
                    System.out.println("\n📦 ARCHIVÉS (" + archives.size() + "):");
                    for (Complement c : archives) {
                        System.out.println("  " + c);
                    }
                }
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la récupération: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }

    /**
     * Modifier un complément
     */
    private void modifierComplement() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("MODIFIER UN COMPLÉMENT"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Complement> complements = complementDAO.findAllActive();
            if (complements.isEmpty()) {
                System.out.println("\n📭 Aucun complément actif trouvé.");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📋 Compléments disponibles :");
            for (Complement c : complements) {
                System.out.println("  " + c);
            }

            System.out.print("\nID du complément à modifier : ");
            int id = ConsoleUtils.lireEntier(scanner);

            Complement complement = complementDAO.findById(id);
            if (complement == null) {
                System.out.println("❌ Complément introuvable!");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📝 Complément actuel :");
            System.out.println(complement.toDetailString());

            scanner.nextLine(); // Clear buffer

            // Modification
            System.out.print("\nNouveau libellé (ou Entrée pour garder) : ");
            String libelle = scanner.nextLine().trim();
            if (!libelle.isEmpty()) {
                complement.setLibelle(libelle);
            }

            System.out.print("Nouveau prix (ou 0 pour garder) : ");
            String prixStr = scanner.nextLine().trim();
            if (!prixStr.isEmpty() && !prixStr.equals("0")) {
                complement.setPrix(new BigDecimal(prixStr));
            }

            System.out.print("Nouvelle URL image (ou Entrée pour garder) : ");
            String imageUrl = scanner.nextLine().trim();
            if (!imageUrl.isEmpty()) {
                complement.setImageUrl(imageUrl);
            }

            System.out.print("Changer le type ? (O/N) : ");
            String changeType = scanner.next().trim().toUpperCase();
            scanner.nextLine(); // Clear buffer

            if (changeType.equals("O")) {
                System.out.println("  1. BOISSON");
                System.out.println("  2. FRITE");
                System.out.print("Nouveau type : ");
                int typeChoix = ConsoleUtils.lireEntier(scanner);
                complement.setType((typeChoix == 1) ? "BOISSON" : "FRITE");
            }

            complementDAO.update(complement);
            System.out.println("\n✅ Complément modifié avec succès!");

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la modification: " + e.getMessage());
        } catch (NumberFormatException e) {
            System.out.println("❌ Format de nombre invalide!");
        }

        ConsoleUtils.pause();
    }

    /**
     * Archiver un complément
     */
    private void archiverComplement() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("ARCHIVER UN COMPLÉMENT"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            List<Complement> complements = complementDAO.findAllActive();
            if (complements.isEmpty()) {
                System.out.println("\n📭 Aucun complément actif trouvé.");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n📋 Compléments actifs :");
            for (Complement c : complements) {
                System.out.println("  " + c);
            }

            System.out.print("\nID du complément à archiver : ");
            int id = ConsoleUtils.lireEntier(scanner);

            Complement complement = complementDAO.findById(id);
            if (complement == null) {
                System.out.println("❌ Complément introuvable!");
                ConsoleUtils.pause();
                return;
            }

            if (complement.isArchived()) {
                System.out.println("⚠️  Ce complément est déjà archivé!");
                ConsoleUtils.pause();
                return;
            }

            System.out.println("\n⚠️  Voulez-vous vraiment archiver ce complément ?");
            System.out.println(complement.toDetailString());
            System.out.print("Confirmer (O/N) : ");

            String confirmation = scanner.next().trim().toUpperCase();
            scanner.nextLine(); // Vider le buffer

            if (confirmation.equals("O") || confirmation.equals("OUI")) {
                complementDAO.archive(id);
                System.out.println("\n✅ Complément archivé avec succès!");
            } else {
                System.out.println("\n❌ Opération annulée.");
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de l'archivage: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }

    /**
     * Filtrer par type
     */
    private void filtrerParType() {
        ConsoleUtils.clearScreen();
        System.out.println("\n" + ConsoleUtils.SEPARATOR);
        System.out.println(ConsoleUtils.centerText("FILTRER PAR TYPE"));
        System.out.println(ConsoleUtils.SEPARATOR);

        try {
            System.out.println("\n📂 Type de complément :");
            System.out.println("  1. BOISSON");
            System.out.println("  2. FRITE");
            System.out.print("Votre choix : ");
            int typeChoix = ConsoleUtils.lireEntier(scanner);

            String type = (typeChoix == 1) ? "BOISSON" : "FRITE";
            List<Complement> complements = complementDAO.findByType(type);

            if (complements.isEmpty()) {
                System.out.println("\n📭 Aucun complément de type " + type + " trouvé.");
            } else {
                System.out.println("\n✅ " + complements.size() + " complément(s) de type " + type + " :\n");
                for (Complement c : complements) {
                    System.out.println(c.toDetailString());
                }
            }

        } catch (SQLException e) {
            System.out.println("❌ Erreur lors de la recherche: " + e.getMessage());
        }

        ConsoleUtils.pause();
    }
}