package com.brasilburger.dao;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Burger;

import java.sql.*;

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
}