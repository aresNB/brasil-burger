package com.brasilburger.dao;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Burger;
import java.util.ArrayList;
import java.util.List;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class BurgerDAO {

    private Connection connection;

    public BurgerDAO() {
        this.connection = DatabaseConfig.getInstance().getConnection();
    }

    /**
     * Créer un nouveau burger
     */
    public void create(Burger burger) throws SQLException {
        String sql = "INSERT INTO burgers (libelle, description, prix, imageUrl, categorieId) " +
                "VALUES (?, ?, ?, ?, ?)";

        try (PreparedStatement stmt = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setString(1, burger.getLibelle());
            stmt.setString(2, burger.getDescription());
            stmt.setBigDecimal(3, burger.getPrix());
            stmt.setString(4, burger.getImageUrl());
            if (burger.getCategorieId() != null) {
                stmt.setInt(5, burger.getCategorieId());
            } else {
                stmt.setNull(5, Types.INTEGER);
            }

            int affectedRows = stmt.executeUpdate();

            if (affectedRows > 0) {
                try (ResultSet rs = stmt.getGeneratedKeys()) {
                    if (rs.next()) {
                        burger.setId(rs.getInt(1));
                    }
                }
            }
        }
    }

    /**
     * Récupérer tous les burgers
     */
    public List<Burger> findAll() throws SQLException {
        List<Burger> burgers = new ArrayList<>();
        String sql = "SELECT b.*, bc.nom as categorie_nom " +
                "FROM burgers b " +
                "LEFT JOIN burger_categories bc ON b.categorieId = bc.id " +
                "ORDER BY b.id DESC";

        try (Statement stmt = connection.createStatement();
                ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                burgers.add(mapResultSetToBurger(rs));
            }
        }
        return burgers;
    }

    /**
     * Mapper un ResultSet vers un objet Burger
     */
    private Burger mapResultSetToBurger(ResultSet rs) throws SQLException {
        Burger burger = new Burger();
        burger.setId(rs.getInt("id"));
        burger.setLibelle(rs.getString("libelle"));
        burger.setDescription(rs.getString("description"));
        burger.setPrix(rs.getBigDecimal("prix"));
        burger.setImageUrl(rs.getString("imageUrl"));
        burger.setArchived(rs.getBoolean("isArchived"));
        burger.setCategorieId(rs.getInt("categorieId"));
        burger.setCategorieNom(rs.getString("categorie_nom"));
        burger.setCreatedAt(rs.getTimestamp("createdAt"));
        return burger;
    }
}