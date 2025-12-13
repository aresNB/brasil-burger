package com.brasilburger.models;

import java.math.BigDecimal;
import java.sql.Timestamp;

public class Menu {

    private int id;
    private String libelle;
    private String imageUrl;
    private boolean isArchived;
    private int burgerId;
    private int boissonId;
    private int friteId;
    private Timestamp createdAt;

    // Informations détaillées (pour affichage)
    private String burgerNom;
    private String boissonNom;
    private String friteNom;
    private BigDecimal burgerPrix;
    private BigDecimal boissonPrix;
    private BigDecimal fritePrix;
    private BigDecimal prixTotal;

    public Menu() {
    }

    public Menu(String libelle, String imageUrl, int burgerId, int boissonId, int friteId) {
        this.libelle = libelle;
        this.imageUrl = imageUrl;
        this.burgerId = burgerId;
        this.boissonId = boissonId;
        this.friteId = friteId;
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

    public int getBurgerId() {
        return burgerId;
    }

    public void setBurgerId(int burgerId) {
        this.burgerId = burgerId;
    }

    public int getBoissonId() {
        return boissonId;
    }

    public void setBoissonId(int boissonId) {
        this.boissonId = boissonId;
    }

    public int getFriteId() {
        return friteId;
    }

    public void setFriteId(int friteId) {
        this.friteId = friteId;
    }

    public Timestamp getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(Timestamp createdAt) {
        this.createdAt = createdAt;
    }

    public String getBurgerNom() {
        return burgerNom;
    }

    public void setBurgerNom(String burgerNom) {
        this.burgerNom = burgerNom;
    }

    public String getBoissonNom() {
        return boissonNom;
    }

    public void setBoissonNom(String boissonNom) {
        this.boissonNom = boissonNom;
    }

    public String getFriteNom() {
        return friteNom;
    }

    public void setFriteNom(String friteNom) {
        this.friteNom = friteNom;
    }

    public BigDecimal getBurgerPrix() {
        return burgerPrix;
    }

    public void setBurgerPrix(BigDecimal burgerPrix) {
        this.burgerPrix = burgerPrix;
    }

    public BigDecimal getBoissonPrix() {
        return boissonPrix;
    }

    public void setBoissonPrix(BigDecimal boissonPrix) {
        this.boissonPrix = boissonPrix;
    }

    public BigDecimal getFritePrix() {
        return fritePrix;
    }

    public void setFritePrix(BigDecimal fritePrix) {
        this.fritePrix = fritePrix;
    }

    public BigDecimal getPrixTotal() {
        if (prixTotal == null && burgerPrix != null && boissonPrix != null && fritePrix != null) {
            prixTotal = burgerPrix.add(boissonPrix).add(fritePrix);
        }
        return prixTotal;
    }

    public void setPrixTotal(BigDecimal prixTotal) {
        this.prixTotal = prixTotal;
    }

    @Override
    public String toString() {
        return String.format("Menu #%d - %s | Prix Total: %s FCFA | %s",
                id, libelle, getPrixTotal(), isArchived ? "ARCHIVÉ" : "ACTIF");
    }

    public String toDetailString() {
        StringBuilder sb = new StringBuilder();
        sb.append("\n╔════════════════════════════════════════════════════════╗\n");
        sb.append(String.format("  ID:          %d\n", id));
        sb.append(String.format("  Libellé:     %s\n", libelle));
        sb.append("\n  Composition:\n");
        sb.append(String.format("    🍔 Burger:   %s (%s FCFA)\n", burgerNom, burgerPrix));
        sb.append(String.format("    🥤 Boisson:  %s (%s FCFA)\n", boissonNom, boissonPrix));
        sb.append(String.format("    🍟 Frites:   %s (%s FCFA)\n", friteNom, fritePrix));
        sb.append(String.format("\n  Prix Total:  %s FCFA\n", getPrixTotal()));
        sb.append(String.format("  Image URL:   %s\n", imageUrl != null ? imageUrl : "N/A"));
        sb.append(String.format("  Statut:      %s\n", isArchived ? "ARCHIVÉ" : "ACTIF"));
        sb.append("╚════════════════════════════════════════════════════════╝\n");
        return sb.toString();
    }
}