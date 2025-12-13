package com.brasilburger.config;

import java.io.FileInputStream;
import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Properties;

public class DatabaseConfig {

    private static DatabaseConfig instance;
    private Connection connection;
    private String url;
    private String username;
    private String password;

    /**
     * Constructeur privé (Singleton)
     * Charge la configuration depuis config.properties
     */
    private DatabaseConfig() {
        try {
            Properties props = new Properties();
            FileInputStream fis = new FileInputStream("config.properties");
            props.load(fis);

            this.url = props.getProperty("db.url");
            this.username = props.getProperty("db.username");
            this.password = props.getProperty("db.password");

            // Charger le driver PostgreSQL
            Class.forName(props.getProperty("db.driver"));

            fis.close();
        } catch (IOException | ClassNotFoundException e) {
            System.err.println("Erreur lors du chargement de la configuration: " + e.getMessage());
            e.printStackTrace();
        }
    }

    /**
     * Obtenir l'instance unique de DatabaseConfig
     */
    public static DatabaseConfig getInstance() {
        if (instance == null) {
            instance = new DatabaseConfig();
        }
        return instance;
    }

    /**
     * Obtenir une connexion à la base de données
     */
    public Connection getConnection() {
        try {
            if (connection == null || connection.isClosed()) {
                connection = DriverManager.getConnection(url, username, password);
                System.out.println("✅ Connexion à la base de données établie avec succès!");
            }
        } catch (SQLException e) {
            System.err.println("❌ Erreur de connexion à la base de données: " + e.getMessage());
            e.printStackTrace();
        }
        return connection;
    }

    /**
     * Fermer la connexion
     */
    public void closeConnection() {
        try {
            if (connection != null && !connection.isClosed()) {
                connection.close();
                System.out.println("Connexion fermée.");
            }
        } catch (SQLException e) {
            System.err.println("Erreur lors de la fermeture: " + e.getMessage());
        }
    }

    /**
     * Tester la connexion
     */
    public boolean testConnection() {
        try {
            Connection conn = getConnection();
            return conn != null && !conn.isClosed();
        } catch (SQLException e) {
            return false;
        }
    }
}
