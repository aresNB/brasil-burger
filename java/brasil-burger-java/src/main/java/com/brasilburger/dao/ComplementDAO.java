package com.brasilburger.dao;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Complement;
import java.util.ArrayList;
import java.util.List;

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

    /**
     * Récupérer tous les compléments
     */
    public List<Complement> findAll() throws SQLException {
        List<Complement> complements = new ArrayList<>();
        String sql = "SELECT * FROM complements ORDER BY type, id DESC";

        try (Statement stmt = connection.createStatement();
                ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                complements.add(mapResultSetToComplement(rs));
            }
        }
        return complements;
    }

    /**
     * Récupérer les compléments actifs
     */
    public List<Complement> findAllActive() throws SQLException {
        List<Complement> complements = new ArrayList<>();
        String sql = "SELECT * FROM complements WHERE isArchived = false ORDER BY type, id DESC";

        try (Statement stmt = connection.createStatement();
                ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                complements.add(mapResultSetToComplement(rs));
            }
        }
        return complements;
    }

    /**
     * Récupérer un complément par ID
     */
    public Complement findById(int id) throws SQLException {
        String sql = "SELECT * FROM complements WHERE id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(sql)) {
            stmt.setInt(1, id);
            try (ResultSet rs = stmt.executeQuery()) {
                if (rs.next()) {
                    return mapResultSetToComplement(rs);
                }
            }
        }
        return null;
    }

    /**
     * Mettre à jour un complément
     */
    public void update(Complement complement) throws SQLException {
        String sql = "UPDATE complements SET libelle = ?, prix = ?, imageUrl = ?, type = ? WHERE id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(sql)) {
            stmt.setString(1, complement.getLibelle());
            stmt.setBigDecimal(2, complement.getPrix());
            stmt.setString(3, complement.getImageUrl());
            stmt.setString(4, complement.getType());
            stmt.setInt(5, complement.getId());

            stmt.executeUpdate();
        }
    }

    /**
     * Archiver un complément
     */
    public void archive(int id) throws SQLException {
        String sql = "UPDATE complements SET isArchived = true WHERE id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(sql)) {
            stmt.setInt(1, id);
            stmt.executeUpdate();
        }
    }

    /**
     * Récupérer les compléments par type (BOISSON ou FRITE)
     */
    public List<Complement> findByType(String type) throws SQLException {
        List<Complement> complements = new ArrayList<>();
        String sql = "SELECT * FROM complements WHERE type = ? AND isArchived = false ORDER BY id DESC";

        try (PreparedStatement stmt = connection.prepareStatement(sql)) {
            stmt.setString(1, type);
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    complements.add(mapResultSetToComplement(rs));
                }
            }
        }
        return complements;
    }

    /**
     * Mapper un ResultSet vers un objet Complement
     */
    private Complement mapResultSetToComplement(ResultSet rs) throws SQLException {
        Complement complement = new Complement();
        complement.setId(rs.getInt("id"));
        complement.setLibelle(rs.getString("libelle"));
        complement.setPrix(rs.getBigDecimal("prix"));
        complement.setImageUrl(rs.getString("imageUrl"));
        complement.setType(rs.getString("type"));
        complement.setArchived(rs.getBoolean("isArchived"));
        complement.setCreatedAt(rs.getTimestamp("createdAt"));
        return complement;
    }
}