package com.brasilburger.models;

/**
 * Modèle représentant une catégorie de burger
 */
public class BurgerCategorie {

    private int id;
    private String nom;

    // Constructeurs
    public BurgerCategorie() {
    }

    public BurgerCategorie(String nom) {
        this.nom = nom;
    }

    public BurgerCategorie(int id, String nom) {
        this.id = id;
        this.nom = nom;
    }

    // Getters et Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    @Override
    public String toString() {
        return String.format("%d. %s", id, nom);
    }
}