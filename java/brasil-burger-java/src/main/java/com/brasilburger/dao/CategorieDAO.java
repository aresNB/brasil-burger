package com.brasilburger.dao;

import com.brasilburger.config.DatabaseConfig;
import com.brasilburger.models.BurgerCategorie;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class CategorieDAO {

    private Connection connection;

    public CategorieDAO() {
        this.connection = DatabaseConfig.getInstance().getConnection();
    }

    public List<BurgerCategorie> findAll() throws SQLException {
        List<BurgerCategorie> categories = new ArrayList<>();
        String sql = "SELECT * FROM burger_categories ORDER BY id";

        try (Statement stmt = connection.createStatement();
                ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                BurgerCategorie categorie = new BurgerCategorie();
                categorie.setId(rs.getInt("id"));
                categorie.setNom(rs.getString("nom"));
                categories.add(categorie);
            }
        }
        return categories;
    }
}