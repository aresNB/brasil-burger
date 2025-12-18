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

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            // Configuration du mapping (noms de tables en minuscules)
            modelBuilder.Entity<BurgerCategorie>()
                .ToTable("burger_categories");

            modelBuilder.Entity<Burger>()
                .ToTable("burgers");
        }
    }
}