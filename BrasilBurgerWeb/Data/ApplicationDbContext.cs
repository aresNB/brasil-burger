// using Microsoft.EntityFrameworkCore;
// using BrasilBurgerWeb.Models;

// namespace BrasilBurgerWeb.Data
// {
//     public class ApplicationDbContext : DbContext
//     {
//         public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
//             : base(options)
//         {
//         }

//         // DbSets
//         public DbSet<BurgerCategorie> BurgerCategories { get; set; }
//         public DbSet<Burger> Burgers { get; set; }

//         protected override void OnModelCreating(ModelBuilder modelBuilder)
//         {
//             base.OnModelCreating(modelBuilder);

//             // Configuration du mapping (noms de tables en minuscules)
//             modelBuilder.Entity<BurgerCategorie>()
//                 .ToTable("burger_categories");

//             modelBuilder.Entity<Burger>()
//                 .ToTable("burgers");
//         }
//     }
// }


using Microsoft.EntityFrameworkCore;
using BrasilBurgerWeb.Models;

namespace BrasilBurgerWeb.Data
{
    public class ApplicationDbContext : DbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
            : base(options)
        {
        }

        // DbSets
        public DbSet<BurgerCategorie> BurgerCategories { get; set; }
        public DbSet<Burger> Burgers { get; set; }
        public DbSet<Complement> Complements { get; set; }
        public DbSet<Menu> Menus { get; set; }
        public DbSet<User> Users { get; set; }
        public DbSet<Zone> Zones { get; set; }
        public DbSet<Quartier> Quartiers { get; set; }
        public DbSet<Commande> Commandes { get; set; }
        public DbSet<LigneCommande> LignesCommande { get; set; }
        public DbSet<Paiement> Paiements { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            // Configuration des noms de tables (en minuscules pour PostgreSQL)
            modelBuilder.Entity<BurgerCategorie>()
                .ToTable("burger_categories");

            modelBuilder.Entity<Burger>()
                .ToTable("burgers");

            modelBuilder.Entity<Complement>()
                .ToTable("complements");

            modelBuilder.Entity<Menu>()
                .ToTable("menus");

            // Configuration des relations Menu
            modelBuilder.Entity<Menu>()
                .HasOne(m => m.Burger)
                .WithMany()
                .HasForeignKey(m => m.BurgerId)
                .OnDelete(DeleteBehavior.Restrict);

            modelBuilder.Entity<Menu>()
                .HasOne(m => m.Boisson)
                .WithMany()
                .HasForeignKey(m => m.BoissonId)
                .OnDelete(DeleteBehavior.Restrict);

            modelBuilder.Entity<Menu>()
                .HasOne(m => m.Frite)
                .WithMany()
                .HasForeignKey(m => m.FriteId)
                .OnDelete(DeleteBehavior.Restrict);

            modelBuilder.Entity<User>()
                .ToTable("users");

            modelBuilder.Entity<User>()
                .HasIndex(u => u.Email)
                .IsUnique();

            modelBuilder.Entity<User>()
                .HasIndex(u => u.Tel)
                .IsUnique();

            // Configuration des tables
            modelBuilder.Entity<Zone>().ToTable("zones");
            modelBuilder.Entity<Quartier>().ToTable("quartiers");
            modelBuilder.Entity<Commande>().ToTable("commandes");
            modelBuilder.Entity<LigneCommande>().ToTable("lignes_commande");

            // Relations Zone-Quartier
            modelBuilder.Entity<Quartier>()
                .HasOne(q => q.Zone)
                .WithMany(z => z.Quartiers)
                .HasForeignKey(q => q.ZoneId)
                .OnDelete(DeleteBehavior.Cascade);

            // Relations Commande
            modelBuilder.Entity<Commande>()
                .HasOne(c => c.Client)
                .WithMany()
                .HasForeignKey(c => c.ClientId)
                .OnDelete(DeleteBehavior.Restrict);

            modelBuilder.Entity<Commande>()
                .HasOne(c => c.Zone)
                .WithMany()
                .HasForeignKey(c => c.ZoneId)
                .OnDelete(DeleteBehavior.SetNull);

            // Relations LigneCommande
            modelBuilder.Entity<LigneCommande>()
                .HasOne(lc => lc.Commande)
                .WithMany(c => c.LignesCommande)
                .HasForeignKey(lc => lc.CommandeId)
                .OnDelete(DeleteBehavior.Cascade);

            // Configuration table Paiement
            modelBuilder.Entity<Paiement>().ToTable("paiements");

            // Index unique sur commandeId (une commande = un seul paiement)
            modelBuilder.Entity<Paiement>()
                .HasIndex(p => p.CommandeId)
                .IsUnique();

            // Index unique sur refTransaction
            modelBuilder.Entity<Paiement>()
                .HasIndex(p => p.RefTransaction)
                .IsUnique();

            // Relation Paiement-Commande
            modelBuilder.Entity<Paiement>()
                .HasOne(p => p.Commande)
                .WithMany()
                .HasForeignKey(p => p.CommandeId)
                .OnDelete(DeleteBehavior.Restrict);
        }
    }
}