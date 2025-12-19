using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("menus")]
    public class Menu
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("libelle")]
        [MaxLength(150)]
        public string Libelle { get; set; } = string.Empty;

        [Column("imageurl")]
        [MaxLength(500)]
        public string? ImageUrl { get; set; }

        [Column("isarchived")]
        public bool IsArchived { get; set; } = false;

        [Required]
        [Column("burgerid")]
        public int BurgerId { get; set; }

        [Required]
        [Column("boissonid")]
        public int BoissonId { get; set; }

        [Required]
        [Column("friteid")]
        public int FriteId { get; set; }

        [Column("createdat")]
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        // Relations (navigation properties)
        [ForeignKey("BurgerId")]
        public Burger? Burger { get; set; }

        [ForeignKey("BoissonId")]
        public Complement? Boisson { get; set; }

        [ForeignKey("FriteId")]
        public Complement? Frite { get; set; }

        // Propriété calculée pour le prix total
        [NotMapped]
        public decimal PrixTotal
        {
            get
            {
                decimal total = 0;
                if (Burger != null) total += Burger.Prix;
                if (Boisson != null) total += Boisson.Prix;
                if (Frite != null) total += Frite.Prix;
                return total;
            }
        }
    }
}