package com.brasilburger.dao;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Complement;

import java.sql.*;

public class ComplementDAO {

    private Connection connection;

    public ComplementDAO() {
        this.connection = DatabaseConfig.getInstance().getConnection();
    }

    /**
     * Créer un nouveau complément
     */
    public void create(Complement complement) throws SQLException {
        String sql = "INSERT INTO complements (libelle, prix, imageUrl, type) VALUES (?, ?, ?, ?)";

        try (PreparedStatement stmt = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setString(1, complement.getLibelle());
            stmt.setBigDecimal(2, complement.getPrix());
            stmt.setString(3, complement.getImageUrl());
            stmt.setString(4, complement.getType());

            int affectedRows = stmt.executeUpdate();

            if (affectedRows > 0) {
                try (ResultSet rs = stmt.getGeneratedKeys()) {
                    if (rs.next()) {
                        complement.setId(rs.getInt(1));
                    }
                }
            }
        }
    }
}