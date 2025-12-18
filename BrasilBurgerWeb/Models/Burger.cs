using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("burgers")]
    public class Burger
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("libelle")]
        [MaxLength(150)]
        public string Libelle { get; set; } = string.Empty;

        [Column("description")]
        public string? Description { get; set; }

        [Required]
        [Column("prix")]
        [DataType(DataType.Currency)]
        public decimal Prix { get; set; }

        [Column("imageurl")]
        [MaxLength(500)]
        public string? ImageUrl { get; set; }

        [Column("isarchived")]
        public bool IsArchived { get; set; } = false;

        [Column("categorieid")]
        public int? CategorieId { get; set; }

        [Column("createdat")]
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

        // Relations
        [ForeignKey("CategorieId")]
        public BurgerCategorie? Categorie { get; set; }
    }
}