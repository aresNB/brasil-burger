using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurgerWeb.Models
{
    [Table("users")]
    public class User
    {
        [Key]
        [Column("id")]
        public int Id { get; set; }

        [Required]
        [Column("nom")]
        [MaxLength(100)]
        public string Nom { get; set; } = string.Empty;

        [Required]
        [Column("prenom")]
        [MaxLength(100)]
        public string Prenom { get; set; } = string.Empty;

        [Required]
        [Column("tel")]
        [MaxLength(20)]
        public string Tel { get; set; } = string.Empty;

        [Required]
        [Column("email")]
        [MaxLength(150)]
        [EmailAddress]
        public string Email { get; set; } = string.Empty;

        [Required]
        [Column("password")]
        [MaxLength(255)]
        public string Password { get; set; } = string.Empty;

        [Column("adresse")]
        public string? Adresse { get; set; }

        [Required]
        [Column("role")]
        [MaxLength(20)]
        public string Role { get; set; } = "CLIENT";

        [Column("createdat")]
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    }
}