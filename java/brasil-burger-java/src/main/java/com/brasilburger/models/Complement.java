package com.brasilburger.models;

import java.math.BigDecimal;
import java.sql.Timestamp;

public class Complement {

    private int id;
    private String libelle;
    private BigDecimal prix;
    private String imageUrl;
    private String type; // BOISSON ou FRITE
    private boolean isArchived;
    private Timestamp createdAt;

    public Complement() {
    }

    public Complement(String libelle, BigDecimal prix, String imageUrl, String type) {
        this.libelle = libelle;
        this.prix = prix;
        this.imageUrl = imageUrl;
        this.type = type;
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

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public boolean isArchived() {
        return isArchived;
    }

    public void setArchived(boolean archived) {
        isArchived = archived;
    }

    public Timestamp getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(Timestamp createdAt) {
        this.createdAt = createdAt;
    }

    @Override
    public String toString() {
        return String.format("Complément #%d - %s | Type: %s | Prix: %s FCFA | %s",
                id, libelle, type, prix, isArchived ? "ARCHIVÉ" : "ACTIF");
    }

    public String toDetailString() {
        StringBuilder sb = new StringBuilder();
        sb.append("\n╔════════════════════════════════════════════════════════╗\n");
        sb.append(String.format("  ID:          %d\n", id));
        sb.append(String.format("  Libellé:     %s\n", libelle));
        sb.append(String.format("  Type:        %s\n", type));
        sb.append(String.format("  Prix:        %s FCFA\n", prix));
        sb.append(String.format("  Image URL:   %s\n", imageUrl != null ? imageUrl : "N/A"));
        sb.append(String.format("  Statut:      %s\n", isArchived ? "ARCHIVÉ" : "ACTIF"));
        sb.append("╚════════════════════════════════════════════════════════╝\n");
        return sb.toString();
    }
}