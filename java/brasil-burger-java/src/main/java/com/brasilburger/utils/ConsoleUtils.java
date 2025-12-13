package com.brasilburger.utils;

import java.util.Scanner;

/**
 * Utilitaires pour l'interface console
 */
public class ConsoleUtils {

    public static final String SEPARATOR = "════════════════════════════════════════════════════════";
    public static final int CONSOLE_WIDTH = 60;

    /**
     * Effacer l'écran (simulation)
     */
    public static void clearScreen() {
        try {
            if (System.getProperty("os.name").contains("Windows")) {
                new ProcessBuilder("cmd", "/c", "cls").inheritIO().start().waitFor();
            } else {
                System.out.print("\033[H\033[2J");
                System.out.flush();
            }
        } catch (Exception e) {
            for (int i = 0; i < 50; i++) {
                System.out.println();
            }
        }
    }

    /**
     * Pause - Attendre une touche
     */
    public static void pause() {
        System.out.print("\n⏸️  Appuyez sur Entrée pour continuer...");
        try {
            System.in.read();
        } catch (Exception e) {
            // Ignorer
        }
    }

    /**
     * Centrer un texte
     */
    public static String centerText(String text) {
        int padding = (CONSOLE_WIDTH - text.length()) / 2;
        StringBuilder sb = new StringBuilder();
        for (int i = 0; i < padding; i++) {
            sb.append(" ");
        }
        sb.append(text);
        return sb.toString();
    }

    /**
     * Lire un entier avec gestion d'erreur
     */
    public static int lireEntier(Scanner scanner) {
        while (true) {
            try {
                String input = scanner.nextLine().trim();
                return Integer.parseInt(input);
            } catch (NumberFormatException e) {
                System.out.print("❌ Veuillez entrer un nombre valide : ");
            }
        }
    }

    /**
     * Afficher un message de succès
     */
    public static void afficherSucces(String message) {
        System.out.println("\n✅ " + message);
    }

    /**
     * Afficher un message d'erreur
     */
    public static void afficherErreur(String message) {
        System.out.println("\n❌ " + message);
    }

    /**
     * Afficher un message d'avertissement
     */
    public static void afficherAvertissement(String message) {
        System.out.println("\n⚠️  " + message);
    }

    /**
     * Afficher un message d'information
     */
    public static void afficherInfo(String message) {
        System.out.println("\nℹ️  " + message);
    }

    /**
     * Afficher un en-tête stylisé
     */
    public static void afficherEntete(String titre) {
        System.out.println("\n" + SEPARATOR);
        System.out.println(centerText(titre));
        System.out.println(SEPARATOR);
    }
}