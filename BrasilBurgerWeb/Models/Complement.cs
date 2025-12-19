using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("complements")]
    public class Complement
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("libelle")]
        [MaxLength(100)]
        public string Libelle { get; set; } = string.Empty;

        [Required]
        [Column("prix")]
        [DataType(DataType.Currency)]
        public decimal Prix { get; set; }

        [Column("imageurl")]
        [MaxLength(500)]
        public string? ImageUrl { get; set; }

        [Required]
        [Column("type")]
        [MaxLength(20)]
        public string Type { get; set; } = string.Empty; // BOISSON ou FRITE

        [Column("isarchived")]
        public bool IsArchived { get; set; } = false;

        [Column("createdat")]
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    }
}