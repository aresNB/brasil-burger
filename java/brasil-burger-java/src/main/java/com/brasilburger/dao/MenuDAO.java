package com.brasilburger.dao;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.Menu;

import java.util.ArrayList;
import java.util.List;
import java.sql.*;

public class MenuDAO {

    private Connection connection;

    public MenuDAO() {
        this.connection = DatabaseConfig.getInstance().getConnection();
    }

    public void create(Menu menu) throws SQLException {
        String sql = "INSERT INTO menus (libelle, imageUrl, burgerId, boissonId, friteId) " +
                "VALUES (?, ?, ?, ?, ?)";

        try (PreparedStatement stmt = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setString(1, menu.getLibelle());
            stmt.setString(2, menu.getImageUrl());
            stmt.setInt(3, menu.getBurgerId());
            stmt.setInt(4, menu.getBoissonId());
            stmt.setInt(5, menu.getFriteId());

            int affectedRows = stmt.executeUpdate();

            if (affectedRows > 0) {
                try (ResultSet rs = stmt.getGeneratedKeys()) {
                    if (rs.next()) {
                        menu.setId(rs.getInt(1));
                    }
                }
            }
        }
    }

    public Menu findById(int id) throws SQLException {
        String sql = "SELECT m.*, " +
                "b.libelle as burger_nom, b.prix as burger_prix, " +
                "c1.libelle as boisson_nom, c1.prix as boisson_prix, " +
                "c2.libelle as frite_nom, c2.prix as frite_prix " +
                "FROM menus m " +
                "JOIN burgers b ON m.burgerId = b.id " +
                "JOIN complements c1 ON m.boissonId = c1.id " +
                "JOIN complements c2 ON m.friteId = c2.id " +
                "WHERE m.id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(sql)) {
            stmt.setInt(1, id);
            try (ResultSet rs = stmt.executeQuery()) {
                if (rs.next()) {
                    return mapResultSetToMenu(rs);
                }
            }
        }
        return null;
    }

    public List<Menu> findAll() throws SQLException {
        List<Menu> menus = new ArrayList<>();
        String sql = "SELECT m.*, " +
                "b.libelle as burger_nom, b.prix as burger_prix, " +
                "c1.libelle as boisson_nom, c1.prix as boisson_prix, " +
                "c2.libelle as frite_nom, c2.prix as frite_prix " +
                "FROM menus m " +
                "JOIN burgers b ON m.burgerId = b.id " +
                "JOIN complements c1 ON m.boissonId = c1.id " +
                "JOIN complements c2 ON m.friteId = c2.id " +
                "ORDER BY m.id DESC";

        try (Statement stmt = connection.createStatement();
                ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                menus.add(mapResultSetToMenu(rs));
            }
        }
        return menus;
    }

    public List<Menu> findAllActive() throws SQLException {
        List<Menu> menus = new ArrayList<>();
        String sql = "SELECT m.*, " +
                "b.libelle as burger_nom, b.prix as burger_prix, " +
                "c1.libelle as boisson_nom, c1.prix as boisson_prix, " +
                "c2.libelle as frite_nom, c2.prix as frite_prix " +
                "FROM menus m " +
                "JOIN burgers b ON m.burgerId = b.id " +
                "JOIN complements c1 ON m.boissonId = c1.id " +
                "JOIN complements c2 ON m.friteId = c2.id " +
                "WHERE m.isArchived = false " +
                "ORDER BY m.id DESC";

        try (Statement stmt = connection.createStatement();
                ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                menus.add(mapResultSetToMenu(rs));
            }
        }
        return menus;
    }

    public void update(Menu menu) throws SQLException {
        String sql = "UPDATE menus SET libelle = ?, imageUrl = ?, burgerId = ?, " +
                "boissonId = ?, friteId = ? WHERE id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(sql)) {
            stmt.setString(1, menu.getLibelle());
            stmt.setString(2, menu.getImageUrl());
            stmt.setInt(3, menu.getBurgerId());
            stmt.setInt(4, menu.getBoissonId());
            stmt.setInt(5, menu.getFriteId());
            stmt.setInt(6, menu.getId());

            stmt.executeUpdate();
        }
    }

    public void archive(int id) throws SQLException {
        String sql = "UPDATE menus SET isArchived = true WHERE id = ?";

        try (PreparedStatement stmt = connection.prepareStatement(sql)) {
            stmt.setInt(1, id);
            stmt.executeUpdate();
        }
    }

    private Menu mapResultSetToMenu(ResultSet rs) throws SQLException {
        Menu menu = new Menu();
        menu.setId(rs.getInt("id"));
        menu.setLibelle(rs.getString("libelle"));
        menu.setImageUrl(rs.getString("imageUrl"));
        menu.setArchived(rs.getBoolean("isArchived"));
        menu.setBurgerId(rs.getInt("burgerId"));
        menu.setBoissonId(rs.getInt("boissonId"));
        menu.setFriteId(rs.getInt("friteId"));
        menu.setCreatedAt(rs.getTimestamp("createdAt"));

        // Informations détaillées
        menu.setBurgerNom(rs.getString("burger_nom"));
        menu.setBurgerPrix(rs.getBigDecimal("burger_prix"));
        menu.setBoissonNom(rs.getString("boisson_nom"));
        menu.setBoissonPrix(rs.getBigDecimal("boisson_prix"));
        menu.setFriteNom(rs.getString("frite_nom"));
        menu.setFritePrix(rs.getBigDecimal("frite_prix"));

        return menu;
    }
}