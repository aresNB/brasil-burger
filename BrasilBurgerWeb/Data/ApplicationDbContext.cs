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
        }
    }
}