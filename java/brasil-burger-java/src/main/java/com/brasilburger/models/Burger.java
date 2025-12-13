package com.brasilburger.models;

import java.math.BigDecimal;
import java.sql.Timestamp;

public class Burger {

    private int id;
    private String libelle;
    private String description;
    private BigDecimal prix;
    private String imageUrl;
    private boolean isArchived;
    private Integer categorieId;
    private String categorieNom;
    private Timestamp createdAt;

    public Burger() {
    }

    public Burger(String libelle, String description, BigDecimal prix, String imageUrl, Integer categorieId) {
        this.libelle = libelle;
        this.description = description;
        this.prix = prix;
        this.imageUrl = imageUrl;
        this.categorieId = categorieId;
        this.isArchived = false;
    }

    // Getters et Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getLibelle() {
        return libelle;
    }

    public void setLibelle(String libelle) {
        this.libelle = libelle;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public BigDecimal getPrix() {
        return prix;
    }

    public void setPrix(BigDecimal prix) {
        this.prix = prix;
    }

    public String getImageUrl() {
        return imageUrl;
    }

    public void setImageUrl(String imageUrl) {
        this.imageUrl = imageUrl;
    }

    public boolean isArchived() {
        return isArchived;
    }

    public void setArchived(boolean archived) {
        isArchived = archived;
    }

    public Integer getCategorieId() {
        return categorieId;
    }

    public void setCategorieId(Integer categorieId) {
        this.categorieId = categorieId;
    }

    public String getCategorieNom() {
        return categorieNom;
    }

    public void setCategorieNom(String categorieNom) {
        this.categorieNom = categorieNom;
    }

    public Timestamp getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(Timestamp createdAt) {
        this.createdAt = createdAt;
    }

    @Override
    public String toString() {
        return String.format("Burger #%d - %s | Prix: %s FCFA | Catégorie: %s | %s",
                id, libelle, prix,
                categorieNom != null ? categorieNom : "N/A",
                isArchived ? "ARCHIVÉ" : "ACTIF");
    }

    public String toDetailString() {
        StringBuilder sb = new StringBuilder();
        sb.append("\n╔════════════════════════════════════════════════════════╗\n");
        sb.append(String.format("  ID:          %d\n", id));
        sb.append(String.format("  Libellé:     %s\n", libelle));
        sb.append(String.format("  Description: %s\n", description != null ? description : "N/A"));
        sb.append(String.format("  Prix:        %s FCFA\n", prix));
        sb.append(String.format("  Catégorie:   %s\n", categorieNom != null ? categorieNom : "N/A"));
        sb.append(String.format("  Image URL:   %s\n", imageUrl != null ? imageUrl : "N/A"));
        sb.append(String.format("  Statut:      %s\n", isArchived ? "ARCHIVÉ" : "ACTIF"));
        sb.append("╚════════════════════════════════════════════════════════╝\n");
        return sb.toString();
    }
}